<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Form\ProductQuantityType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @Route("/cart", name="cart")
     */
    public function index(Request $request, ProductRepository $productRepository,CartRepository $cartRepository): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app.home');
        }
        $session_array = $cartRepository->findBy(["user"=>$this->getUser()]);
        
        $cartWithData = [];
        foreach ($session_array as  $cart) {
            $cartWithData[]=[
                'product' => $productRepository->find($cart->getProduct()->getId()),
                'quantity' => $cart->getQuantity()
            ];
        }
        $total = 0;

        foreach($cartWithData as $item){
            $total += $item['product']->getPrix() * $item['quantity'];
        }

        //ProductQuantity form
        $form_quantity = $this->createForm(ProductQuantityType::class,null);
        $form_quantity->handleRequest($request);

        //à faire avec une autre méthode
        if($form_quantity->isSubmitted() && $form_quantity->isValid())
        {
            dd($form_quantity->get('quantity'));
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'cart_data' => $cartWithData,
            //'quantityForm' => $form_quantity->createView(),
            'total' => $total,
        ]);
    }

    /**
     * @Route("/cart/add/{id}-{quantity}",name="cart.add")
     */
    public function add($id,$quantity,EntityManagerInterface $em,ProductRepository $productRep,CartRepository $cartRep)
    {
        $user = $this->getUser();
        $quantity=intval($quantity);

        if(!$user) return $this->json([
            'code' => 403,
            'message' => "Unauthorized"
        ],403);

        if(!$productRep->find($id))return $this->json([
            'code' => 404,
            'message' => "Product not found"
        ],404);

        //check if the product  is in the cart
        $has_cart = $cartRep->findBy(['product' => $productRep->find($id) , "user"=> $this->getUser() ]); 
        if($has_cart){
            $qte= $has_cart[0]->getQuantity();
            $qte+=$quantity;
            $has_cart[0]->setQuantity($qte);
            $em->persist($has_cart[0]);
        }else{
            $cart = new Cart();
            $cart->setProduct($productRep->find($id))
                ->setUser($this->getUser())
                ->setQuantity($quantity);
            $em->persist($cart);
        }
        $em->flush();
        
        //if($quantity == 1){$quantity == null;}
        return $this->json([
            'code' => "200",
            'message' => "the product is well added",
            //'productNb' => $cartRep->count(["user"=> $this->getUser() ])+$quantity
            'productNb' =>intval($quantity)
        ],200);
    }

    /**
     * @Route("/cart/remove/{id}" , name="cart.remove")
     */
    public function remove(Product $product, SessionInterface $session,CartRepository $cartRep,EntityManagerInterface $em)
    {
        $cart = $cartRep->findBy(['product' => $product, "user"=> $this->getUser() ]); 
        $em->remove($cart[0]);
        $em->flush();

        return $this->json([
            'code' => "200",
            'message' => "the product is well deleted",
            'productNb' => $cartRep->count(["user"=> $this->getUser() ])
            
        ],200);
    }

   /**
     * @Route("/cart/checkout" , name="cart.checkout")
     */
     public function checkout(CartRepository $cartRep,ProductRepository $productRep)
     {
        //CART
        $session_array = $cartRep->findBy(["user"=>$this->getUser()]);
        
        
        $cartWithData = [];
        foreach ($session_array as  $cart) {
            $cartWithData[]=[
                'product' => $productRep->find($cart->getProduct()->getId()),
                'quantity' => $cart->getQuantity()
            ];
        }
        //CART
        $total = 0;

        foreach($cartWithData as $item){
            $total += $item['product']->getPrix() * $item['quantity'];
        }
        return $this->render('cart/checkout.html.twig',[
            "cart_data" => $cartWithData,
            'total' => $total
        ]);
     }
}
