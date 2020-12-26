<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Faker\Factory;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i=0; $i<10  ; $i++) { 
           $user = new User();
            $user->setFirstname($faker->name)
                ->setLastname($faker->name)
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user,"password1"));
            $manager->persist($user);     
        }
        
        $manager->flush();
    }
}
