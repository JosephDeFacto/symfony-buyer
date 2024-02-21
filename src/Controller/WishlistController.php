<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishlistController extends AbstractController
{

    public EntityManagerInterface $entityManager;

    public WishlistRepository $wishlistRepository;

    public function __construct(EntityManagerInterface $entityManager, WishlistRepository $wishlistRepository)
    {
        $this->entityManager = $entityManager;
        $this->wishlistRepository = $wishlistRepository;
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

    #[Route('/wishlist', name: 'app_my_wishlist', methods: ['GET'])]
    public function getWishlist(): Response
    {
        $user = $this->getUser();

        $wishlists = $this->wishlistRepository->findBy(['user' => $user]);

        return $this->render('wishlist/index.html.twig', ['wishlists' => $wishlists]);
    }
}
