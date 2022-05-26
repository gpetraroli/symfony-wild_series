<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $categoryRepository->add($category, true);

            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{categoryName}', name: 'show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, $categoryName): Response
    {
        $category = $doctrine->getRepository(Category::class)->findOneBy([
            'name' => $categoryName
        ]);

        if (!$category) {
            throw $this->createNotFoundException('Category "' . $categoryName . '" was not found.');
        }

        $programs = $doctrine->getRepository(Program::class)->findBy(
            ['category' => $category->getId()],
            ['id' => 'DESC'],
            3
        );

        return $this->render('category/show.html.twig', ['programs' => $programs, 'category' => $categoryName,]);
    }
}
