<?php

declare(strict_types=1);

namespace App\DependencyInjection;

final readonly class Parameters
{
    public const string DEFAULT_STRING_PARAMETER = 'app.default.string_parameter';

    public const string DISCORD_API_CLIENT_ACCEPT = 'app.discord.api_client_accept';
    public const string DISCORD_API_CLIENT_BASE_URI = 'app.discord.api_client_base_uri';
    public const string DISCORD_BOT_TOKEN = 'app.discord.bot_token';
    public const string DISCORD_OAUTH2_CLIENT_ID = 'app.discord.oauth2_client_id';
    public const string DISCORD_OAUTH2_CLIENT_SECRET = 'app.discord.oauth2_client_secret';
    public const string DISCORD_PUBLIC_KEY = 'app.discord.public_key';

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }
}
