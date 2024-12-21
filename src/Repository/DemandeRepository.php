<?php

namespace App\Repository;

use App\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Demande>
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }

    /**
     * Retourne les demandes paginées.
     */
    public function findDemandePaginated(int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->orderBy('d.dateDemande', 'DESC'); // Trier par date de demande, décroissant

        $query = $queryBuilder->getQuery()
            ->setFirstResult(($page - 1) * $limit)  // Début de la pagination
            ->setMaxResults($limit); // Limite des résultats

        return new Paginator($query, true); // Retourne un Paginator pour gérer la pagination
    }

    /**
     * Recherche des demandes par statut (par exemple: "en attente", "traitée", "annulée").
     */
    public function findByStatus(string $status, int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->where('d.status = :status')
            ->setParameter('status', $status)
            ->orderBy('d.dateDemande', 'DESC') // Trier par date de demande
            ->setFirstResult(($page - 1) * $limit) // Début de la pagination
            ->setMaxResults($limit); // Limite des résultats

        $query = $queryBuilder->getQuery();

        return new Paginator($query, true); // Retourne un Paginator pour gérer la pagination
    }

    /**
     * Recherche des demandes basées sur un critère (par exemple, par utilisateur ou par date).
     */
    public function findByCriteria(array $criteria, int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('d');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere('d.' . $field . ' = :' . $field)
                         ->setParameter($field, $value);
        }

        $queryBuilder->orderBy('d.dateDemande', 'DESC') // Trier par date de demande
                     ->setFirstResult(($page - 1) * $limit) // Début de la pagination
                     ->setMaxResults($limit); // Limite des résultats

        $query = $queryBuilder->getQuery();

        return new Paginator($query, true); // Retourne un Paginator pour gérer la pagination
    }
}
