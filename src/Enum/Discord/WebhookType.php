<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/events/webhook-events#webhook-types
 */
enum WebhookType: int
{
    /**
     * PING event sent to verify your Webhook Event URL is active.
     */
    case PING = 0;

    /**
     * Webhook event (details for event in event body object).
     */
    case Event = 1;
}
