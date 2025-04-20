<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/user#get-current-user-guilds-example-partial-guild
 */
final readonly class PartialGuild
{
    /**
     * @param string $id Guild id.
     * @param string|null $name Guild name (2-100 characters, excluding trailing and leading whitespace).
     * @param string|null $icon Icon hash.
     * @param string[] $features Enabled guild features.
     * @param string|null $banner Banner hash.
     * @param bool|null $owner True if the user is the owner of the guild.
     * @param string|null $permissions Total permissions for the user in the guild (excludes overwrites and implicit
     * permissions).
     * @param int|null $approximate_member_count Approximate number of members in this guild, returned from the
     * GET /guilds/<id> and /users/@me/guilds endpoints when with_counts is true.
     * @param int|null $approximate_presence_count Approximate number of non-offline members in this guild, returned
     * from the GET /guilds/<id> and /users/@me/guilds endpoints when with_counts is true.
     */
    public function __construct(
        public string  $id,
        public ?string $name,
        public ?string $icon,
        public array   $features,
        public ?string $banner,
        public ?bool   $owner = null,
        public ?string $permissions = null,
        public ?int    $approximate_member_count = null,
        public ?int    $approximate_presence_count = null
    )
    {
    }
}
