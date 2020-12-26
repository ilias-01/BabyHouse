<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',TextType::class,[
                'label' => 'Your email address',
                'disabled' => true
            ])
            ->add('firstname',TextType::class,[
                'label' => 'Your firstname',
                'disabled' => true
            ])
            ->add('lastname',TextType::class,[
                'label' => 'Your lastname',
                'disabled' => true
            ])
            ->add('old_password',PasswordType::class,[
                'label'=>'Your old password',
                'attr' => [
                    'placeholder' => 'Please enter your actual password'
                ],
                'mapped' => false
            ])
            ->add('new_password',RepeatedType::class,[
                'type' => PasswordType::class,
                'invalid_message' => 'the password and the confirmation must be identical',
                'label'=> 'Your new password',
                'required'=> true,
                'mapped' => false,
                'first_options' => ['label' => 'Your new password'],
                'second_options' => ['label' => 'Confirme your new password']
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Submit modification',
                'attr' =>[
                    'class' => 'btn btn-block'
                ]
            ]);
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
