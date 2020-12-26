<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    
    public function findByCategory($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.Category = :val')
            ->setParameter('val', $value)
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }
    

    public function findBySearch($search)
    {
        $query = $this->createQueryBuilder('p');
        
        if($search instanceof ProductSearch && $search->getTitle()){
            $query = $query
                ->andWhere('p.title LIKE :title')
                ->setParameter('title', '%'.$search->getTitle().'%');
        }
        if($search instanceof ProductSearch && $search->getCategory()){
            $category_id = $search->getCategory()->getId();
            if($search->getCategory()->getLabel() !== "All categories"){
               $query = $query
                ->andWhere('p.Category = :id')
                ->setParameter('id', $category_id); 
            }
        }
        if(!($search instanceof ProductSearch)){
            $query = $query
                ->andWhere('p.prix <= :max ')
                ->andWhere('p.prix >= 0 ')
                ->setParameter('max', $search)
                ;
        }

        return $query
            ->getQuery()
            ->getResult()
        ;
    }
    
}
