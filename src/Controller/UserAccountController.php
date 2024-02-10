<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UpdatePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountController extends AbstractController
{

    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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

    #[Route('/user/update-password', name: 'app_user_password_update', methods: ['POST', 'GET'])]
    public function updatePassword(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UpdatePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($hasher->hashPassword($user, $form->get('newPassword')->getData()));
            $this->entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user_account/updatePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
