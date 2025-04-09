<?php

declare(strict_types=1);

namespace App\Dto\Discord;

/**
 * @see https://discord.com/developers/docs/resources/emoji#emoji-object-emoji-structure
 */
final readonly class Emoji
{
    /**
     * @param string|null $id Emoji id.
     * @param string|null $name Emoji name.
     * @param string[]|null $roles Roles allowed to use this emoji.
     * @param User|null $user User that created this emoji.
     * @param bool|null $require_colons Whether this emoji must be wrapped in colons.
     * @param bool|null $managed Whether this emoji is managed.
     * @param bool|null $animated Whether this emoji is animated.
     * @param bool|null $available Whether this emoji can be used, may be false due to loss of Server Boosts.
     */
    public function __construct(
        public ?string $id,
        public ?string $name,
        public ?array  $roles = null,
        public ?User   $user = null,
        public ?bool   $require_colons = null,
        public ?bool   $managed = null,
        public ?bool   $animated = null,
        public ?bool   $available = null
    )
    {
    }
}
