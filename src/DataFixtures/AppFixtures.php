<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        
        for ($i=0; $i < 9 ; $i++) { 
            $category = new Category();
            $category->setLabel("newBorn");
            $manager->persist($category);
            $product = new Product();
            $product->setTitle($faker->title)
                    ->setDescription($faker->text)
                    ->setName($faker->name)
                    ->setFilename('anete-lusina-zwshjake-ii-unsplash-5fb006bb7ce57818534493.jpg')
                    ->setCategory($category)
                    ->setPrix($faker->randomNumber(2));
            $manager->persist($product);
        }
        
        $manager->flush();
    }
}
