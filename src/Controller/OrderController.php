<?php

namespace App\Controller;

use App\Entity\OrderItems;
use App\Entity\Orders;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\Repository\OrderItemsRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    public CartRepository $cartRepository;
    public ProductRepository $productRepository;
    public OrderItemsRepository $orderItemsRepository;

    public function __construct(CartRepository $cartRepository, ProductRepository $productRepository, OrderItemsRepository $orderItemsRepository)
    {
        $this->cartRepository = $cartRepository;
        $this->productRepository = $productRepository;
        $this->orderItemsRepository = $orderItemsRepository;
    }
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new Response('User not authenticated', Response::HTTP_UNAUTHORIZED);
        }
        $order = new Orders();
        $order->setUser($this->getUser() ?? null);

        $entityManager->persist($order);
        $cartItems = $this->cartRepository->findBy(['user' => $user]);
        foreach ($cartItems as $cartItem) {
            foreach ($cartItem->getCartItems() as $item) {
                $orderItem = new OrderItems();
                $orderItem->setOrders($order);
                $orderItem->setProduct($item->getProduct());
                $orderItem->setQuantity($item->getQuantity());
            }
        }
        $entityManager->persist($orderItem);

        foreach ($cartItems as $cartItem) {
            $entityManager->remove($cartItem);
        }
        $session = $request->getSession();

        $session->remove('cartSession');
        $entityManager->flush();

        return $this->redirectToRoute('app_product');
    }

    #[Route('/orders', name: 'app_orders')]
    public function orders(): Response
    {
        $orders = $this->orderItemsRepository->findAll();

        return $this->render('order/orders.html.twig', ['orders' => $orders]);


    }
}
