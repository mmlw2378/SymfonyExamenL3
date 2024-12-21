<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Retourne les articles en rupture de stock (quantité restante = 0)
     */
    public function findRupture(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.quantiteRestante = 0')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les articles disponibles (quantité restante > 0)
     */
    public function findAvailable(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.quantiteRestante > 0')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne tous les articles
     */
    public function findAllArticles(): array
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les articles par leur nom
     */
    public function findByLibelle(string $search): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.nomArticle LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les articles paginés avec option de recherche
     */
    public function findPaginatedArticles(int $page, int $limit, string $search = ''): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if (!empty($search)) {
            $queryBuilder->where('a.nomArticle LIKE :search')
                         ->setParameter('search', '%' . $search . '%');
        }

        $queryBuilder->orderBy('a.nomArticle', 'ASC')
                     ->setFirstResult(($page - 1) * $limit)
                     ->setMaxResults($limit);

        $query = $queryBuilder->getQuery();
        return new Paginator($query, true);
    }

    /**
     * Compte le nombre d'articles correspondant à une recherche
     */
    public function countSearchResults(string $search = ''): int
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if ($search) {
            $queryBuilder->where('a.nomArticle LIKE :search')
                         ->setParameter('search', '%' . $search . '%');
        }

        return (int) $queryBuilder->select('COUNT(a.id)')
                                  ->getQuery()
                                  ->getSingleScalarResult();
    }

    /**
     * Retourne les articles en fonction de la quantité restante avec un opérateur de comparaison
     * Exemple d'utilisation : $this->findByQuantityComparison(0, '=');
     */
    public function findByQuantityComparison(int $quantity, string $operator = '>='): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.quantiteRestante ' . $operator . ' :quantity')
            ->setParameter('quantity', $quantity)
            ->getQuery()
            ->getResult();
    }
}
