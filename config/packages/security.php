<?php

declare(strict_types=1);

use App\Entity\User;
use App\Security\AuthenticationEntryPoint;
use App\Security\Discord\OAuth2Authenticator;
use App\Security\Discord\RequestAuthenticator;
use App\Security\Providers;
use Symfony\Config\SecurityConfig;

return static function (SecurityConfig $security): void {
    $security
        ->provider(Providers::AGENT)
        ->memory()
        ->user(RequestAuthenticator::DISCORD_AGENT_USER_IDENTIFIER);

    $security
        ->provider(Providers::USER)
        ->entity()
        ->class(User::class)
        ->property(User::IDENTIFIER_PROPERTY_NAME);

    $security
        ->provider(Providers::ALL)
        ->chain()
        ->providers([Providers::AGENT, Providers::USER]);

    $security
        ->firewall('dev')
        ->pattern('^/(_(profiler|wdt)|css|images|js)/')
        ->security(false);

    $security
        ->firewall('discord')
        ->pattern('^/discord/webhook-event')
        ->stateless(true)
        ->provider(Providers::ALL)
        ->customAuthenticators([RequestAuthenticator::class]);

    $security
        ->firewall('dashboard')
        ->lazy(true)
        ->provider(Providers::USER)
        ->entryPoint(AuthenticationEntryPoint::class)
        ->customAuthenticators([OAuth2Authenticator::class])
        ->logout()
        ->path('/logout')
        ->target('app_logout'); // TODO
};
