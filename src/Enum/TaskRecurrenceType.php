<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskRecurrenceType: string
{
    case Once  = 'once';
    case Every = 'every';
    case Cron = 'cron';
}
