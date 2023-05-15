<?php


namespace App\Repository;

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function SearchQuestion($id)
    {
        $qb = $this->createQueryBuilder('q')
            ->select('p', 'q')
            ->join('q.product', 'p')
            ->addSelect('a', 'q')
            ->join('q.answers', 'a')
            ->Where('q.product =  :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }
}