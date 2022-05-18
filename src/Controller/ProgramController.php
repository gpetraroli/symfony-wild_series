<?php

namespace App\Controller;

use App\Entity\Program;
use App\Entity\Season;
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

        $seasons = $program->getSeasons();

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
            ]);
    }

    #[Route('/program/{programId}/season/{seasonId<\d+>}', name: 'program_season_show', methods: ['GET'])]
    public function showSeason(ManagerRegistry $doctrine ,int $programId, int $seasonId): Response
    {
        $program = $doctrine->getRepository(Program::class)->findOneBy(['id' => $programId]);

        $season = $doctrine->getRepository(Season::class)->findOneBy([
            'id' => $seasonId,
            'program' => $program->getId(),
        ]);

        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }
}
