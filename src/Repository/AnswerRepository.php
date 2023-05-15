<?php


namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Answer::class);
        $this->paginator = $paginator;
    }

    /**
     * Récupère les produits en lien avec une recherche
     *
     */
    public function findSearch(Answer $answer, $response_filter)
    {
        $query = $this->getsearchQuery($answer, $response_filter)->getQuery();

        return $this->paginator->paginate(
            $query,
            $answer->page,
            3
        );
    }

    private function getsearchQuery(Answer $answer, $response_filter): QueryBuilder
    {

        $query = $this
            ->createQueryBuilder('a')
            ->leftJoin('a.question', 'q');

        if (($response_filter === "vide")) {

            $query = $query
                ->where("a.body IS NULL ");

        }

        return $query;
    }


}