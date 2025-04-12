<?php

declare(strict_types=1);

namespace App\Enum\Discord;

/**
 * @see https://discord.com/developers/docs/events/webhook-events#event-types
 */
enum WebhookEventBodyType: string
{
    /**
     * Sent when an app was authorized by a user to a server or their account
     */
    case ApplicationAuthorized = 'APPLICATION_AUTHORIZED';

    /**
     * Entitlement was created.
     */
    case EntitlementCreate = 'ENTITLEMENT_CREATE';

    /**
     * User was added to a Quest (currently unavailable)
     */
    case QuestUserEnrollment = 'QUEST_USER_ENROLLMENT';
}
