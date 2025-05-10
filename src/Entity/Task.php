<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: '`task`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_TASK_ID', fields: ['id'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_TASK_DISCORD_CHANNEL_ID', fields: ['discordChannelId'])]
class Task
{
    use Timestampable;

    /**
     * @var Uuid|null
     */
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    /**
     * @var Guild|null
     */
    #[ORM\ManyToOne(targetEntity: Guild::class)]
    #[Assert\NotNull]
    private ?Guild $guild = null;

    /**
     * @var int|string|null
     */
    #[ORM\Column(name: 'discord_channel_id', type: Types::BIGINT)]
    #[Assert\NotNull]
    // Discord snowflakes are 17-19 digits. https://discord.com/developers/docs/reference#snowflakes
    #[Assert\Regex(pattern: '/^[1-9]\d{16,18}$/', message: 'This value is not a Discord Snowflake.', normalizer: 'strval')] // TODO: 20 digits max in 2090...
    private int|string|null $discordChannelId = null;

    /**
     * @return Uuid|null
     */
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @return Guild|null
     */
    public function getGuild(): ?Guild
    {
        return $this->guild;
    }

    /**
     * @param Guild $guild
     * @return void
     */
    public function setGuild(Guild $guild): void
    {
        $this->guild = $guild;
    }

    /**
     * @return int|string|null
     */
    public function getDiscordChannelId(): int|string|null
    {
        return $this->discordChannelId;
    }

    /**
     * @param int|string $discordChannelId
     * @return void
     */
    public function setDiscordChannelId(int|string $discordChannelId): void
    {
        $this->discordChannelId = $discordChannelId;
    }
}
