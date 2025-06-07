<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\TaskRecurrenceType;
use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: '`task`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_TASK_ID', fields: ['id'])]
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
     * @var TaskRecurrenceType|null
     */
    #[ORM\Column(name: 'recurrence_type', type: Types::STRING, enumType: TaskRecurrenceType::class)]
    #[Assert\NotNull]
    private ?TaskRecurrenceType $recurrenceType = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'recurrence_value', type: Types::STRING)]
    #[Assert\NotBlank]
    private ?string $recurrenceValue = null;

    /**
     * @var int|null
     */
    #[ORM\Column(name: 'message_age', type: Types::INTEGER)]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 525960)]
    private ?int $messageAge = null;

    /**
     * @var TaskStatus|null
     */
    #[ORM\Column(type: Types::STRING, enumType: TaskStatus::class)]
    #[Assert\NotNull]
    private ?TaskStatus $status = null;

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

    /**
     * @return TaskRecurrenceType|null
     */
    public function getRecurrenceType(): ?TaskRecurrenceType
    {
        return $this->recurrenceType;
    }

    /**
     * @param TaskRecurrenceType $recurrenceType
     * @return void
     */
    public function setRecurrenceType(TaskRecurrenceType $recurrenceType): void
    {
        $this->recurrenceType = $recurrenceType;
    }

    /**
     * @return string|null
     */
    public function getRecurrenceValue(): ?string
    {
        return $this->recurrenceValue;
    }

    /**
     * @param string $recurrenceValue
     * @return void
     */
    public function setRecurrenceValue(string $recurrenceValue): void
    {
        $this->recurrenceValue = $recurrenceValue;
    }

    /**
     * @return int|null
     */
    public function getMessageAge(): ?int
    {
        return $this->messageAge;
    }

    /**
     * @param int $messageAge
     * @return void
     */
    public function setMessageAge(int $messageAge): void
    {
        $this->messageAge = $messageAge;
    }

    /**
     * @return TaskStatus|null
     */
    public function getStatus(): ?TaskStatus
    {
        return $this->status;
    }

    /**
     * @param TaskStatus $status
     * @return void
     */
    public function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
    }
}
