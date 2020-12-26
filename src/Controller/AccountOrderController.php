<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    /**
     * @Route("/account/order", name="account_order")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $orders = $em->getRepository(Order::class)->findPaymentOrders($this->getUser());
        return $this->render('account/orders.html.twig',[
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/account/order/{reference}", name="show_order")
     */
    public function show($reference,EntityManagerInterface $em): Response
    {
        $order = $em->getRepository(Order::class)->findOneByReference($reference);
        return $this->render('account/show_order.html.twig',[
            'order' => $order
        ]);
    }
}
