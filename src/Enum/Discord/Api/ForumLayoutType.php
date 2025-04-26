<?php

declare(strict_types=1);

namespace App\Enum\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#channel-object-forum-layout-types
 */
enum ForumLayoutType: int
{
    /**
     * No default has been set for forum channel.
     */
    case NOT_SET = 0;

    /**
     * Display posts as a list.
     */
    case LIST_VIEW = 1;

    /**
     * Display posts as a collection of tiles.
     */
    case GALLERY_VIEW = 2;
}
