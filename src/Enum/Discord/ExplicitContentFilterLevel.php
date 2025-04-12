<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/resources/guild#guild-object-explicit-content-filter-level
 */
enum ExplicitContentFilterLevel: int
{
    /**
     * Media content will not be scanned.
     */
    case DISABLED = 0;

    /**
     * Media content sent by members without roles will be scanned.
     */
    case MEMBERS_WITHOUT_ROLES = 1;

    /**
     * Media content sent by all members will be scanned.
     */
    case ALL_MEMBERS = 2;
}
