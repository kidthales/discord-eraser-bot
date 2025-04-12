<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/resources/guild#guild-object-premium-tier
 */
enum PremiumTier: int
{
    /**
     * Guild has not unlocked any Server Boost perks.
     */
    case NONE = 0;

    /**
     * Guild has unlocked Server Boost level 1 perks.
     */
    case TIER_1 = 1;

    /**
     * Guild has unlocked Server Boost level 2 perks
     */
    case TIER_2 = 2;

    /**
     * Guild has unlocked Server Boost level 3 perks.
     */
    case TIER_3 = 3;
}
