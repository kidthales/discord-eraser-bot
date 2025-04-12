<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/resources/user#user-object-premium-types
 */
enum PremiumType: int
{
    case None = 0;
    case NitroClassic = 1;
    case Nitro = 2;
    case NitroBasic = 3;
}
