<?php

declare(strict_types=1);

use App\DependencyInjection\Parameters;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $parameters = $container->parameters();

    $parameters
        ->set(Parameters::DEFAULT_STRING_PARAMETER, '!ChangeThisParameterValue!')
        ->set(Parameters::DISCORD_API_CLIENT_ACCEPT, 'application/json')
        ->set(Parameters::DISCORD_API_CLIENT_BASE_URI, 'https://discord.com/api/v10/')
        ->set(
            Parameters::DISCORD_BOT_TOKEN,
            '%env(default:' . Parameters::DEFAULT_STRING_PARAMETER . ':string:DISCORD_BOT_TOKEN)%'
        )
        ->set(
            Parameters::DISCORD_OAUTH2_CLIENT_ID,
            '%env(default:' . Parameters::DEFAULT_STRING_PARAMETER . ':string:DISCORD_OAUTH2_CLIENT_ID)%'
        )
        ->set(
            Parameters::DISCORD_OAUTH2_CLIENT_SECRET,
            '%env(default:' . Parameters::DEFAULT_STRING_PARAMETER . ':string:DISCORD_OAUTH2_CLIENT_SECRET)%'
        )
        ->set(
            Parameters::DISCORD_PUBLIC_KEY,
            '%env(default:' . Parameters::DEFAULT_STRING_PARAMETER . ':string:DISCORD_PUBLIC_KEY)%'
        );

    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', __DIR__ . '/../src/')
        ->exclude([
            __DIR__ . '/../src/DependencyInjection/',
            __DIR__ . '/../src/Entity/',
            __DIR__ . '/../src/Kernel.php',
        ]);

    $services
        ->set('custom_normalizer', \Symfony\Component\Serializer\Normalizer\CustomNormalizer::class)
        ->tag('serializer.normalizer');

    $parameters->set('.container.dumper.inline_factories', true);
};
