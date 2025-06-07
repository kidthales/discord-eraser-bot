<?php

namespace App\Enum;

enum TaskStatus: string
{
    case Adding = 'Adding';
    case Removing = 'Removing';
    case Scheduled = 'Scheduled';
    case Running = 'Running';
    case Suspended = 'Suspended';
}
