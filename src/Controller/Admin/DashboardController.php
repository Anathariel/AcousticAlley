<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use App\Entity\Products;
use App\Form\CategoriesFormType;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin', name: 'admin')]
    public function dashboard(): Response
    {
       // Fetch categories and products from the database using the injected EntityManager
        $categories = $this->entityManager->getRepository(Categories::class)->findAll();
        $products = $this->entityManager->getRepository(Products::class)->findAll();

        // Render the template with categories and products
        return $this->render('Admin/dashboard.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    #[Route('/admin/create', name: 'create')]
    public function create(Request $request): Response {

        $category = new Categories();
        $categoryForm = $this->createForm(CategoriesFormType::class, $category);

        $product = new Products();
        $productForm = $this->createForm(ProductsFormType::class, $product);

        $categoryForm->handleRequest($request);
        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $newCategory = $categoryForm->getData();
            $thumbnail = $categoryForm->get('thumbnail')->getData();
            if($thumbnail) {
                $newFileName = uniqid() . '.' . $thumbnail->guessExtension();

                try{
                    $thumbnail->move(
                        $this->getParameter('kernel.project_dir') . '/public/asset/media/categories',
                        $newFileName
                    );
                } catch(FileException $e) {
                    return new Response($e->getMessage());
                }

                $newCategory->setThumbnail('/categories/' . $newFileName);
            }

            $this->entityManager->persist($newCategory);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin');
        }

        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $newProduct = $productForm->getData();
            $thumbnail = $productForm->get('thumbnail')->getData();
            if($thumbnail) {
                $newFileName = uniqid() . '.' . $thumbnail->guessExtension();

                try{
                    $thumbnail->move(
                        $this->getParameter('kernel.project_dir') . '/public/asset/media/products',
                        $newFileName
                    );
                } catch(FileException $e) {
                    return new Response($e->getMessage());
                }

                $newProduct->setThumbnail('/products/' . $newFileName);
            }
            $this->entityManager->persist($newProduct);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin');
        }

        return $this->render('Admin/create.html.twig', [
            'categoryForm' => $categoryForm->createView(),
            'productForm' => $productForm->createView(),
        ]);
    }

    #[Route('/admin/edit/{id}', name: 'edit')]
    public function edit($id): Response
    {
        // Load the entity based on the provided ID
        $category = $this->entityManager->getRepository(Categories::class)->find($id);
        $categoryForm = $this->createForm(CategoriesFormType::class, $category);
        // Handle form submission and other logic...

        return $this->render('Admin/edit.html.twig', [
            'category' => $category,
            'categoryForm' => $categoryForm,
        ]);
    }
}