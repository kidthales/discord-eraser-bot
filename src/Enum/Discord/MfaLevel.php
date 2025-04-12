<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/resources/guild#guild-object-mfa-level
 */
enum MfaLevel: int
{
    /**
     * Guild has no MFA/2FA requirement for moderation actions.
     */
    case NONE = 0;

    /**
     * Guild has a 2FA requirement for moderation actions.
     */
    case ELEVATED = 1;
}
