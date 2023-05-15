<?php

namespace App\Repository;

use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\PaginationInterface;
use App\Entity\Product;
use App\Data\searchData;
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
    /**
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Product::class);
        $this->paginator = $paginator;
    }

    /**
     * Récupère les produits en lien avec une recherche
     */
    public function findSearch(SearchData $search)
    {
        $query = $this->getsearchQuery($search)->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            9
        );
    }

    private function getsearchQuery(SearchData $search, $ignorePrice = false)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.categories', 'c');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('p.name LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->min) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.price >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max) && $ignorePrice === false) {
            $query = $query
                ->andWhere('p.price <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->promo)) {
            $query = $query
                ->andWhere('p.promo = 1');
        }

        if (!empty($search->categories)) {
            $query = $query
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->categories);
        }

        return $query;

    }

    /**
     * @param $sort
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     * tri
     */
    public function findSort($sort)
    {
        $query = $this->getSortProducts($sort)->getQuery();
        return $this->paginator->paginate(
            $query,
            $sort->page,
            9
        );
    }

    /**
     * @param $sort
     * @return \Doctrine\ORM\QueryBuilder
     * c'est le tri de la page accueil dropdown
     */
    public function getSortProducts($sort)
    {

        $query = $this
            ->createQueryBuilder('p')
            ->select('c', 'p')
            ->join('p.categories', 'c');

        switch ($sort) {
            case "amount-asc":
                $query->orderBy("p.price", "asc");
                break;
            case "amount-desc":
                $query->orderBy("p.price", "desc");
                break;
            case "name-asc":
                $query->orderBy("p.name", "asc");
                break;
            case "name-desc":
                $query->orderBy("p.name", "desc");
                break;
        }

        return $query;
    }

    public function findMinMax(SearchData $search)
    {
        $results = $this->getsearchQuery($search, true)
            ->select('MIN(p.price) as min', 'MAX(p.price) as max')
            ->getQuery()->getScalarResult();

        return [(int)$results[0]['min'], (int)$results[0]['max']];

    }

    public function findArray($array)
    {
        $qb = $this->createQueryBuilder('u')
            ->Select('u')
            ->Where('u.id IN (:array)')
            ->setParameter('array', $array);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param $array
     * @return int|mixed|string
     * trouve un produit present dans le tableau
     */
    public function findProducts($array)
    {
        $qb = $this->createQueryBuilder('u')
            ->Select('u')
            ->Where('u.id =  :array')
            ->setParameter('array', $array);

        return $qb->getQuery()->getResult();
    }

    public function majQte(mixed $qte, $id)
    {
        $query = $this->createQueryBuilder('')
            ->update(Product::class, 'p')
            ->set('p.stock ', ':stock')
            ->setParameter('stock', $qte)
            ->where('p.id =:id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->execute();
    }


}
