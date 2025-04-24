<?php

declare(strict_types=1);

use App\Controller\Admin\DashboardController;
use App\Controller\HomeController;
use App\Entity\User;
use App\Security\AuthenticationEntryPoint;
use App\Security\Discord\Authenticator\OAuth2Authenticator;
use App\Security\Discord\Authenticator\RequestAuthenticator;
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
        ->target(HomeController::ROUTE_NAME);

    $security
        ->roleHierarchy(User::ROLE_SUPER_ADMIN, User::ROLE_USER);

    $security
        ->accessControl()
        ->path('^' . DashboardController::ROUTE_PATH)
        ->roles(User::ROLE_USER);
};
