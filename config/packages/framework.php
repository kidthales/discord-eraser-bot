<?php

declare(strict_types=1);

use App\DependencyInjection\Parameters;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (FrameworkConfig $framework, ContainerConfigurator $container): void {
    $framework
        ->secret('%env(string:APP_SECRET)%')
        ->session()
        ->enabled(true);

    $framework
        ->httpClient()
        ->scopedClient('discord_api.client')
        ->scope(param(Parameters::DISCORD_API_CLIENT_BASE_URI))
        ->header('Accept', param(Parameters::DISCORD_API_CLIENT_ACCEPT))
        ->header('Authorization', 'Bot ' . param(Parameters::DISCORD_BOT_TOKEN));

    if ($container->env() === 'test') {
        $framework
            ->test(true)
            ->session()
            ->storageFactoryId('session.storage.factory.mock_file');
    }
};
