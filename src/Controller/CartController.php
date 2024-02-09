<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Doctrine\DBAL\Exception\DatabaseDoesNotExist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private CartRepository $cartRepository;

    private CartItemRepository $cartItemRepository;

    public function __construct(CartRepository $cartRepository, CartItemRepository $cartItemRepository, EntityManagerInterface $entityManager)
    {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->entityManager = $entityManager;
    }

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
        $productId = $product->getId();


        if (!isset($cartSession[$productId])) {
            $cartSession[$productId] = ['quantity' => 0];
        }

        $cartSession[$productId]['quantity'] += $quantity;

        $session->set('cartSession', $cartSession);

        return $this->json(['cartSession' => $cartSession[$productId]['quantity']]);

    }

    #[Route('/cart', name: 'app_cart')]
   public function cart(): Response
   {
       $user = $this->getUser();

       $cartProducts = $this->cartRepository->findBy(['user' => $user]);

       return $this->render('cart/cart.html.twig', ['cartProducts' => $cartProducts]);
   }

   #[Route('cart/remove/{id}', name: 'app_cart_remove_id')]
   public function remove(Request $request, CartItem $cartItem): Response
   {
       $this->entityManager->remove($cartItem);

       $session = $request->getSession();
       $cartSession = $session->get('cartSession', []);

       $productId = $cartItem->getProduct()->getId();

       unset($cartSession[$productId]);

       $session->set('cartSession', $cartSession);
       return $this->render('cart/cart.html.twig');
   }

   #[Route('/cart/clear', name: 'app_cart_clear')]
   public function clearCart(Request $request): Response
   {
       $user = $this->getUser();

       $this->cartItemRepository->clearCart();

       $this->entityManager->flush();

       $session = $request->getSession();


       $session->remove('cartSession');

       $this->addFlash('info', 'Your cart is empty');

       return $this->render('cart/cart.html.twig');
   }
}
