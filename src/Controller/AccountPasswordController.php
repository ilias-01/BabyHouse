<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    /**
     * @Route("/account/password/modify-password", name="account_password")
     */
    public function index(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordType::class,$user);
        $form->handleRequest($request);
        $notification=null;
        if($form->isSubmitted() && $form->isValid()){

            $old_password = $form->get('old_password')->getData();
            if($encoder->isPasswordValid($user,$old_password)){
                $new_pwd = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($user,$new_pwd);
                $user->setPassword($password);
                $em->persist($user);
                $em->flush($user);
                $notification="your password has been modified";
            }else{
                $notification = "the old password is incorrect";
            }

        }
        return $this->render('account/password.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
