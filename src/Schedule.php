<?php

declare(strict_types=1);

namespace App;

use App\Entity\Task;
use App\Enum\TaskRecurrenceType;
use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use App\Scheduler\Message\DoTask;
use App\Scheduler\Message\ScheduleTasks;
use DateTimeImmutable;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\Event\FailureEvent;
use Symfony\Component\Scheduler\Event\PostRunEvent;
use Symfony\Component\Scheduler\Event\PreRunEvent;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule as SymfonySchedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Component\Scheduler\Trigger\CallbackTrigger;
use Symfony\Component\Scheduler\Trigger\CronExpressionTrigger;
use Symfony\Component\Scheduler\Trigger\JitterTrigger;
use Symfony\Component\Scheduler\Trigger\PeriodicalTrigger;
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
                RecurringMessage::cron('* * * * *', new ScheduleTasks()),
                ...array_map(
                    $this->makeRecurringTask(...),
                    $this->taskRepository->findByStatus(TaskStatus::Scheduled)
                )
            )
            ->before(function (PreRunEvent $event) {
                // TODO
                $message = $event->getMessage();
            })
            ->after(function (PostRunEvent $event) {
                // TODO
            })
            ->onFailure(function (FailureEvent $event) {
                // TODO
            });
    }

    /**
     * @param Task $task
     * @return RecurringMessage
     */
    private function makeRecurringTask(Task $task): RecurringMessage
    {
        switch ($task->getRecurrenceType()) {
            case TaskRecurrenceType::Cron:
                $trigger = CronExpressionTrigger::fromSpec($task->getRecurrenceValue());
                break;
            case TaskRecurrenceType::Every:
                $trigger = new PeriodicalTrigger($task->getRecurrenceValue());
                break;
            case TaskRecurrenceType::Once:
            default:
                $triggered = false;
                $trigger = new CallbackTrigger(function (DateTimeImmutable $run) use ($task, &$triggered): ?DateTimeImmutable {
                    if ($triggered) {
                        return null;
                    }

                    $triggered = true;

                    return $run->setTimestamp((int) $task->getRecurrenceValue());
                });
                break;
        }

        return RecurringMessage::trigger(new JitterTrigger($trigger), new DoTask($task->getId()));
    }
}
