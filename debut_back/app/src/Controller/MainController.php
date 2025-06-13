<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;

final class MainController extends AbstractController
{
    #[Route('/accueil', name: 'app_accueil')]
    public function index(EntityManagerInterface $entityManager, ProductsRepository $productsRepository, UsersRepository $usersRepository): Response
    {
        $products = $productsRepository->findBy([], ['created_at' => 'DESC'] );
        dd($products);
        $users = $usersRepository->find;

        return $this->render('accueil/index.html.twig', compact('products', 'users'));
    }
}
