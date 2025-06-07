<?php

declare(strict_types=1);

namespace App\Scheduler\Handler;

use App\Scheduler\Message\DoTask;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DoTaskHandler
{
    public function __invoke(DoTask $message)
    {
        // TODO
    }
}
