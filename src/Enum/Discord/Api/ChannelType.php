<?php

declare(strict_types=1);

namespace App\Enum\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#channel-object-channel-types
 */
enum ChannelType: int
{
    /**
     * A text channel within a server.
     */
    case GUILD_TEXT = 0;

    /**
     * A direct message between users.
     */
    case DM = 1;

    /**
     * A voice channel within a server.
     */
    case GUILD_VOICE = 2;

    /**
     * A direct message between multiple users.
     */
    case GROUP_DM = 3;

    /**
     * An organizational category that contains up to 50 channels.
     */
    case GUILD_CATEGORY = 4;

    /**
     * A channel that users can follow and crosspost into their own server (formerly news channels).
     */
    case GUILD_ANNOUNCEMENT = 5;

    /**
     * A temporary sub-channel within a GUILD_ANNOUNCEMENT channel.
     */
    case ANNOUNCEMENT_THREAD = 10;

    /**
     * A temporary sub-channel within a GUILD_TEXT or GUILD_FORUM channel.
     */
    case PUBLIC_THREAD = 11;

    /**
     * A temporary sub-channel within a GUILD_TEXT channel that is only viewable by those invited and those with the
     * MANAGE_THREADS permission.
     */
    case PRIVATE_THREAD = 12;

    /**
     * A voice channel for hosting events with an audience.
     */
    case GUILD_STAGE_VOICE = 13;

    /**
     * The channel in a hub containing the listed servers.
     */
    case GUILD_DIRECTORY = 14;

    /**
     * Channel that can only contain threads.
     */
    case GUILD_FORUM = 15;

    /**
     * Channel that can only contain threads, similar to GUILD_FORUM channels.
     */
    case GUILD_MEDIA = 16;
}
