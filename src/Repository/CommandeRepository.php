<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function CommandExists($user)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->andWhere('c.valider = 0')
            ->getQuery();

        return $qb->getSingleScalarResult();
    }

    function updateRelanceAvide($idcommande)
    {
        $query = $this->createQueryBuilder('')
            ->update(Commande::class, 'c')
            ->set('c.valider', 1)
            ->where('c.id =:idcommande')
            ->setParameter('idcommande', $idcommande)
            ->getQuery();

        $query->execute();
    }

    public function byHistorique($userId, $commandeId)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.lines', 'l')
            ->addSelect('l')
            ->where('c.id = :idcom')
            ->setParameter('idcom', $commandeId)
            ->andWhere('c.user = :id')
            ->setParameter('id', $userId)
            ->andWhere('c.valider = 1');

        return $qb->getQuery()->getResult();
    }

    public function searchUserAdress($userId)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.user', 'u')
            ->addSelect('u')
            ->leftJoin('u.userAdresse', 'a')
            ->addSelect('a')
            ->where('c.user = :id')
            ->setParameter('id', $userId)
            ->andWhere('c.valider = 1');

        return $qb->getQuery()->getResult();
    }


    public function trouveCommande($idcommande)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.lines', 'l')
            ->addSelect('l')
            ->where('c.id = :id')
            //->andWhere('c.valider = 1')
            ->setParameter('id', $idcommande);

        return $qb->getQuery()->getResult();
    }

    public function trouveCommandeHistorique($idcommande)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.lines', 'l')
            ->addSelect('l')
            ->where('c.id = :id')
            ->andWhere('c.valider = 1')
            ->setParameter('id', $idcommande);

        return $qb->getQuery()->getResult();
    }


    public function findLastOrder($userId)
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('c, MAX(c.id) as idMax')
            ->where('c.user = :id')
            //->andWhere('c.valider = 1')
            ->setParameter('id', $userId);

        return $qb->getQuery()->getScalarResult();
    }

    public function findalls()
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.valider = 1')
            ->orderBy('c.id');

        return $qb->getQuery()->getResult();
    }

}
