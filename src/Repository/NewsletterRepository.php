<?php

namespace App\Repository;

use App\Entity\Newsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NewsletterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsletter::class);
    }

    public function findInArray($usermail)
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.email)')
            ->where('n.email = :email')
            ->setParameter('email', $usermail)
            ->getQuery()
            ->getSingleResult()
            ;
    }

}
