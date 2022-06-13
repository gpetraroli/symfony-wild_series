<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommentController extends AbstractController
{
    #[Route('/comment/{commentId}', name: 'comment_edit', methods: ['GET', 'POST'])]
    #[Entity('comment', options: ['id' => 'commentId'])]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $entityManager): Response
    {
        // TODO: refactor condition with Voters or denyAccessUnlessGranted
        if ($this->getUser() !== $comment->getAuthor() && !in_array('ROLE_ADMIN' ,$this->getUser()->getRoles())) {
            throw new AccessDeniedException('Only the owner can edit the comment!');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            // TODO: ask how to redirect to previous route

            return $this->redirectToRoute('program_index');
        }

        return $this->renderForm('comment/edit.html.twig', ['form' => $form]);
    }
}
