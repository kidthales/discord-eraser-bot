<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#forum-tag-object-forum-tag-structure
 */
final readonly class ForumTag
{
    /**
     * @param string $id The id of the tag.
     * @param string $name The name of the tag (0-20 characters).
     * @param bool $moderated Whether this tag can only be added to or removed from threads by a member with the
     * MANAGE_THREADS permission.
     * @param string|null $emoji_id The id of a guild's custom emoji.
     * @param string|null $emoji_name The unicode character of the emoji.
     */
    public function __construct(
        public string  $id,
        public string  $name,
        public bool    $moderated,
        public ?string $emoji_id,
        public ?string $emoji_name
    )
    {
    }
}
