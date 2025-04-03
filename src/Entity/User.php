<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Identifiable;
use App\Entity\Traits\Timestampable;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
final class User implements UserInterface
{
    use Identifiable, Timestampable;

    public const string ROLE_USER = 'ROLE_USER';

    /**
     * @var int|string|null
     */
    #[ORM\Column(name: 'discord_id', type: Types::BIGINT, unique: true)]
    private int|string|null $discordId = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

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

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     * @return void
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->discordId;
    }

    /**
     * @return void
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
