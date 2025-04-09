<?php

declare(strict_types=1);

namespace App\Dto\Discord;

/**
 * @see https://discord.com/developers/docs/events/webhook-events#application-authorized-application-authorized-structure
 */
final readonly class ApplicationAuthorizedWebhookEventData
{
    /**
     * @param User $user User who authorized the app.
     * @param string[] $scopes List of scopes the user authorized.
     * @param int|null $integration_type Installation context for the authorization. Either guild (0) if installed to a
     * server or user (1) if installed to a user's account.
     * @param Guild|null $guild Server which app was authorized for (when integration type is 0).
     */
    public function __construct(
        public User   $user,
        public array  $scopes,
        public ?int   $integration_type = null,
        public ?Guild $guild = null
    )
    {
    }
}
