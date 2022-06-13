<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Repository\CommentRepository;
use App\Repository\ProgramRepository;
use App\Service\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProgramController extends AbstractController
{
    #[Route('/program/', name: 'program_index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $programs = $doctrine->getRepository(Program::class)->findAll();

        return $this->render('program/index.html.twig', [
            'programs' => $programs,
        ]);
    }

    #[Route('/program/new', name: 'program_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProgramRepository $programRepository, MailerInterface $mailer, Slugify $slugify): Response
    {
        $program = new Program();

        $form = $this->createForm(ProgramType::class, $program);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $program->setSlug($slugify->generate($program->getTitle()));
            $program->setOwner($this->getUser());
            $programRepository->add($program, true);

            $this->addFlash('success', 'TV series created correctly.');

            $userEmail = 'user@email.com';
            $email = (new Email())
                ->from('your_email@example.com')
                ->to($userEmail)
                ->subject('A new tv series is avaible')
                ->text('Sending emails is fun again!')
                ->html($this->renderView('program/newProgramEmail.html.twig', [
                    'program' => $program,
                    'user' => $userEmail,
                ]));

            $mailer->send($email);

            return $this->redirectToRoute('program_index');
        }

        return $this->renderForm('program/new.html.twig', ['form' => $form]);
    }

    #[Route('/program/{slug}', name: 'program_show', methods: ['GET'])]
    public function show(Program $program): Response
    {
        // using param converter to retrieve $program
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$program.' found in program\'s table.'
            );
        }

        $seasons = $program->getSeasons();

        return $this->render('program/show.html.twig', [
            'program' => $program,
            'seasons' => $seasons,
        ]);
    }

    #[Route('program/{programId}/season/{seasonId<\d+>}', name: 'program_season_show', methods: ['GET'])]
    #[Entity('program', options: ['id' => 'programId'])]
    #[Entity('season', options: ['id' => 'seasonId'])]
    public function showSeason(Program $program, Season $season): Response
    {
        // using param converter to retrieve $program and $season
        $episodes = $season->getEpisodes();

        return $this->render('program/season_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episodes' => $episodes,
        ]);
    }

    #[Route('/program/{programId}/season/{seasonId}/episode/{episodeId}', name: 'program_episode_show', methods: ['GET', 'POST'])]
    #[Entity('program', options: ['id' => 'programId'])]
    #[Entity('season', options: ['id' => 'seasonId'])]
    #[Entity('episode', options: ['id' => 'episodeId'])]
    public function showEpisode(Program $program, Season $season, Episode $episode, Request $request, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setEpisode($episode);
            $commentRepository->add($comment, true);

            return $this->redirectToRoute('program_episode_show', [
                'programId' => $program->getId(),
                'seasonId' => $season->getId(),
                'episodeId' => $episode->getId(),
            ]);
        }

        return $this->renderForm('program/episode_show.html.twig', [
            'program' => $program,
            'season' => $season,
            'episode' => $episode,
            'form' => $form,
            'comments' => $episode->getComments(),
        ]);
    }

    #[Route('/program/{slug}/edit', name: 'program_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $program->getOwner()) {
            throw new AccessDeniedException('Only the owner can edit the program!');
        }

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', '"' . $program->getTitle() . '" has been updated.');

            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/program/{id}/watchlist', name: 'program_add_to_watchlist', methods: ['GET', 'POST'])]
    public function addToWatchlist(Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()->isInWatchlist($program)) {
            $this->getUser()->removeFromWatchlist($program);
        } else {
            $this->getUser()->addToWatchlist($program);
        }

        $entityManager->flush();

        return $this->json([
            'isInWatchlist' => $this->getUser()->isInWatchlist($program)
        ]);
    }
}
