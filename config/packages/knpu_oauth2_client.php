<?php

declare(strict_types=1);

use App\Controller\DiscordController;
use App\DependencyInjection\Parameters;
use App\Security\Discord\OAuth2Authenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('knpu_oauth2_client', [
        'clients' => [
            OAuth2Authenticator::REGISTRY_CLIENT_KEY => [
                'type' => 'discord',
                'client_id' => param(Parameters::DISCORD_OAUTH2_CLIENT_ID),
                'client_secret' => param(Parameters::DISCORD_OAUTH2_CLIENT_SECRET),
                'redirect_route' => DiscordController::OAUTH2_CHECK_ROUTE_NAME,
                'redirect_params' => [],
            ],
        ],
    ]);
};
