<?php

namespace App\Repository;

use App\Entity\Dette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dette>
 */
class DetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dette::class);
    }

    /**
     * Recherche des dettes en fonction de leur statut (par exemple, "payée" ou "en attente").
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des dettes supérieures à un certain montant.
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
     * Recherche des dettes dont la date d'échéance est dans une période spécifique.
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
     * Recherche des dettes d'un client spécifique.
     */
    public function findByClientId(int $clientId): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.client = :clientId')
            ->setParameter('clientId', $clientId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche une dette par son identifiant unique.
     */
    public function findOneById(int $id): ?Dette
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
