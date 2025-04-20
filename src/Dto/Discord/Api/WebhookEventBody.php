<?php

declare(strict_types=1);

namespace App\Dto\Discord\Api;

use App\Enum\Discord\WebhookEventBodyType;
use Symfony\Component\Serializer\Attribute\DiscriminatorMap;

#[DiscriminatorMap(typeProperty: 'type', mapping: [
    WebhookEventBodyType::ApplicationAuthorized->value => ApplicationAuthorizedWebhookEventBody::class
])]
abstract readonly class WebhookEventBody
{
    /**
     * @param WebhookEventBodyType $type Event type.
     * @param string $timestamp Timestamp of when the event occurred in ISO8601 format.
     */
    public function __construct(public WebhookEventBodyType $type, public string $timestamp)
    {
    }
}
