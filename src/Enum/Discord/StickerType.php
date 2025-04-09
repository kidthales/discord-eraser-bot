<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/resources/sticker#sticker-object-sticker-types
 */
enum StickerType: int
{
    /**
     * An official sticker in a pack.
     */
    case STANDARD = 1;

    /**
     * A sticker uploaded to a guild for the guild's members.
     */
    case GUILD = 2;
}
