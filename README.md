# Discord Eraser Bot

Schedule message deletion tasks in your Discord servers.

> [!WARNING]
> Under active development. Please see the [MVP milestone](https://github.com/kidthales/discord-eraser-bot/milestone/1) for current status.

> [!NOTE]
> [Design Document](https://github.com/kidthales/discord-eraser-bot/wiki/Design)

## Development with Docker

| Operation                | Make Target | Shell (in `docker` directory)                                                                                                                                              |
|--------------------------|-------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Build Image              | `build`     | `COMPOSE_BAKE=true docker compose build --pull --no-cache`                                                                                                                 |
| Start Services           | `up`        | `docker compose up --detach`                                                                                                                                               |
| Stop Services            | `down`      | `docker compose down --remove-orphans`                                                                                                                                     |
| Create & Migrate Test DB | `test-db`   | `docker compose exec php php bin/console -e test doctrine:database:create && docker compose exec php php bin/console -e test doctrine:migrations:migrate --no-interaction` |
| Run Unit Tests           | `test`      | `docker compose exec -e APP_ENV=test php bin/phpunit`                                                                                                                      |
| Build Production Image   |             | `IMAGES_PREFIX=my-prefix/ IMAGES_TAG=my-tag docker buildx bake`                                                                                                            |
