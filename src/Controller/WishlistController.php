<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Wishlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishlistController extends AbstractController
{

    public EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/wishlist/add/{id}', name: 'app_wishlist', methods: ['POST'])]
    public function index(Product $product): Response
    {
        $user = $this->getUser();

        $wishlist = new Wishlist();
        $wishlist->setUser($user);
        $wishlist->setProduct($product);

        $this->entityManager->persist($wishlist);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_product_id', ['id' => $product->getId()]);
    }
}
