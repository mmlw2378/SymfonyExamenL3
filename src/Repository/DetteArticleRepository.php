<?php

namespace App\Repository;

use App\Entity\DetteArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetteArticle>
 */
class DetteArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetteArticle::class);
    }

    /**
     * Recherche des dettes associées à un article spécifique.
     */
    public function findByArticleId(int $articleId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.article = :articleId')
            ->setParameter('articleId', $articleId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des dettes dont le montant est supérieur à une valeur donnée.
     */
    public function findByAmountGreaterThan(float $amount): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.montant > :amount')
            ->setParameter('amount', $amount)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des dettes associées à une période d'échéance.
     */
    public function findByDueDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.dateEcheance BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche une dette associée à un article spécifique et un montant.
     */
    public function findByArticleAndAmount(int $articleId, float $amount): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.article = :articleId')
            ->andWhere('d.montant = :amount')
            ->setParameter('articleId', $articleId)
            ->setParameter('amount', $amount)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche une dette article par son identifiant unique.
     */
    public function findOneById(int $id): ?DetteArticle
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
