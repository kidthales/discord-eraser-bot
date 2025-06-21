<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Repository\Traits\Alterable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    use Alterable;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param Task $task
     * @param LockMode|int|null $lockMode
     * @return void
     * @throws ORMException
     */
    public function refresh(Task $task, LockMode|int|null $lockMode = null): void
    {
        $this->getEntityManager()->refresh($task, $lockMode);
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

    /**
     * @param TaskStatus $status
     * @return Task[]
     */
    public function findByStatus(TaskStatus $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}
