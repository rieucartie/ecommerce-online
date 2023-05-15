<?php

namespace App\Repository;

use App\Entity\Tva;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tva|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tva|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tva[]    findAll()
 * @method Tva[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TvaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tva::class);
    }

}
