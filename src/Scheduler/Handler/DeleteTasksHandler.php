<?php

declare(strict_types=1);

namespace App\Scheduler\Handler;

use App\Scheduler\Message\DeleteTasks;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteTasksHandler
{
    public function __invoke(DeleteTasks $message)
    {
        // TODO
    }
}
