<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Repository\Traits\Alterable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository
{
    use Alterable;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $identifier
     * @return User|null
     */
    public function findOneByUserIdentifier(string $identifier): ?User
    {
        return $this->findOneByDiscordId($identifier);
    }

    /**
     * @param int|string $discordId
     * @return User|null
     */
    public function findOneByDiscordId(int|string $discordId): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.discordId = :discordId')
            ->setParameter('discordId', $discordId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
