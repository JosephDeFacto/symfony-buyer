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
    public function index(EntityManagerInterface $entityManager): Response
    {
        // TODO separate the code logic in the service
        $order = new Orders();
        $order->setUser($this->getUser() ?? null);

        $entityManager->persist($order);
        $cartProducts = $this->cartRepository->findAll();

        $orderItems = new OrderItems();
        $orderItems->setOrders($order);
        foreach ($cartProducts as $p) {
            $orderItems->setProduct($p->getCartItems()[0]->getProduct());
        }

        $entityManager->persist($orderItems);

        $entityManager->flush();

        return new Response('New order committed');
    }

    #[Route('/orders', name: 'app_orders')]
    public function orders(): Response
    {
        $orders = $this->orderItemsRepository->findAll();

        return $this->render('order/orders.html.twig', ['orders' => $orders]);


    }
}
