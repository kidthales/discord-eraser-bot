<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/resources/guild#guild-object-guild-nsfw-level
 */
enum GuildNsfwLevel: int
{
    case DEFAULT = 0;
    case EXPLICIT = 1;
    case SAFE = 2;
    case AGE_RESTRICTED = 3;
}
