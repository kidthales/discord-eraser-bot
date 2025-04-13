# Discord Eraser Bot

[![CI](https://github.com/kidthales/discord-eraser-bot/workflows/CI/badge.svg)](https://github.com/kidthales/discord-eraser-bot/actions/workflows/ci.yml)
[![Coverage](https://kidthales.com/discord-eraser-bot/coverage/badge.svg)](https://kidthales.com/discord-eraser-bot/coverage/)
[![License](https://img.shields.io/badge/License-AGPL_3.0_Only-blue)](https://github.com/kidthales/discord-eraser-bot/blob/main/LICENSE)

Schedule message deletion tasks in your Discord servers.

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
| Run Unit Tests with Coverage Reporting | `cov` _(also prepares test database)_  | `docker compose exec -e APP_ENV=test -e XDEBUG_MODE=coverage php bin/phpunit --coverage-text`                                                                              |
| Build Docs                             | `docs`                                 | `docker run --rm -v $(pwd):/data phpdoc/phpdoc`                                                                                                                            |
| Build Production Image                 |                                        | `IMAGES_PREFIX=my-prefix/ IMAGES_TAG=my-tag docker buildx bake`                                                                                                            |

> [!TIP]
> The Makefile provides many more development shortcuts!
> Run `make` or `make help` in the project's root directory to show the Makefile help screen.
