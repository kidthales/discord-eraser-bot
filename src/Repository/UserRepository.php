<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param int|string $discordId
     * @return User|null
     */
    public function findOneByDiscordId(int|string $discordId): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.discordId = :val')
            ->setParameter('val', $discordId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $identifier
     * @return User|null
     */
    public function findOneByUserIdentifier(string $identifier): ?User
    {
        return $this->findOneByDiscordId($identifier);
    }
}
