<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $cartRep;
    private $productRep;
    private $em;

    public function __construct(CartRepository $cartRep,ProductRepository $productRep,EntityManagerInterface $em)
    {
        $this->cartRep = $cartRep;
        $this->productRep = $productRep;
        $this->em = $em;
    }

    /**
     * @Route("/order", name="order")
     */
    public function index(): Response
    {
        if(!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('add_address');
        }

        //CART
        $session_array = $this->cartRep->findBy(["user"=>$this->getUser()]);
        
        
        $cartWithData = [];
        foreach ($session_array as  $cart) {
            $cartWithData[]=[
                'product' => $this->productRep->find($cart->getProduct()->getId()),
                'quantity' => $cart->getQuantity()
            ];
        }
        //CART

        $form = $this->createForm(OrderType::class,null,[
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig',[
            'form' =>$form->createView(),
            'cart_data' => $cartWithData
        ]);
    }


    /**
     * @Route("/order/recap", name="order_recap",methods={"POST"})
     */
    public function add(Request $request)
    {
        $form = $this->createForm(OrderType::class,null,[
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        //CART
        $session_array = $this->cartRep->findBy(["user"=>$this->getUser()]);
        
        
        $cartWithData = [];
        foreach ($session_array as  $cart) {
            $cartWithData[]=[
                'product' => $this->productRep->find($cart->getProduct()->getId()),
                'quantity' => $cart->getQuantity()
            ];
        }
        //CART


        if($form->isSubmitted() && $form->isValid()){
            $date = new \DateTime();
            $carrier = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastName();
            $delivery_content .= '<br/>'.$delivery->getPhone();
            if($delivery->getCompany()){
                $delivery_content .= '<br/>'.$delivery->getCompany();
            }
            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();


            //Enregister ma commande - Order
            $order = new Order();
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);
            $this->em->persist($order);

            //Enregistrer mes produit - OrderDetails
            foreach($cartWithData as $product ){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrix());
                $orderDetails->setTotal($product['product']->getPrix() * $product['quantity'] );
            
                $this->em->persist($orderDetails);
            }
            $this->em->flush();
            return $this->render('order/checkout.html.twig',[
                'cart_data' => $cartWithData,
                'carrier' => $carrier,
                'delivery' => $delivery_content,
                'reference' => $order->getReference()
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}
