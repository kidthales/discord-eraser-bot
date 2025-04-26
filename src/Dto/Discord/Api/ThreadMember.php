<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#thread-member-object-thread-member-structure
 */
final readonly class ThreadMember
{
    /**
     * @param string $join_timestamp Time the user last joined the thread.
     * @param int $flags Any user-thread settings, currently only used for notifications.
     * @param string|null $id ID of the thread.
     * @param string|null $user_id ID of the user
     * @param GuildMember|null $member Additional information about the user
     */
    public function __construct(
        public string       $join_timestamp,
        public int          $flags,
        public ?string      $id = null,
        public ?string      $user_id = null,
        public ?GuildMember $member = null
    )
    {
    }
}
