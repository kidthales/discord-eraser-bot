<?php

declare(strict_types=1);

namespace App\Dto\Discord;

/**
 * @see https://discord.com/developers/docs/resources/guild#welcome-screen-object-welcome-screen-channel-structure
 */
final readonly class WelcomeScreenChannel
{
    /**
     * @param string $channel_id The channel's id.
     * @param string $description The description shown for the channel.
     * @param string|null $emoji_id The emoji id, if the emoji is custom.
     * @param string|null $emoji_name The emoji name if custom, the Unicode character if standard, or null if no emoji
     * is set.
     */
    public function __construct(
        public string  $channel_id,
        public string  $description,
        public ?string $emoji_id,
        public ?string $emoji_name
    )
    {
    }
}
