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
- _(optional)_ Free [ngrok](https://ngrok.com/) account
    - A reverse-proxy is needed to forward traffic from a public edge endpoint to our app on localhost; i.e., inbound Discord webhooks & interactions

## Quick Start

These steps assume you are familiar with [setting up a Discord app](https://discord.com/developers/docs/quick-start/getting-started) and will be developing with [GNU Make](https://www.gnu.org/software/make/) & [ngrok](https://ngrok.com/).

1. Copy the contents of the `.env.dev` file to a new git ignored file named `.env.dev.local`; update each environment variable in this new file:
   - `DISCORD_BOT_TOKEN`: Found on the `https://discord.com/developers/applications/<app-id>/bot` page
   - `DISCORD_OAUTH2_CLIENT_ID`: Found on the `https://discord.com/developers/applications/<app-id>/oauth2` page
   - `DISCORD_OAUTH2_CLIENT_SECRET`: Found on the `https://discord.com/developers/applications/<app-id>/oauth2` page
   - `DISCORD_PUBLIC_KEY`: Found on the `https://discord.com/developers/applications/<app-id>/information` page
2. _(optional)_ Create a new git ignored file `docker/.env` and assign the `TZ` environment variable your preferred timezone (default will be `UTC`)
3. Build & run the app, with logs: `make start logs`
4. Visit https://localhost & accept the browser TLS warning to view the app's license & acknowledgements
5. Create a new git ignored file `docker/.env.ngrok` and assign the `NGROK_AUTHTOKEN` environment variable your token from the ngrok dashboard
6. In a separate terminal, run the ngrok agent: `make ngrok`
   - Make note of the `https://<unique-identifier>.ngrok-free.app` address shown on the `Forwarding` line,for use in the next step
7. Go to `https://discord.com/developers/applications/<app-id>/webhooks` and set the endpoint url with `https://<unique-identifier>.ngrok-free.app/discord/webhook-event`; enable the "Application Authorized" event
8. Go to `https://discord.com/developers/applications/<app-id>/oauth2` and add the redirect url `https://localhost/discord/oauth2-check`
9. Go to `https://discord.com/developers/applications/<app-id>/installation` and ensure only the "Guild Install" Installation Context is selected with Install Link set to "None"
10. Install the app to a Discord guild of your choice: `https://discord.com/oauth2/authorize?client_id=<app-id>&permissions=17179877376&integration_type=0&scope=bot`
     - Confirm authorization of "Manage Messages" & "Manage Threads" bot permissions
11. Login to the [web dashboard](https://localhost/_) using the same Discord user account used for the guild install of the app

When finished, ensure you stop the ngrok agent (`ctrl-c` in the ngrok terminal). Use `make down` to stop the app.

For future development cycles, as long as the sqlite db is available and the app remains installed in the guild, you will be able to run the app, login, & perform actions. You only need to run the ngrok agent when you need to receive Discord webhooks & interactions; the associated `https://<unique-identifier>.ngrok-free.app/` endpoints for webhooks & interactions will need to updated in the Discord app dashboard after each new ngrok agent run.

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
| Start ngrok Agent                      | `ngrok`                                | `docker run --rm -it --net=host --env-file .env.ngrok ngrok/ngrok http https://localhost:443 --host-header=localhost`                                                      |
| Build Docs                             | `docs`                                 | `docker run --rm -v $(pwd)/..:/data phpdoc/phpdoc`                                                                                                                         |
| Build Production Image                 |                                        | `IMAGES_PREFIX=my-prefix/ IMAGES_TAG=my-tag docker buildx bake`                                                                                                            |

> [!TIP]
> The Makefile provides many more development shortcuts.
>
> Run `make` or `make help` in the project's root directory to show the Makefile help screen.

## Environment Variables

| Name                            | Level       | Description                                                                                                                                                                                                                                 |
|---------------------------------|-------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `APP_ENV`                       | `container` | [Symfony app environment](https://symfony.com/doc/current/configuration.html#selecting-the-active-environment), defined at the container level for potential reuse                                                                          |
| `APP_SECRET`                    | `symfony`   | [Symfony app secret](https://symfony.com/doc/current/reference/configuration/kernel.html#kernel-secret)                                                                                                                                     |
| `CADDY_EXTRA_CONFIG`            | `container` | [`Caddyfile` snippet](https://caddyserver.com/docs/caddyfile/concepts#snippets) or the [named-routes](https://caddyserver.com/docs/caddyfile/concepts#named-routes) options block, one per line                                             |
| `CADDY_GLOBAL_OPTIONS`          | `container` | [`Caddyfile` global options block](https://caddyserver.com/docs/caddyfile/options#global-options), one per line                                                                                                                             |
| `CADDY_SERVER_EXTRA_DIRECTIVES` | `container` | [`Caddyfile` directives](https://caddyserver.com/docs/caddyfile/concepts#directives)                                                                                                                                                        |
| `CADDY_SERVER_LOG_OPTIONS`      | `container` | [`Caddyfile` server log options block](https://caddyserver.com/docs/caddyfile/directives/log), one per line                                                                                                                                 |
| `DATABASE_URL`                  | `container` | [Symfony database configuration](https://symfony.com/doc/current/doctrine.html#configuring-the-database), also used in container entrypoint                                                                                                 |
| `DISCORD_BOT_TOKEN`             | `symfony`   | Found on the `https://discord.com/developers/applications/<app-id>/bot` page                                                                                                                                                                |
| `DISCORD_OAUTH2_CLIENT_ID`      | `symfony`   | Found on the `https://discord.com/developers/applications/<app-id>/oauth2` page                                                                                                                                                             |
| `DISCORD_OAUTH2_CLIENT_SECRET`  | `symfony`   | Found on the `https://discord.com/developers/applications/<app-id>/oauth2` page                                                                                                                                                             |
| `DISCORD_PUBLIC_KEY`            | `symfony`   | Found on the `https://discord.com/developers/applications/<app-id>/information` page                                                                                                                                                        |
| `FRANKENPHP_CONFIG`             | `container` | List of extra [`Caddyfile` FrankenPHP directives](https://frankenphp.dev/docs/config/#caddyfile-config), one per line                                                                                                                       |
| `FRANKENPHP_WORKER_CONFIG`      | `container` | List of extra [`Caddyfile` FrankenPHP directives](https://frankenphp.dev/docs/config/#caddyfile-config), one per line; defaults to `watch`                                                                                                  |
| `SERVER_NAME`                   | `container` | The server name or address; defaults to `localhost`                                                                                                                                                                                         |
| `TZ`                            | `container` | Timezone used by the [tzdata package](https://pkgs.alpinelinux.org/package/edge/main/x86/tzdata) and the [`date.timezone` php.ini directive](https://www.php.net/manual/en/datetime.configuration.php#ini.date.timezone); defaults to `UTC` |
| `XDEBUG_MODE`                   | `container` | Enable [Xdebug](https://xdebug.org/) when set to `debug`                                                                                                                                                                                    |
