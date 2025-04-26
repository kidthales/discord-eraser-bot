<?php

declare(strict_types=1);

namespace App\Enum\Discord\Api;

/**
 * @see https://discord.com/developers/docs/resources/channel#channel-object-video-quality-modes
 */
enum VideoQualityMode: int
{
    /**
     * Discord chooses the quality for optimal performance.
     */
    case AUTO = 1;

    /**
     * 720p.
     */
    case FULL = 2;
}
