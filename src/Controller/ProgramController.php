<?php

namespace App\Controller;

use App\Entity\Program;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProgramController extends AbstractController
{
    #[Route('/program/', name: 'program_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $programs = $doctrine->getRepository(Program::class)->findAll();

        return $this->render('program/index.html.twig', ['programs' => $programs]);
    }

    #[Route('/program/{id<\d+>}', name: 'program_show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, $id): Response
    {
        $program = $doctrine->getRepository(Program::class)->findOneBy(['id' => $id]);

        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', ['program' => $program]);
    }
}
