<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    #[Route('/my-profile', name:'my-profile', methods: ['GET'])]
    public function myProfile(): Response
    {
        return $this->render('user/user.html.twig');
    }
}
