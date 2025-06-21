<?php

declare(strict_types=1);

namespace App\Scheduler\Message;

use Symfony\Component\Uid\Uuid;

final class ScheduleTasks
{
    /**
     * @param Uuid[] $scheduled
     */
    public function __construct(public array $scheduled = [])
    {
    }
}
