<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait DiscordIdentifiable
{
    /**
     * @var int|string|null
     */
    #[ORM\Column(name: 'discord_id', type: Types::BIGINT)]
    #[Assert\NotNull]
    // Discord snowflakes are 17-19 digits. https://discord.com/developers/docs/reference#snowflakes
    #[Assert\Regex(pattern: '/^[1-9]\d{16,18}$/', message: 'This value is not a Discord Snowflake.', normalizer: 'strval')] // TODO: 20 digits max in 2090...
    private int|string|null $discordId = null;

    /**
     * @return int|string|null
     */
    public function getDiscordId(): int|string|null
    {
        return $this->discordId;
    }

    /**
     * @param int|string $discordId
     * @return void
     */
    public function setDiscordId(int|string $discordId): void
    {
        $this->discordId = $discordId;
    }
}
