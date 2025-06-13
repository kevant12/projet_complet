<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mime\MimeTypes;

class FileUploader
{
    private $targetDirectory;
    private $slugger;
    private $allowedMimeTypes;
    private $maxFileSize;

    public function __construct(string $targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->allowedMimeTypes = [
            'image' => ['image/jpeg', 'image/png', 'image/gif'],
            'video' => ['video/mp4', 'video/quicktime']
        ];
        $this->maxFileSize = [
            'image' => 5 * 1024 * 1024, // 5MB
            'video' => 50 * 1024 * 1024 // 50MB
        ];
    }

    public function upload(UploadedFile $file, string $type = 'image', ?string $targetDirectory = null): string
    {
        $directory = $targetDirectory ?? $this->targetDirectory;
        // Vérification du type MIME
        $mimeTypes = new MimeTypes();
        $mimeType = $mimeTypes->guessMimeType($file->getPathname());
        
        if (!in_array($mimeType, $this->allowedMimeTypes[$type])) {
            throw new FileException('Type de fichier non autorisé.');
        }

        // Vérification de la taille
        if ($file->getSize() > $this->maxFileSize[$type]) {
            throw new FileException('Le fichier est trop volumineux.');
        }

        // Vérification du contenu du fichier
        if (!$this->isValidFileContent($file, $type)) {
            throw new FileException('Le contenu du fichier est invalide.');
        }

        // Génération d'un nom de fichier sécurisé
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            // Vérification et création du répertoire si nécessaire
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Déplacement du fichier
            $file->move($directory, $fileName);

            // Vérification finale du fichier
            if (!file_exists($directory.'/'.$fileName)) {
                throw new FileException('Erreur lors de l\'enregistrement du fichier.');
            }

            return $fileName;
        } catch (FileException $e) {
            throw new FileException('Une erreur est survenue lors du téléchargement du fichier.');
        }
    }

    private function isValidFileContent(UploadedFile $file, string $type): bool
    {
        $content = file_get_contents($file->getPathname());
        
        // Vérification des signatures de fichiers
        if ($type === 'image') {
            // Vérification des signatures d'images
            $signatures = [
                'jpeg' => "\xFF\xD8\xFF",
                'png' => "\x89PNG\r\n\x1a\n",
                'gif' => "GIF87a"
            ];
            
            foreach ($signatures as $format => $signature) {
                if (strpos($content, $signature) === 0) {
                    return true;
                }
            }
            return false;
        }
        
        if ($type === 'video') {
            // Vérification des signatures de vidéos
            $signatures = [
                'mp4' => "\x00\x00\x00\x18ftypmp42",
                'mov' => "\x00\x00\x00\x14ftypqt"
            ];
            
            foreach ($signatures as $format => $signature) {
                if (strpos($content, $signature) === 0) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
} 