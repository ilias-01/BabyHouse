<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\ProductSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                "required" => false,
                "label" => false,
                "attr"  =>[
                    'placeholder'=> 'Search...'
                ]
            ])
            ->add('category',EntityType::class,[
                //'required' => false,
                'label' =>false,
                'class' => Category::class,
                'choice_label' => 'label'
                //'multiple' => true,
            ])
            // ->add('price', RangeType::class, [
            //     'label' =>false,
            //     'attr' => [
            //         'min' => 5,
            //         'max' => 50
            //     ]
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductSearch::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
