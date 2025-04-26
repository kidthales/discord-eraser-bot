<?php

declare(strict_types=1);

namespace App\Enum\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#channel-object-sort-order-types
 */
enum SortOrderType: int
{
    /**
     * Sort forum posts by activity.
     */
    case LATEST_ACTIVITY = 0;

    /**
     * Sort forum posts by creation time (from most recent to oldest).
     */
    case CREATION_DATE = 1;
}
