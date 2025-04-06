<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DiscordIdentifiable;
use App\Entity\Traits\Identifiable;
use App\Entity\Traits\Timestampable;
use App\Repository\GuildRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GuildRepository::class)]
#[ORM\Table(name: '`guild`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_GUILD_DISCORD_ID', fields: ['discordId'])]
class Guild
{
    use Identifiable, DiscordIdentifiable, Timestampable;

    /**
     * @var bool|null
     */
    #[ORM\Column(type: Types::BOOLEAN)]
    #[Assert\NotNull]
    private ?bool $installed = null;

    /**
     * @return bool|null
     */
    public function getInstalled(): ?bool
    {
        return $this->installed;
    }

    /**
     * @param bool $installed
     * @return void
     */
    public function setInstalled(bool $installed): void
    {
        $this->installed = $installed;
    }
}
