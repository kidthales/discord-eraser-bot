<?php

declare(strict_types=1);

namespace App\Scheduler\Message;

use Symfony\Component\Uid\Uuid;

final readonly class DoTask
{
    /**
     * @param Uuid $taskId
     */
    public function __construct(public Uuid $taskId)
    {
    }
}
