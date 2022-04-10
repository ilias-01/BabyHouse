<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Wishlist;
use App\Repository\ProductRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WishListController extends AbstractController
{
    /**
     * @Route("/wish-list", name="wish_list")
     */
    public function index(): Response
    {
        return $this->render('wish_list/index.html.twig', [
            'controller_name' => 'WishListController',
        ]);
    }

    //Rename the function To Toggle
    /**
     * @Route("/wish-list/toggle/{id}", name="wish_list_toggle")
     */
    public function toggle(Product $product,EntityManagerInterface $em,WishlistRepository $wishRep): Response
    {
        #Amelioration: rename the function to toggle ???

        //check if the product  is in the wishlist
        $has_wishlist = $wishRep->findBy(['product' => $product , "user"=> $this->getUser()]); 
        if($has_wishlist){
            //delete the product from the wishlist
            $wish = $wishRep->findBy(['product' => $product, "user"=> $this->getUser() ]); 
            $em->remove($wish[0]);
            // $em->persist($has_wishlist[0]);
            $em->flush();
            return $this->json([
                'code' => "200",
                'message' => "the product is well deleted",
                'toggle' => false,
            ],200);
        }else{
            $wish = new Wishlist();
            $wish->setProduct($product)
                ->setUser($this->getUser());
            $em->persist($wish);
            $em->flush();
            return $this->json([
                'code' => "200",
                'message' => "the product is well added to the wishlist",
                'toggle' => true,
            ],200);
        }
    }
}
