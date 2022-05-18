<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();

        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/{categoryName}', name: 'show', methods: ['GET'])]
    public function show(CategoryRepository $categoryRepository, ProgramRepository $programRepository, $categoryName): Response
    {
        $category = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$category) {
            throw $this->createNotFoundException('Category "' . $categoryName . '" was not found.');
        }

        $programs = $programRepository->findBy(
            ['category' => $category->getId()],
            ['id' => 'DESC'],
            3
        );

        return $this->render('category/show.html.twig', ['programs' => $programs]);
    }
}
