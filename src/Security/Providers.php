<?php

declare(strict_types=1);

namespace App\Security;

final readonly class Providers
{
    public const string AGENT = 'agent_provider';
    public const string ALL = 'all_provider';
    public const string USER = 'user_provider';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
