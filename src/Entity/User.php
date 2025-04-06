<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DiscordIdentifiable;
use App\Entity\Traits\Identifiable;
use App\Entity\Traits\Timestampable;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_DISCORD_ID', fields: ['discordId'])]
final class User implements UserInterface
{
    use Identifiable, DiscordIdentifiable, Timestampable;

    public const string ROLE_USER = 'ROLE_USER';
    public const string ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @var string[]
     */
    #[ORM\Column(type: Types::JSON)]
    #[Assert\AtLeastOneOf([
        new Assert\Count(exactly: 0),
        new Assert\Sequentially([
            new Assert\Count(exactly: 1),
            new Assert\Expression(expression: "'" . self::ROLE_SUPER_ADMIN . "' in value", message: "This collection should contain only '" . self::ROLE_SUPER_ADMIN . "'.")
        ])
    ])]
    private array $roles = [];

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_values(array_unique($roles));
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
