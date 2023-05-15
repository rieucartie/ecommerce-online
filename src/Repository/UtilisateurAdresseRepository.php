<?php

namespace App\Repository;

use App\Entity\UtilisateurAdresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UtilisateurAdresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method UtilisateurAdresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method UtilisateurAdresse[]    findAll()
 * @method UtilisateurAdresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurAdresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UtilisateurAdresse::class);
    }

    /**
     * @param $value
     * @return int|mixed|string
     * jointure pour afficher un user et son adressse
     */
    public function findAdresse($value)
    {
        return $this->createQueryBuilder('u')
            ->select('u', 'us')
            ->join('u.user', 'us')
            ->andWhere('us.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $livraison
     * @param $user
     * @return int|mixed|string
     * trouver la livraison avec le user et la session de livraison physique
     */
    public function GetInfosAdress($livraison, $user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $livraison)
            ->andWhere('u.user = :val2')
            ->setParameter('val2', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $livraison
     * @param $user
     * @return int|mixed|string
     * trouver la livraison avec le user et la session de livraison de facturation
     */
    public function GetInfosAdressFacturation($livraison, $user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $livraison)
            ->andWhere('u.user = :val2')
            ->setParameter('val2', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $user
     * @return int|mixed|string
     * trouve User grace a l'id
     */
    public function findByUserId($user)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user = :val')
            ->setParameter('val', $user)
            ->getQuery()
            ->getResult();
    }
}
