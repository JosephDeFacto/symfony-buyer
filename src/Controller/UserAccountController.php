<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountController extends AbstractController
{

    #[Route('/user/account', name: 'app_user_account')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('User is not authenticated');
        }

        return $this->render('user_account/index.html.twig', [
            'user' => $user,
        ]);
    }
}
