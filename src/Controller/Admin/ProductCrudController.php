<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            ImageField::new('filename')
                ->setLabel('Image')
                 ->setBasePath('images/products')
                 ->setUploadDir('public/images/products')
                 ->setUploadedFileNamePattern('[randomhash].[extension]')
                 ->setRequired(false),
            TextField::new('title'),
            TextareaField::new('description'),
            BooleanField::new('isBest'),
            MoneyField::new('prix')->setCurrency('USD'),
        ];
    }
    
}
