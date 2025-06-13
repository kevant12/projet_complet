<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ArticleForm;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductsRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ArticleController extends AbstractController
{
    #[Route('/products', name: 'app_product')]
    public function index(ProductsRepository $productsRepository): Response
    {
        $products = $productsRepository->findAll();

        return $this->render('product/index.html.twig', compact('products'));
    }

    #[Route('/add', name: 'app_product_add')]
    #[IsGranted('ROLE_USER')]
    public function add(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $products = new Products();
        $formAdd = $this->createForm(ArticleForm::class, $products);
        $formAdd->handleRequest($request);

        if($formAdd->isSubmitted() && $formAdd->isValid()) {
            // Gestion de l'image (obligatoire)
            $imageFile = $formAdd->get('image')->getData();
            if (!$imageFile) {
                $this->addFlash('error', 'Une image est obligatoire pour l\'article.');
                return $this->render('product/add.html.twig', compact('formAdd'));
            }
            $imageFileName = $fileUploader->upload(
                $imageFile,
                'image',
                $this->getParameter('kernel.project_dir') . $this->getParameter('app.uploads.images_directory')
            );
            $products->setImage($imageFileName);

            // Gestion de la vidéo (optionnelle)
            $videoFile = $formAdd->get('video')->getData();
            if ($videoFile) {
                $videoFileName = $fileUploader->upload(
                    $videoFile,
                    'video',
                    $this->getParameter('kernel.project_dir') . $this->getParameter('app.uploads.videos_directory')
                );
                $products->setVideo($videoFileName);
            }

            $entityManager->persist($products);
            $entityManager->flush();

            $this->addFlash('success', 'Article ajouté avec succès !');
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/add.html.twig', compact('formAdd'));
    }
}
