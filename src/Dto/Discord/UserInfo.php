<?php

declare(strict_types=1);

namespace App\Dto\Discord;

/**
 * Assumes that 'identify' Discord OAuth2 scope has been authorized.
 */
final readonly class UserInfo
{
    /**
     * @param string $id
     * @param string $username
     * @param string $discriminator
     * @param string $avatar
     * @param bool $verified
     */
    public function __construct(
        public string $id,
        public string $username,
        public string $discriminator,
        public string $avatar,
        public bool   $verified = false
    )
    {
    }

    /**
     * @return string
     */
    public function getAvatarUrl(): string
    {
        return 'https://cdn.discordapp.com/avatars/' . $this->id . '/' . $this->avatar . '.png';
    }
}
