<?php

namespace App\Repository;

use App\Entity\DemandeArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<DemandeArticle>
 */
class DemandeArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeArticle::class);
    }

    /**
     * Retourne les demandes d'articles paginées.
     */
    public function findDemandeArticlesPaginated(int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->orderBy('d.dateDemande', 'DESC'); // Trier par date de demande, décroissant

        $query = $queryBuilder->getQuery()
            ->setFirstResult(($page - 1) * $limit)  // Début de la pagination
            ->setMaxResults($limit); // Limite des résultats

        return new Paginator($query, true); // Retourne un Paginator pour gérer la pagination
    }

    /**
     * Recherche des demandes d'articles par statut (ex: en attente, traitée, annulée).
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
     * Recherche des demandes d'articles par libellé d'article.
     */
    public function findByArticleLibelle(string $libelle, int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->join('d.article', 'a')
            ->where('a.nomArticle LIKE :libelle')
            ->setParameter('libelle', '%' . $libelle . '%') // Recherche partielle
            ->orderBy('d.dateDemande', 'DESC') // Trier par date de demande
            ->setFirstResult(($page - 1) * $limit) // Début de la pagination
            ->setMaxResults($limit); // Limite des résultats

        $query = $queryBuilder->getQuery();

        return new Paginator($query, true); // Retourne un Paginator pour gérer la pagination
    }
}
