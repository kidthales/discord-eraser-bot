<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

use App\Enum\Discord\WebhookType;

/**
 * @see https://discord.com/developers/docs/events/webhook-events#payload-structure
 */
final readonly class WebhookEventPayload
{
    /**
     * @var int Version scheme for the webhook event, currently always 1.
     */
    public int $version;

    /**
     * @param string $application_id ID of your app.
     * @param WebhookType $type Type of webhook, either 0 for PING or 1 for webhook events.
     * @param WebhookEventBody|null $event Event data payload.
     */
    public function __construct(
        public string            $application_id,
        public WebhookType       $type,
        public ?WebhookEventBody $event = null
    )
    {
        $this->version = 1;
    }
}
