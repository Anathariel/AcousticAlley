<?php

namespace App\Controller\Admin;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Categories;
use App\Entity\Products;
use App\Form\CategoriesFormType;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
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

    // DEFAULT ADMIN DASHBOARD - READ
    #[Route('/admin', name: 'admin_dashboard')]
    public function admin_dashboard(): Response
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

    // DEFAULT USER DASHBOARD - READ
    #[Route('/dashboard', name: 'user_dashboard')]
    public function user_dashboard(): Response
    {
        /** @var UserInterface $user */
        $user = $this->getUser();

        // Check if the user is logged in
        if ($user instanceof UserInterface) {
            $firstName = $user->getFirstname();
        } else {
            // Handle the case when the user is not logged in
            $firstName = null;
        }

        // Render the template
        return $this->render('Users/dashboard.html.twig', [
            'firstName' => $firstName,
        ]);
    }


    // ----------- ADMIN SIDE CRUD CONTROLS ----------- //

    // CREATE
    #[Route('/admin/create', name: 'create')]
    public function create(Request $request): Response
    {

        $category = new Categories();
        $categoryForm = $this->createForm(CategoriesFormType::class, $category);

        $product = new Products();
        $productForm = $this->createForm(ProductsFormType::class, $product);

        $categoryForm->handleRequest($request);
        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $newCategory = $categoryForm->getData();
            $thumbnail = $categoryForm->get('thumbnail')->getData();
            if ($thumbnail) {
                $newFileName = uniqid() . '.' . $thumbnail->guessExtension();

                try {
                    $thumbnail->move(
                        $this->getParameter('kernel.project_dir') . '/public/asset/media/categories',
                        $newFileName
                    );
                } catch (FileException $e) {
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
            if ($thumbnail) {
                $newFileName = uniqid() . '.' . $thumbnail->guessExtension();

                try {
                    $thumbnail->move(
                        $this->getParameter('kernel.project_dir') . '/public/asset/media/products',
                        $newFileName
                    );
                } catch (FileException $e) {
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

    // EDIT
    #[Route('/admin/edit/{id}', name: 'edit')]
    public function edit($id, Request $request): Response
    {
        //CATEGORY LOGIC
        $category = $this->entityManager->getRepository(Categories::class)->find($id);
        $categoryForm = $this->createForm(CategoriesFormType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $thumbnail = $categoryForm->get('thumbnail')->getData();

            if ($thumbnail) {
                $newFileName = uniqid() . '.' . $thumbnail->guessExtension();

                try {
                    $thumbnail->move(
                        $this->getParameter('kernel.project_dir') . '/public/asset/media/categories',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $category->setThumbnail('/categories/' . $newFileName);
            }

            $category->setName($categoryForm->get('name')->getData());
            $this->entityManager->flush();

            return $this->redirectToRoute('admin');
        }

        //PRODUCT LOGIC
        $product = $this->entityManager->getRepository(Products::class)->find($id);
        $productForm = $this->createForm(ProductsFormType::class, $product);

        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $thumbnail = $productForm->get('thumbnail')->getData();

            if ($thumbnail) {
                $newFileName = uniqid() . '.' . $thumbnail->guessExtension();

                try {
                    $thumbnail->move(
                        $this->getParameter('kernel.project_dir') . '/public/asset/media/products',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }

                $product->setThumbnail('/products/' . $newFileName);
            }

            $product->setName($productForm->get('name')->getData());
            $product->setDescription($productForm->get('description')->getData());
            $product->setPrice($productForm->get('price')->getData());
            $product->setQuantity($productForm->get('quantity')->getData());

            // Handle categories
            $selectedCategories = $productForm->get('c_id')->getData();

            // Compare the currently selected categories with the existing categories
            $existingCategories = $product->getCId()->toArray();

            // Clear existing categories if there's a change
            if (!$this->categoriesAreEqual($selectedCategories, $existingCategories)) {
                $product->getCId()->clear();
            }

            // Add selected categories to the product
            foreach ($selectedCategories as $Pcategory) {
                $product->addCId($Pcategory);
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('admin');
        }

        //RENDER VIEW
        return $this->render('Admin/edit.html.twig', [
            'category' => $category,
            'categoryForm' => $categoryForm->createView(),
            'product' => $product,
            'productForm' => $productForm->createView(),
        ]);
    }
    /**
     * Compares two collections of categories to determine if they are equal.
     *
     * @param \Doctrine\Common\Collections\Collection|array $categoriesA
     * @param \Doctrine\Common\Collections\Collection|array $categoriesB
     *
     * @return bool
     */
    private function categoriesAreEqual($categoriesA, $categoriesB)
    {
        // Convert to arrays if they are not already
        if (!$categoriesA instanceof \Doctrine\Common\Collections\Collection) {
            $categoriesA = is_array($categoriesA) ? $categoriesA : [];
        } else {
            $categoriesA = $categoriesA->toArray();
        }

        if (!$categoriesB instanceof \Doctrine\Common\Collections\Collection) {
            $categoriesB = is_array($categoriesB) ? $categoriesB : [];
        } else {
            $categoriesB = $categoriesB->toArray();
        }

        // Use array_map to extract the IDs
        $categoryIdsA = array_map(function ($category) {
            return $category->getId();
        }, $categoriesA);

        $categoryIdsB = array_map(function ($category) {
            return $category->getId();
        }, $categoriesB);

        // Sort the arrays to ensure order doesn't affect the comparison
        sort($categoryIdsA);
        sort($categoryIdsB);

        // Compare the sorted arrays
        return $categoryIdsA == $categoryIdsB;
    }

    // DELETE
    #[Route('/admin/delete/{id}', methods: ['GET', 'DELETE'], name: 'delete')]
    public function delete($id): Response
    {
        // Check if it's a category
        $category = $this->entityManager->getRepository(Categories::class)->find($id);
        if ($category) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            return $this->redirectToRoute('admin');
        }

        // Check if it's a product
        $product = $this->entityManager->getRepository(Products::class)->find($id);
        if ($product) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
            return $this->redirectToRoute('admin');
        }
    }
}
