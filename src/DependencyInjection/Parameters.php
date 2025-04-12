<?php

declare(strict_types=1);

namespace App\DependencyInjection;

final readonly class Parameters
{
    public const string DEFAULT_STRING_PARAMETER = 'app.default_string_parameter';

    public const string DISCORD_APP_PUBLIC_KEY = 'app.discord_app_public_key';
    public const string DISCORD_OAUTH2_CLIENT_ID = 'app.discord_oauth2_client_id';
    public const string DISCORD_OAUTH2_CLIENT_SECRET = 'app.discord_oauth2_client_secret';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
