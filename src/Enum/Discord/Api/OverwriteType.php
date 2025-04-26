<?php

declare(strict_types=1);

namespace App\Enum\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#overwrite-object-overwrite-structure
 */
enum OverwriteType: int
{
    case role = 1;
    case member = 2;
}
