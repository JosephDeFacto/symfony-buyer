<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AboutUsController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('service/cart.html.twig', []);
    }
}
