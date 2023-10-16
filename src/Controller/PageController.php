<?php

namespace App\Controller;

use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_homepage')]
    public function homepage(): Response
    {
        // Fetch categories from the database using the injected EntityManager
        $categories = $this->entityManager->getRepository(Categories::class)->findAll();

        // Render the template with categories
        return $this->render('page/homepage.html.twig', [
            'categories' => $categories,
        ]);
    }
}