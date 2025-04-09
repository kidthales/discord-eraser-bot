<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/resources/guild#guild-object-default-message-notification-level
 */
enum DefaultMessageNotificationLevel: int
{
    /**
     * Members will receive notifications for all messages by default.
     */
    case ALL_MESSAGES = 0;

    /**
     * Members will receive notifications only for messages that @mention them by default.
     */
    case ONLY_MENTIONS = 1;
}
