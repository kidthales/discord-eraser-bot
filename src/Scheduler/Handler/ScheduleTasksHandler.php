<?php

declare(strict_types=1);

namespace App\Scheduler\Handler;

use App\Scheduler\Message\ScheduleTasks;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ScheduleTasksHandler
{
    public function __invoke(ScheduleTasks $message)
    {
        // TODO
    }
}
