<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Recherche des utilisateurs par rôle.
     */
    public function findByRole(string $role): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere(':role MEMBER OF u.roles')  // Vérifie si le rôle est dans le tableau des rôles
            ->setParameter('role', $role)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des utilisateurs basés sur des critères (nom, email, etc.).
     */
    public function findByCriteria(array $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('u');

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere('u.' . $field . ' = :' . $field)
                         ->setParameter($field, $value);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Recherche un utilisateur par son email.
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche des utilisateurs en fonction de leur nom.
     */
    public function findByName(string $name): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.nom LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();
    }
}
