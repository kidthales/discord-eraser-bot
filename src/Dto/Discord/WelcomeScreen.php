<?php

declare(strict_types=1);

namespace App\Dto\Discord;

/**
 * @see https://discord.com/developers/docs/resources/guild#welcome-screen-object-welcome-screen-structure
 */
final readonly class WelcomeScreen
{
    /**
     * @param string|null $description The server description shown in the welcome screen.
     * @param WelcomeScreenChannel[] $welcome_channels The channels shown in the welcome screen, up to 5.
     */
    public function __construct(public ?string $description, public array $welcome_channels)
    {
    }
}
