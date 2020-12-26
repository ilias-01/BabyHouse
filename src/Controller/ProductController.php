<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductSearch;
use App\Form\ProductSearchPriceType;
use App\Form\ProductSearchType;
use App\Form\ProductType;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    //CRUD on the backoffice
    /**
     * @Route("/product/create", name="app.product")
     */
    public function createProduct(Request $request,EntityManagerInterface $em): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('app.home');
        }
        return $this->render('product/create.html.twig', [
            'form' =>$form->createView(),
        ]);
    }

    /**
     * @Route("/product/show/{id}", name="product.show")
     */
    public function showProduct(Product $product,ProductRepository $productRep,CartRepository $cartRep): Response
    {  
        $similar_products = $productRep->findByCategory($product->getCategory()->getId());

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

        return $this->render('product/show.html.twig',[
            "product" => $product,
            "similar_products" => $similar_products,
            'cart_data' => $cartWithData
        ]);
    }

    /**
     * @Route("/shop-grid",name="product.shop")
     */
    public function shop(ProductRepository $productRep,CategoryRepository $categoryRep,CartRepository $cartRep,Request $request )
    {
        // dd($session->get('cart',[]));
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

        //search form
        $search = new ProductSearch();
        $form = $this->createForm(ProductSearchType::class,$search);
        $form->handleRequest($request);

        //Price search form
        $form_price = $this->createForm(ProductSearchPriceType::class,null);
        $form_price->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
                $products = $productRep->findBySearch($search);
                return $this->render('product/shop_grid.html.twig',[
                    'products' => $products,
                    'search_form' => $form->createView(),
                    'search_price' => $form_price->createView(),
                    'cart_data'   => $cartWithData,
                    'categories'   => $categoryRep->findAll(),
                ]);

        }


        if($form_price->isSubmitted() && $form_price->isValid())
        {
            $price = $form_price->get('price')->getData();
            $products = $productRep->findBySearch($price);
                return $this->render('product/shop_grid.html.twig',[
                    'products' => $products,
                    'search_form' => $form->createView(),
                    'search_price' => $form_price->createView(),
                    'cart_data'   => $cartWithData,
                    'categories'   => $categoryRep->findAll(),
                ]);
        }

        return $this->render('product/shop_grid.html.twig',[
            'products' => $productRep->findAll(),
            'categories' => $categoryRep->findAll(),
            'search_form' => $form->createView(),
            'search_price' => $form_price->createView(),
            'cart_data' => $cartWithData
        ]);
    }
}
