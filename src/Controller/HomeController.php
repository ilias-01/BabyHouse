<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ProductSearch;
use App\Entity\Wishlist;
use App\Form\ProductQuantityType;
use App\Form\ProductSearchPriceType;
use App\Form\ProductSearchType;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\WishlistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app.home")
     */
    public function index(ProductRepository $productRep,CategoryRepository $categoryRep,
    SessionInterface $session,Request $request,CartRepository $cartRepository,WishlistRepository $wishlistRep): Response
    {
        //$category = new Category(); $category->setLabel('Boys');
        //$categories = $categoryRep->findAll();
        //récupérer trois type de produits: by category/random/recent product
        $random_products = $productRep->findAll();
        $category_boys = $categoryRep->findBy(["label" => "Boys"]);
        $category_girls = $categoryRep->findBy(["label" => "Girls"]);
        
        $products_category_boys = $productRep->findByCategory($category_boys[0]->getId());
        $product_category_girls = $productRep->findByCategory($category_girls[0]->getId());
        //CART
        $session_array = $cartRepository->findBy(["user"=>$this->getUser()]);

        //WISHLIST
        $wish = $wishlistRep->findBy(["user"=>$this->getUser()]);
        // dd($wish);
        $wishlist=[];
        if($wish){
            foreach ($wish as $w) {
                $wishlist[] = $w->getProduct()->getId();
            }
        }
        
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


        

        if($form->isSubmitted() && $form->isValid() && ($search->getCategory() || $search->getTitle()))
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
                    'wishlist_ids' => $wishlist,
                    'search_form' => $form->createView(),
                    'search_price' => $form_price->createView(),
                    'cart_data'   => $cartWithData,
                    'categories'   => $categoryRep->findAll(),
                ]);
        }

        


        return $this->render('home/index.html.twig', [
            'products_random'           => $random_products,
            'products_category_boys'    => $products_category_boys,
            'products_category_girls'   => $product_category_girls,
            'wishlist_ids' => $wishlist,
            'categories'   => $categoryRep->findAll(),
            'cart_data'   => $cartWithData,
            'search_form' => $form->createView(),
        ]);
    }
}
