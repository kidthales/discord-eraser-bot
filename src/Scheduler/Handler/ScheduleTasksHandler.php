<?php

declare(strict_types=1);

namespace App\Scheduler\Handler;

use App\Scheduler\Message\ScheduleTasks;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ScheduleTasksHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param ScheduleTasks $message
     * @return void
     */
    public function __invoke(ScheduleTasks $message): void
    {
        $this->logger->info('Tasks scheduled', ['uids' => $message->scheduled]);
    }
}
