<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

use App\Enum\Discord\Api\ChannelType;
use App\Enum\Discord\Api\ForumLayoutType;
use App\Enum\Discord\Api\SortOrderType;
use App\Enum\Discord\Api\VideoQualityMode;

/**
 * @see https://discord.com/developers/docs/resources/channel#channel-object-channel-structure
 */
final readonly class Channel
{
    /**
     * @param string $id The id of this channel.
     * @param ChannelType $type The type of channel.
     * @param string|null $guild_id The id of the guild (may be missing for some channel objects received over gateway
     * guild dispatches).
     * @param int|null $position Sorting position of the channel (channels with the same position are sorted by id).
     * @param Overwrite[]|null $permission_overwrites Explicit permission overwrites for members and roles.
     * @param string|null $name The name of the channel (1-100 characters).
     * @param string|null $topic The channel topic (0-4096 characters for GUILD_FORUM and GUILD_MEDIA channels, 0-1024
     * characters for all others).
     * @param bool|null $nsfw Whether the channel is nsfw.
     * @param string|null $last_message_id The id of the last message sent in this channel (or thread for GUILD_FORUM or
     * GUILD_MEDIA channels) (may not point to an existing or valid message or thread).
     * @param int|null $bitrate The bitrate (in bits) of the voice channel.
     * @param int|null $user_limit The user limit of the voice channel.
     * @param int|null $rate_limit_per_user Amount of seconds a user has to wait before sending another message
     * (0-21600); bots, as well as users with the permission manage_messages or manage_channel, are unaffected.
     * @param User[]|null $recipients The recipients of the DM.
     * @param string|null $icon Icon hash of the group DM.
     * @param string|null $owner_id ID of the creator of the group DM or thread.
     * @param string|null $application_id Application id of the group DM creator if it is bot-created.
     * @param bool|null $managed For group DM channels: whether the channel is managed by an application via the
     * gdm.join OAuth2 scope.
     * @param string|null $parent_id For guild channels: id of the parent category for a channel (each parent category
     * can contain up to 50 channels), for threads: id of the text channel this thread was created.
     * @param string|null $last_pin_timestamp When the last pinned message was pinned. This may be null in events such
     * as GUILD_CREATE when a message is not pinned.
     * @param string|null $rtc_region Voice region id for the voice channel, automatic when set to null.
     * @param VideoQualityMode|null $video_quality_mode The camera video quality mode of the voice channel, endpoint
     * defaults to 1 when not present.
     * @param int|null $message_count Number of messages (not including the initial message or deleted messages) in a
     * thread.
     * @param int|null $member_count An approximate count of users in a thread, stops counting at 50.
     * @param ThreadMetadata|null $thread_metadata Thread-specific fields not needed by other channels.
     * @param ThreadMember|null $member Thread member object for the current user, if they have joined the thread, only
     * included on certain API endpoints.
     * @param int|null $default_auto_archive_duration Default duration, copied onto newly created threads, in minutes,
     * threads will stop showing in the channel list after the specified period of inactivity, can be set to: 60, 1440,
     * 4320, 10080.
     * @param string|null $permissions Computed permissions for the invoking user in the channel, including overwrites,
     * only included when part of the resolved data received on a slash command interaction. This does not include
     * implicit permissions, which may need to be checked separately.
     * @param int|null $flags Channel flags combined as a bitfield.
     * @param int|null $total_message_sent Number of messages ever sent in a thread, it's similar to message_count on
     * message creation, but will not decrement the number when a message is deleted.
     * @param ForumTag[]|null $available_tags The set of tags that can be used in a GUILD_FORUM or a GUILD_MEDIA channel.
     * @param string[]|null $applied_tags The IDs of the set of tags that have been applied to a thread in a GUILD_FORUM
     * or a GUILD_MEDIA channel.
     * @param DefaultReaction|null $default_reaction_emoji The emoji to show in the add reaction button on a thread in a
     * GUILD_FORUM or a GUILD_MEDIA channel.
     * @param int|null $default_thread_rate_limit_per_user The initial rate_limit_per_user to set on newly created
     * threads in a channel. this field is copied to the thread at creation time and does not live update.
     * @param SortOrderType|null $default_sort_order The default sort order type used to order posts in GUILD_FORUM and
     * GUILD_MEDIA channels. Defaults to null, which indicates a preferred sort order hasn't been set by a channel admin.
     * @param ForumLayoutType|null $default_forum_layout The default forum layout view used to display posts in
     * GUILD_FORUM channels. Endpoint defaults to 0, which indicates a layout view has not been set by a channel admin.
     */
    public function __construct(
        public string            $id,
        public ChannelType       $type,
        public ?string           $guild_id = null,
        public ?int              $position = null,
        public ?array            $permission_overwrites = null,
        public ?string           $name = null,
        public ?string           $topic = null,
        public ?bool             $nsfw = null,
        public ?string           $last_message_id = null,
        public ?int              $bitrate = null,
        public ?int              $user_limit = null,
        public ?int              $rate_limit_per_user = null,
        public ?array            $recipients = null,
        public ?string           $icon = null,
        public ?string           $owner_id = null,
        public ?string           $application_id = null,
        public ?bool             $managed = null,
        public ?string           $parent_id = null,
        public ?string           $last_pin_timestamp = null,
        public ?string           $rtc_region = null,
        public ?VideoQualityMode $video_quality_mode = null,
        public ?int              $message_count = null,
        public ?int              $member_count = null,
        public ?ThreadMetadata   $thread_metadata = null,
        public ?ThreadMember     $member = null,
        public ?int              $default_auto_archive_duration = null,
        public ?string           $permissions = null,
        public ?int              $flags = null,
        public ?int              $total_message_sent = null,
        public ?array            $available_tags = null,
        public ?array            $applied_tags = null,
        public ?DefaultReaction  $default_reaction_emoji = null,
        public ?int              $default_thread_rate_limit_per_user = null,
        public ?SortOrderType    $default_sort_order = null,
        public ?ForumLayoutType  $default_forum_layout = null
    )
    {
    }
}
