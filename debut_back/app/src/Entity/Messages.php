<?php

namespace App\Entity;

use App\Repository\MessagesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessagesRepository::class)]
class Messages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $id_expediteur = null;

    #[ORM\Column(length: 500)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_envoi = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Users $users = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdExpediteur(): ?int
    {
        return $this->id_expediteur;
    }

    public function setIdExpediteur(int $id_expediteur): static
    {
        $this->id_expediteur = $id_expediteur;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeImmutable
    {
        return $this->date_envoi;
    }

    public function setDateEnvoi(\DateTimeImmutable $date_envoi): static
    {
        $this->date_envoi = $date_envoi;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): static
    {
        $this->users = $users;

        return $this;
    }
}
