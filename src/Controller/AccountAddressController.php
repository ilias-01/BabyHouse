<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em =$em;
    }
    /**
     * @Route("/account/address", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }
    
    /**
     * @Route("/account/address/add", name="add_address")
     */
    public function add(Request $request): Response
    {   
        $address = new Address();
        $form = $this->createForm(AddressType::class,$address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $address->setUser($this->getUser());
            $this->em->persist($address);
            $this->em->flush();

            return $this->redirectToRoute('account_address');
        }
        return $this->render('account/address_form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/address/{id}", name="modify_address")
     */
    public function modify(Address $address,Request $request): Response
    {   
        $form = $this->createForm(AddressType::class,$address);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/address/delete/{id}", name="delete_address")
     */
    public function delete(Address $address): Response
    {   
        if($address && $address->getUser() == $this->getUser()){
            $this->em->remove($address);
            $this->em->flush();
        }
        return $this->redirectToRoute('account_address');
    }

}
