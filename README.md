# Discord Eraser Bot

Schedule message deletion tasks in your Discord servers.

[![CI](https://github.com/kidthales/discord-eraser-bot/workflows/CI/badge.svg)](https://github.com/kidthales/discord-eraser-bot/actions/workflows/ci.yml)
[![Coverage](https://kidthales.com/discord-eraser-bot/badge.svg)](https://kidthales.com/discord-eraser-bot/)

> [!WARNING]
> Under active development. Please see the [MVP milestone](https://github.com/kidthales/discord-eraser-bot/milestone/1) for current status.

> [!NOTE]
> [Design Document](https://github.com/kidthales/discord-eraser-bot/wiki/Design)

## Development with Docker

| Operation                              | Make Target | Shell (in `docker` directory)                                                                 |
|----------------------------------------|-------------|-----------------------------------------------------------------------------------------------|
| Build Image                            | `build`     | `COMPOSE_BAKE=true docker compose build --pull --no-cache`                                    |
| Start Services                         | `up`        | `docker compose up --detach`                                                                  |
| Stop Services                          | `down`      | `docker compose down --remove-orphans`                                                        |
| Run Unit Tests                         | `test`      | `docker compose exec -e APP_ENV=test php bin/phpunit`                                         |
| Run Unit Tests with Coverage Reporting | `cov`       | `docker compose exec -e APP_ENV=test -e XDEBUG_MODE=coverage php bin/phpunit --coverage-text` |
| Build Production Image                 |             | `IMAGES_PREFIX=my-prefix/ IMAGES_TAG=my-tag docker buildx bake`                               |
