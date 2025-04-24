<?php

declare(strict_types=1);

namespace App\Dto\Discord;

use App\Dto\Discord\Api\PartialGuild;

/**
 * Assumes that 'identify' Discord OAuth2 scope has been authorized.
 */
final readonly class AuthorizedGuild
{
    /**
     * @param PartialGuild $guild
     * @return self
     */
    public static function fromPartialGuild(PartialGuild $guild): self
    {
        return new self(
            $guild->id,
            $guild->name,
            $guild->icon,
            $guild->features,
            $guild->banner,
            $guild->owner,
            $guild->permissions,
            $guild->approximate_member_count,
            $guild->approximate_presence_count
        );
    }

    /**
     * @param string $id
     * @param string $name
     * @param string|null $icon
     * @param array $features
     * @param string|null $banner
     * @param bool $owner
     * @param string $permissions
     * @param int|null $approximateMemberCount
     * @param int|null $approximatePresenceCount
     */
    public function __construct(
        public string  $id,
        public string  $name,
        public ?string $icon,
        public array   $features,
        public ?string $banner,
        public bool    $owner,
        public string  $permissions,
        public ?int    $approximateMemberCount,
        public ?int    $approximatePresenceCount
    )
    {
    }

    /**
     * @return string
     */
    public function getIconUrl(): string
    {
        return 'https://cdn.discordapp.com/icons/' . $this->id . '/' . $this->icon . '.png';
    }
}
