<?php

declare(strict_types=1);

namespace App\Dto\Discord;

use App\Enum\Discord\WebhookEventBodyType;

/**
 * @see https://discord.com/developers/docs/events/webhook-events#application-authorized
 */
final readonly class ApplicationAuthorizedWebhookEventBody extends WebhookEventBody
{
    /**
     * @param WebhookEventBodyType $type Event type.
     * @param string $timestamp Timestamp of when the event occurred in ISO8601 format.
     * @param ApplicationAuthorizedWebhookEventData $data Data for the event. The shape depends on the event type.
     */
    public function __construct(
        WebhookEventBodyType                         $type,
        string                                       $timestamp,
        public ApplicationAuthorizedWebhookEventData $data
    )
    {
        parent::__construct($type, $timestamp);
    }
}
