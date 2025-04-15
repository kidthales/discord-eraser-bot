# Discord Eraser Bot

[![CI](https://github.com/kidthales/discord-eraser-bot/workflows/CI/badge.svg)](https://github.com/kidthales/discord-eraser-bot/actions/workflows/ci.yml)
[![Coverage](https://kidthales.com/discord-eraser-bot/coverage/badge.svg)](https://kidthales.com/discord-eraser-bot/coverage/)
[![License](https://img.shields.io/badge/License-AGPL_3.0_Only-blue)](https://github.com/kidthales/discord-eraser-bot/blob/main/LICENSE)

Schedule message deletion tasks in your Discord servers.

> [!IMPORTANT]
> This README is a development guide.
>
> For high-level usage guides & other details about what the bot does, please refer to this [documentation](https://kidthales.com/discord-eraser-bot/).

> [!WARNING]
> Under active development. Not production ready.
>
> [Design Doc](https://github.com/kidthales/discord-eraser-bot/wiki/Design) · [Milestone: MVP](https://github.com/kidthales/discord-eraser-bot/milestone/1)

## Requirements

- [Docker Compose](https://docs.docker.com/compose/install/)
- _(optional)_ [GNU Make](https://www.gnu.org/software/make/)
    - Stack Overflow [answer](https://stackoverflow.com/a/32127632) about Windows support

## Development with Docker

| Operation                              | Make Target                            | Shell (in `docker` directory)                                                                                                                                              |
|----------------------------------------|----------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Build Image                            | `build`                                | `COMPOSE_BAKE=true docker compose build --pull --no-cache`                                                                                                                 |
| Start Services                         | `up`                                   | `docker compose up --detach`                                                                                                                                               |
| Stop Services                          | `down`                                 | `docker compose down --remove-orphans`                                                                                                                                     |
| Show Live Logs                         | `logs`                                 | `docker compose logs --tail=0 --follow`                                                                                                                                    |
| Prepare Test Database                  | `test-db`                              | `docker compose exec php php bin/console -e test doctrine:database:create && docker compose exec php php bin/console -e test doctrine:migrations:migrate --no-interaction` |
| Run Unit Tests                         | `test` _(also prepares test database)_ | `docker compose exec -e APP_ENV=test php bin/phpunit`                                                                                                                      |
| Run Unit Tests with Coverage Reporting | `cov` _(also prepares test database)_  | `docker compose exec -e APP_ENV=test -e XDEBUG_MODE=coverage php bin/phpunit --coverage-text --coverage-html coverage`                                                     |
| Build Docs                             | `docs`                                 | `docker run --rm -v $(pwd):/data phpdoc/phpdoc`                                                                                                                            |
| Build Production Image                 |                                        | `IMAGES_PREFIX=my-prefix/ IMAGES_TAG=my-tag docker buildx bake`                                                                                                            |

> [!TIP]
> The Makefile provides many more development shortcuts.
>
> Run `make` or `make help` in the project's root directory to show the Makefile help screen.

## Environment Variables

| Name                            | Level       | Description                                                                                                                                                                                     |
|---------------------------------|-------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `APP_ENV`                       | `container` | [Symfony app environment](https://symfony.com/doc/current/configuration.html#selecting-the-active-environment), defined at the container level for potential reuse                              |
| `APP_SECRET`                    | `symfony`   | [Symfony app secret](https://symfony.com/doc/current/reference/configuration/kernel.html#kernel-secret)                                                                                         |
| `CADDY_EXTRA_CONFIG`            | `container` | [`Caddyfile` snippet](https://caddyserver.com/docs/caddyfile/concepts#snippets) or the [named-routes](https://caddyserver.com/docs/caddyfile/concepts#named-routes) options block, one per line |
| `CADDY_GLOBAL_OPTIONS`          | `container` | [`Caddyfile` global options block](https://caddyserver.com/docs/caddyfile/options#global-options), one per line                                                                                 |
| `CADDY_SERVER_EXTRA_DIRECTIVES` | `container` | [`Caddyfile` directives](https://caddyserver.com/docs/caddyfile/concepts#directives)                                                                                                            |
| `CADDY_SERVER_LOG_OPTIONS`      | `container` | [`Caddyfile` server log options block](https://caddyserver.com/docs/caddyfile/directives/log), one per line                                                                                     |
| `DATABASE_URL`                  | `container` | [Symfony database configuration](https://symfony.com/doc/current/doctrine.html#configuring-the-database), also used in container entrypoint                                                     |
| `DISCORD_APP_PUBLIC_KEY`        | `symfony`   | Found on the `https://discord.com/developers/applications/<app-id>/information` page                                                                                                            |
| `DISCORD_OAUTH2_CLIENT_ID`      | `symfony`   | Found on the `https://discord.com/developers/applications/<app-id>/oauth2` page                                                                                                                 |
| `DISCORD_OAUTH2_CLIENT_SECRET`  | `symfony`   | Found on the `https://discord.com/developers/applications/<app-id>/oauth2` page                                                                                                                 |
| `FRANKENPHP_CONFIG`             | `container` | List of extra [`Caddyfile` FrankenPHP directives](https://frankenphp.dev/docs/config/#caddyfile-config), one per line                                                                           |
| `FRANKENPHP_WORKER_CONFIG`      | `container` | List of extra [`Caddyfile` FrankenPHP directives](https://frankenphp.dev/docs/config/#caddyfile-config), one per line; defaults to `watch`                                                      |
| `PHP_DATE_TIMEZONE`             | `container` | [`date.timezone` php.ini directive](https://www.php.net/manual/en/datetime.configuration.php#ini.date.timezone) value, ensure this matches with `TZ`; defaults to `UTC`                         |
| `SERVER_NAME`                   | `container` | The server name or address; defaults to `localhost`                                                                                                                                             |
| `TZ`                            | `container` | [GNU libc POSIX timezone](https://www.gnu.org/software/libc/manual/html_node/TZ-Variable.html) value, ensure this matches with `PHP_DATE_TIMEZONE`; defaults to `UTC`                           |
| `XDEBUG_MODE`                   | `container` | Enable [Xdebug](https://xdebug.org/) when set to `debug`                                                                                                                                        |
