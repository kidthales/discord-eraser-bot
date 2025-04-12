<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/resources/guild#guild-object-verification-level
 */
enum VerificationLevel: int
{
    /**
     * Unrestricted.
     */
    case NONE = 0;

    /**
     * Must have verified email on account.
     */
    case LOW = 1;

    /**
     * Must be registered on Discord for longer than 5 minutes.
     */
    case MEDIUM = 2;

    /**
     * Must be a member of the server for longer than 10 minutes.
     */
    case HIGH = 3;

    /**
     * Must have a verified phone number.
     */
    case VERY_HIGH = 4;
}
