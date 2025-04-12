<?php

declare(strict_types=1);

namespace App\Dto\Discord;

/**
 * @see https://discord.com/developers/docs/topics/permissions#role-object-role-structure
 */
final readonly class Role
{
    /**
     * @param string $id Role id.
     * @param string $name Role name.
     * @param int $color Integer representation of hexadecimal color code.
     * @param bool $hoist If this role is pinned in the user listing.
     * @param int $position Position of this role (roles with the same position are sorted by id).
     * @param string $permissions Permission bit set.
     * @param bool $managed Whether this role is managed by an integration.
     * @param bool $mentionable Whether this role is mentionable.
     * @param int $flags Role flags combined as a bitfield.
     * @param string|null $icon Role icon hash.
     * @param string|null $unicode_emoji Role unicode emoji.
     * @param RoleTags|null $tags The tags this role has.
     */
    public function __construct(
        public string    $id,
        public string    $name,
        public int       $color,
        public bool      $hoist,
        public int       $position,
        public string    $permissions,
        public bool      $managed,
        public bool      $mentionable,
        public int       $flags,
        public ?string   $icon = null,
        public ?string   $unicode_emoji = null,
        public ?RoleTags $tags = null
    )
    {
    }
}
