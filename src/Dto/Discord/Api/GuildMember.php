<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/guild#guild-member-object-guild-member-structure
 */
final readonly class GuildMember
{
    /**
     * @param string[] $roles Array of role object ids.
     * @param string $joined_at When the user joined the guild.
     * @param bool $deaf Whether the user is deafened in voice channels.
     * @param bool $mute Whether the user is muted in voice channels.
     * @param int $flags Guild member flags represented as a bit set, endpoint defaults to 0.
     * @param User|null $user The user this guild member represents.
     * @param string|null $nick This user's guild nickname.
     * @param string|null $avatar The member's guild avatar hash.
     * @param string|null $premium_since When the user started boosting the guild.
     * @param bool|null $pending Whether the user has not yet passed the guild's Membership Screening requirements.
     * @param string|null $permissions Total permissions of the member in the channel, including overwrites, returned
     * when in the interaction object.
     * @param string|null $communication_disabled_until When the user's timeout will expire and the user will be able to
     * communicate in the guild again, null or a time in the past if the user is not timed out.
     * @param AvatarDecorationData|null $avatar_decoration_data Data for the member's guild avatar decoration.
     */
    public function __construct(
        public array                 $roles,
        public string                $joined_at,
        public bool                  $deaf,
        public bool                  $mute,
        public int                   $flags,
        public ?User                 $user = null,
        public ?string               $nick = null,
        public ?string               $avatar = null,
        public ?string               $premium_since = null,
        public ?bool                 $pending = null,
        public ?string               $permissions = null,
        public ?string               $communication_disabled_until = null,
        public ?AvatarDecorationData $avatar_decoration_data = null
    )
    {
    }
}
