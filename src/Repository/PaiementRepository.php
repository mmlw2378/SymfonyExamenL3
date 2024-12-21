<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Paiement>
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    /**
     * Recherche des paiements effectués à une date spécifique.
     */
    public function findByDate(\DateTime $date): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.datePaiement = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des paiements effectués dans une période spécifique (entre deux dates).
     */
    public function findByDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.datePaiement BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des paiements en fonction de leur statut (payé ou non).
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des paiements supérieurs à un certain montant.
     */
    public function findByAmountGreaterThan(float $amount): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.montant > :amount')
            ->setParameter('amount', $amount)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche un paiement par son identifiant unique.
     */
    public function findOneById(int $id): ?Paiement
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
