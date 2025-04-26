<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#thread-metadata-object-thread-metadata-structure
 */
final readonly class ThreadMetadata
{
    /**
     * @param bool $archived Whether the thread is archived.
     * @param int $auto_archive_duration The thread will stop showing in the channel list after auto_archive_duration
     * minutes of inactivity, can be set to: 60, 1440, 4320, 10080.
     * @param string $archive_timestamp Timestamp when the thread's archive status was last changed, used for
     * calculating recent activity.
     * @param bool $locked Whether the thread is locked; when a thread is locked, only users with MANAGE_THREADS can
     * unarchive it.
     * @param bool|null $invitable Whether non-moderators can add other non-moderators to a thread; only available on
     * private threads.
     * @param string|null $create_timestamp Timestamp when the thread was created; only populated for threads created
     * after 2022-01-09.
     */
    public function __construct(
        public bool    $archived,
        public int     $auto_archive_duration,
        public string  $archive_timestamp,
        public bool    $locked,
        public ?bool   $invitable = null,
        public ?string $create_timestamp = null
    )
    {
    }
}
