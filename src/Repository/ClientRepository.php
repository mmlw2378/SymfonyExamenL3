<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * Retourne les clients paginés.
     */
    public function findClientsPaginated(int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC'); // Trier par nom (vous pouvez ajuster si nécessaire)

        $query = $queryBuilder->getQuery()
            ->setFirstResult(($page - 1) * $limit)  // Début de la pagination
            ->setMaxResults($limit); // Limite des résultats

        return new Paginator($query, true);  // Retourne un Paginator pour gérer la pagination
    }

    /**
     * Recherche des clients par téléphone et retourne les résultats paginés.
     */
    public function findByPhone(string $phone, int $page = 1, int $limit = 10): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.telephone LIKE :phone')
            ->setParameter('phone', '%' . $phone . '%') // Recherche partielle
            ->orderBy('c.nom', 'ASC')  // Trier par nom
            ->setFirstResult(($page - 1) * $limit) // Début de la pagination
            ->setMaxResults($limit); // Limite des résultats

        $query = $queryBuilder->getQuery();

        return new Paginator($query, true); // Retourne un Paginator pour gérer la pagination
    }
}
