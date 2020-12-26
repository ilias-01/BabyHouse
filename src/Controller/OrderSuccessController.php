<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * @Route("/order/success/{stripeSessionId}", name="order_success")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        if(!$order || $order->getUser() != $this->getUser() ){
            return $this->redirectToRoute('app.home');
        }
        
        if($order->getState() == 0){
            //vider le panier
            $carts = $this->em->getRepository(Cart::class)->findByUser($this->getUser());
            foreach($carts as $cart)
            {
                $this->em->remove($cart);
            }
            //Modifier le status de notre commande
            $order->setState(1);
            $this->em->flush();

            //Envoyer un mail à notre client pour lui confirmer ça commande
            // $mail = new Mail();
            // $content = "Bonjour".$order->getUser()->getFirstname()."<br>Merci pour votre commande <br>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Tenetur corporis nisi temporibus reprehenderit cumque delectus impedit itaque quidem rerum perferendis! Iure necessitatibus suscipit obcaecati officia itaque earum molestiae ab rem?";
            // $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Votre commande est bien validée',$content);
        }

        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}
