<?php

declare(strict_types=1);

namespace App;

use App\Entity\Task;
use App\Enum\TaskRecurrenceType;
use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use App\Scheduler\Message\DeleteTasks;
use App\Scheduler\Message\DoTask;
use App\Scheduler\Message\ScheduleTasks;
use DateTimeImmutable;
use LogicException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
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
use Throwable;

#[AsSchedule]
final class Schedule implements ScheduleProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var SymfonySchedule|null
     */
    private ?SymfonySchedule $schedule = null;

    /**
     * @param CacheInterface $cache
     * @param TaskRepository $taskRepository
     */
    public function __construct(private readonly CacheInterface $cache, private readonly TaskRepository $taskRepository)
    {
    }

    /**
     * @return SymfonySchedule
     */
    public function getSchedule(): SymfonySchedule
    {
        return $this->schedule ??= new SymfonySchedule()
            ->stateful($this->cache) // ensure missed tasks are executed
            ->processOnlyLastMissedRun(true) // ensure only last missed task is run
            ->with(
                RecurringMessage::cron('* * * * *', new ScheduleTasks()),
                RecurringMessage::cron('* * * * *', new DeleteTasks()),
                ...array_map(
                    $this->makeRecurringTask(...),
                    $this->taskRepository->findByStatus(TaskStatus::Scheduled)
                )
            )
            ->before(function (PreRunEvent $event) {
                try {
                    switch (get_class($event->getMessage())) {
                        case ScheduleTasks::class:
                            $this->beforeScheduleTasks($event);
                            break;
                        case DoTask::class:
                            $this->beforeDoTask($event);
                            break;
                        case DeleteTasks::class:
                        default:
                            break;
                    }
                } catch (Throwable $e) {
                    $this->logger->error('Unexpected error before message handling: ' . $e->getMessage(), [
                        'messageId' => $event->getMessageContext()->id,
                        'exception' => FlattenException::createFromThrowable($e),
                    ]);
                }
            })
            // Exceptions thrown here may be handled in onFailure callback below.
            ->after(function (PostRunEvent $event) {
                switch (get_class($event->getMessage())) {
                    case DoTask::class:
                        $this->afterDoTask($event);
                        break;
                    case ScheduleTasks::class:
                    case DeleteTasks::class:
                    default:
                        break;
                }
            })
            // Exceptions thrown from after callback above may be handled here.
            ->onFailure(function (FailureEvent $event) {
                $e = $event->getError();
                $this->logger->error('Unexpected error during or after message handling: ' . $e->getMessage(), [
                    'messageId' => $event->getMessageContext()->id,
                    'exception' => FlattenException::createFromThrowable($e),
                ]);
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
                $triggered = false;
                $trigger = new CallbackTrigger(
                    function (DateTimeImmutable $run) use ($task, &$triggered): ?DateTimeImmutable {
                        if ($triggered) {
                            return null;
                        }

                        $triggered = true;

                        return $run->setTimestamp((int) $task->getRecurrenceValue());
                    }
                );
                break;
            default:
                $trigger = new CallbackTrigger(fn () => null);
                break;
        }

        return RecurringMessage::trigger(new JitterTrigger($trigger), new DoTask($task->getId()));
    }

    /**
     * @param PreRunEvent $event
     * @return void
     */
    private function beforeScheduleTasks(PreRunEvent $event): void
    {
        /** @var ScheduleTasks $message */
        $message = $event->getMessage();

        foreach ($this->taskRepository->findByStatus(TaskStatus::Adding) as $task) {
            $recurringTask = $this->makeRecurringTask($task);
            $this->schedule->add($recurringTask);

            $task->setStatus(TaskStatus::Scheduled);

            try {
                $this->taskRepository->flush();
                $message->scheduled[] = $task->getId();
            } catch (Throwable $e) {
                $this->logger->error(
                    'Error updating task status to ' . TaskStatus::Scheduled->value . ': ' . $e->getMessage(),
                    [
                        'messageId' => $event->getMessageContext()->id,
                        'taskId' => $task->getId(),
                        'exception' => FlattenException::createFromThrowable($e),
                    ]
                );
                $this->schedule->remove($recurringTask);
            }
        }
    }

    /**
     * @param PreRunEvent $event
     * @return void
     */
    private function beforeDoTask(PreRunEvent $event): void
    {
        $messageContext = $event->getMessageContext();

        $task = $this->taskRepository->find($event->getMessage()->taskId);

        if ($task === null) {
            $event->shouldCancel(true);
            $this->schedule->removeById($messageContext->id);
            return;
        }

        switch ($task->getStatus()) {
            case TaskStatus::Scheduled:
                break;
            case TaskStatus::Removing:
                $event->shouldCancel(true);

                try {
                    $this->taskRepository->remove($task, true);
                } catch (Throwable $e) {
                    $this->logger->error('Error removing task: ' . $e->getMessage(), [
                        'messageId' => $messageContext->id,
                        'taskId' => $task->getId(),
                        'exception' => FlattenException::createFromThrowable($e),
                    ]);
                    return;
                }

                $this->schedule->removeById($messageContext->id);

                return;
            case TaskStatus::Suspended:
                $event->shouldCancel(true);
                $this->schedule->removeById($messageContext->id);
                return;
            case TaskStatus::Running:
                $event->shouldCancel(true);
                return;
            case TaskStatus::Adding:
            default:
                $event->shouldCancel(true);
                throw new LogicException('Invalid task status: ' . $task->getStatus()?->value);
        }

        $task->setStatus(TaskStatus::Running);

        try {
            $this->taskRepository->flush();
        }  catch (Throwable $e) {
            $this->logger->error(
                'Error updating task status to ' . TaskStatus::Running->value . ': ' . $e->getMessage(),
                [
                    'messageId' => $messageContext->id,
                    'taskId' => $task->getId(),
                    'exception' => FlattenException::createFromThrowable($e),
                ]
            );
            $event->shouldCancel(true);
        }
    }

    /**
     * @param PostRunEvent $event
     * @return void
     */
    private function afterDoTask(PostRunEvent $event): void
    {
        $task = $this->taskRepository->find($event->getMessage()->taskId);

        switch ($task?->getStatus()) {
            case TaskStatus::Running:
                $status = match ($task->getRecurrenceType()) {
                    TaskRecurrenceType::Cron, TaskRecurrenceType::Every => TaskStatus::Scheduled,
                    TaskRecurrenceType::Once => TaskStatus::Removing,
                    default => throw new LogicException(
                        'Invalid task recurrence type: ' . $task->getRecurrenceType()?->value
                    ),
                };
                break;
            case TaskStatus::Suspended:
            case TaskStatus::Removing:
                return;
            case TaskStatus::Adding:
            case TaskStatus::Scheduled:
            default:
                throw new LogicException('Invalid task status: ' . $task?->getStatus()?->value);
        }

        $task->setStatus($status);

        try {
            // TODO: If we throw here, task status will be stuck in Running...
            $this->taskRepository->flush();
        } catch (Throwable $e) {
            $this->logger->critical('Error updating task status to ' . $status->value . ': ' . $e->getMessage(), [
                'messageId' => $event->getMessageContext()->id,
                'taskId' => $task->getId(),
                'exception' => FlattenException::createFromThrowable($e),
            ]);
        }
    }
}
