<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    #[Route('/{id}/addToCart', name: 'app_add_to_cart')]
    public function index(EntityManagerInterface $entityManager, Product $product, Request $request): Response
    {
        $user = $this->getUser();


        $cart = new Cart();
        $cart->setUser($user);
        $cart->setCreatedAt(new \DateTime('NOW'));

        $entityManager->persist($cart);

        $cartItems = new CartItem();
        $quantity = (int) $request->request->get('quantity');

        if ($quantity < 1) {
            $quantity = 1;
        }

        $cartItems->setCart($cart);
        $cartItems->setProduct($product);
        $cartItems->setQuantity($quantity);

        $entityManager->persist($cartItems);

        $entityManager->flush();

        $session = $request->getSession();
        $cartSession = $session->get('cartSession', []);

        if (empty($cartSession)) {
            $session->set('cartSession', $quantity);
        }

        return $this->json(['quantity' => $cartItems->getQuantity(), 'cartSession' => $quantity]);

    }

    #[Route('cart', name: 'app_cart')]
   public function cart(Request $request): Response
   {

       return new Response('');
   }
}
