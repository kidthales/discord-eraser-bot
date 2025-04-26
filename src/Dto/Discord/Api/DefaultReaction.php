<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#default-reaction-object-default-reaction-structure
 */
class DefaultReaction
{
    /**
     * @param string|null $emoji_id The id of a guild's custom emoji.
     * @param string|null $emoji_name The unicode character of the emoji.
     */
    public function __construct(public ?string $emoji_id, public ?string $emoji_name)
    {
    }
}
