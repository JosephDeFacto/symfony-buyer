<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Service\OrderCalculator;
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
    private OrderCalculator $orderCalculator;


    public function __construct(CartRepository $cartRepository, CartItemRepository $cartItemRepository, EntityManagerInterface $entityManager, OrderCalculator $orderCalculator)
    {
        $this->cartRepository = $cartRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->entityManager = $entityManager;
        $this->orderCalculator = $orderCalculator;

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
    public function cart(OrderCalculator $orderCalculator): Response
    {
        $user = $this->getUser();
        $subtotal = 0;
        $total = 0;
        $cartProducts = $this->cartRepository->findBy(['user' => $user]);

        foreach ($cartProducts as $cart) {
           foreach ($cart->getCartItems() as $cartItem) {
               $subtotal += $this->orderCalculator->calculateSubtotal($cartItem);
               $total += $orderCalculator->calculateTotal($cartItem);
           }
        }

        return $this->render('cart/cart.html.twig', ['cartProducts' => $cartProducts, 'subtotal' => $subtotal, 'total' => $total]);
    }

   #[Route('cart/remove/{id}', name: 'app_cart_remove_id')]
   public function remove(Request $request, CartItem $cartItem): Response
   {
       $this->entityManager->remove($cartItem);
       $this->entityManager->flush();

       $session = $request->getSession();
       $cartSession = $session->get('cartSession', []);

       $productId = $cartItem->getProduct()->getId();

       unset($cartSession[$productId]);

       $session->set('cartSession', $cartSession);

       /*return $this->render('cart/cart.html.twig');*/
       return $this->redirectToRoute('app_cart');
   }

   #[Route('/cart/clear', name: 'app_cart_clear')]
   public function clearCart(Request $request): Response
   {
       $this->cartItemRepository->clearCart();

       $this->entityManager->flush();

       $session = $request->getSession();

       $session->remove('cartSession');

       $this->addFlash('info', 'Your cart is empty');

       return $this->redirectToRoute('app_cart');
   }
}
