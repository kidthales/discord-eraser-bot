<?php

declare(strict_types=1);

namespace App\Dto\Discord;

use App\Enum\Discord\StickerFormatType;
use App\Enum\Discord\StickerType;

/**
 * @see https://discord.com/developers/docs/resources/sticker#sticker-object-sticker-structure
 */
final readonly class Sticker
{
    /**
     * @param string $id ID of the sticker.
     * @param string $name Name of the sticker.
     * @param string|null $description Description of the sticker.
     * @param string $tags Autocomplete/suggestion tags for the sticker (max 200 characters).
     * @param StickerType $type Type of sticker.
     * @param StickerFormatType $format_type Type of sticker format.
     * @param string|null $pack_id For standard stickers, id of the pack the sticker is from.
     * @param bool|null $available Whether this guild sticker can be used, may be false due to loss of Server Boosts.
     * @param string|null $guild_id ID of the guild that owns this sticker.
     * @param User|null $user The user that uploaded the guild sticker.
     * @param int|null $sort_value The standard sticker's sort order within its pack.
     */
    public function __construct(
        public string            $id,
        public string            $name,
        public ?string           $description,
        public string            $tags,
        public StickerType       $type,
        public StickerFormatType $format_type,
        public ?string           $pack_id = null,
        public ?bool             $available = null,
        public ?string           $guild_id = null,
        public ?User             $user = null,
        public ?int              $sort_value = null,
    )
    {
    }
}
