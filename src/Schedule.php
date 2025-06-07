<?php

declare(strict_types=1);

namespace App;

use App\Entity\Task;
use App\Repository\TaskRepository;
use DateTimeImmutable;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule as SymfonySchedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\Trigger\CallbackTrigger;
use Symfony\Component\Scheduler\Trigger\JitterTrigger;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule]
final class Schedule implements ScheduleProviderInterface
{
    private ?SymfonySchedule $schedule = null;

    public function __construct(private readonly CacheInterface $cache, private readonly TaskRepository $taskRepository)
    {
    }

    public function getSchedule(): SymfonySchedule
    {
        return $this->schedule ??= new SymfonySchedule()
            ->stateful($this->cache) // ensure missed tasks are executed
            ->processOnlyLastMissedRun(true) // ensure only last missed task is run
            ->with(
                ...array_map(function (Task $task) {
                    $firstTrigger = true;
                    return RecurringMessage::trigger(
                        new JitterTrigger(
                            new CallbackTrigger(
                                function (DateTimeImmutable $run) use ($task, &$firstTrigger): ?DateTimeImmutable {
                                    $this->taskRepository->refresh($task);

                                    $messageId = $task->getNextDiscordMessageId();

                                    if ($messageId === null) {
                                        if ($firstTrigger) {
                                            $firstTrigger = false;
                                            return $run;
                                        }

                                        return $run->modify(sprintf('+%s minutes', $task->getMessageTtl()));
                                    }

                                    // TODO: calculate next run time from message snowflake...
                                    return $run;
                                }
                            )
                        ),
                        new \ArrayObject() // TODO
                    );
                }, $this->taskRepository->findScheduled())
            );
    }
}
