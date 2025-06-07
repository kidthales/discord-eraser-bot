<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Guild;
use App\Repository\Traits\Alterable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Guild>
 */
class GuildRepository extends ServiceEntityRepository
{
    use Alterable;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guild::class);
    }

    /**
     * @param int|string $discordId
     * @return Guild|null
     */
    public function findOneByDiscordId(int|string $discordId): ?Guild
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.discordId = :discordId')
            ->setParameter('discordId', $discordId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param array<int|string> $discordIds
     * @return Guild[]
     */
    public function findInstalledByDiscordIds(array $discordIds): array
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.installed = 1')
            ->andWhere('g.discordId IN (:discordIds)')
            ->setParameter('discordIds', $discordIds, ArrayParameterType::STRING)
            ->getQuery()
            ->getResult();
    }
}
