<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeController extends AbstractController
{
    /**
     * @Route("/order/create-session/{reference}", name="stripe_create_session")
     */
    public function index($reference,OrderRepository $orderRep,ProductRepository $productRep,EntityManagerInterface $em)
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $product_for_stripe= [];
        
        $order = $orderRep->findOneByReference($reference);
        if(!$order){
             return new JsonResponse(['error'=>'order']);
        }
        foreach($order->getOrderDetails()->getValues() as $product )
        {
            $product_object = $productRep->findOneByName($product->getProduct());
            $product_for_stripe[]=[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $product->getPrice()*100,
                    'product_data' => [
                      'name' => $product->getProduct(),
                      'images' => [$YOUR_DOMAIN."/images/products/".$product_object->getFileName()],
                    ],
                  ],
                  'quantity' => $product->getQuantity(),
                ];
        }

            //Ajout du transporteur
            $product_for_stripe[]=[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $order->getCarrierPrice(),
                    'product_data' => [
                      'name' => $order->getCarrierName(),
                      'images' => [$YOUR_DOMAIN],
                    ],
                  ],
                  'quantity' => 1,
                ];

        Stripe::setApiKey('sk_test_51HxzciFUswP4n39TYvuXM8jDQ5gB4spQnqyeUFQHOISZSW1ISDLrgbDLpCQZ8A56FqXlp6qfiw0DOarJPwi7fbr300vPMHqoj2');
        $checkout_session = Session::create([
                'customer_email' => $this->getUser()->getEmail(),
                'payment_method_types' => ['card'],
                'line_items' => [
                    $product_for_stripe
                ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/order/success/{CHECKOUT_SESSION_ID}',
                'cancel_url' => $YOUR_DOMAIN . '/order/error/{CHECKOUT_SESSION_ID}',
              ]);

        $order->setStripeSessionId($checkout_session->id);
        $em->flush();

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}
