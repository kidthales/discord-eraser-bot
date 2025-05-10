<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param string $discordId
     * @return Task[]
     */
    public function findByDiscordGuildId(string $discordId): array
    {
        return $this->getEntityManager()
            ->createQuery('SELECT t, g FROM App\Entity\Task t INNER JOIN t.guild g WHERE g.discordId = :discordId')
            ->setParameter('discordId', $discordId)
            ->getResult();
    }
}
