<a name="readme-top"></a>

[![CI](https://github.com/kidthales/discord-eraser-bot/workflows/CI/badge.svg)](https://github.com/kidthales/discord-eraser-bot/actions/workflows/ci.yml)
[![Coverage](https://kidthales.com/discord-eraser-bot/coverage/badge.svg)](https://kidthales.com/discord-eraser-bot/coverage/)
[![License](https://img.shields.io/badge/License-AGPL_3.0_Only-blue)](https://github.com/kidthales/discord-eraser-bot/blob/main/LICENSE)

<br />

<div align="center" style="background-color: #721c24">
    <p align="center"><strong>Under active development. Not production ready.</strong></p>
</div>

<div align="center">
    <h1 align="center">Discord Eraser Bot</h1>
    <p align="center">
        Schedule message deletion tasks in your Discord servers.
        <br />
        <a href="https://kidthales.com/discord-eraser-bot/"><strong>Explore the docs »</strong></a>
        <br />
        <br />
        <a href="https://github.com/kidthales/discord-eraser-bot/wiki/Design">Design Doc</a>
        ·
        <a href="https://github.com/kidthales/discord-eraser-bot/milestone/1">Milestone: MVP</a>
        ·
        <a href="https://github.com/kidthales/discord-eraser-bot/issues">Request Feature</a>
    </p>
</div>

## Development with Docker

| Operation                              | Make Target | Shell (in `docker` directory)                                                                 |
|----------------------------------------|-------------|-----------------------------------------------------------------------------------------------|
| Build Image                            | `build`     | `COMPOSE_BAKE=true docker compose build --pull --no-cache`                                    |
| Start Services                         | `up`        | `docker compose up --detach`                                                                  |
| Stop Services                          | `down`      | `docker compose down --remove-orphans`                                                        |
| Run Unit Tests                         | `test`      | `docker compose exec -e APP_ENV=test php bin/phpunit`                                         |
| Run Unit Tests with Coverage Reporting | `cov`       | `docker compose exec -e APP_ENV=test -e XDEBUG_MODE=coverage php bin/phpunit --coverage-text` |
| Build Production Image                 |             | `IMAGES_PREFIX=my-prefix/ IMAGES_TAG=my-tag docker buildx bake`                               |
