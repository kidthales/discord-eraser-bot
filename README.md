# Discord Eraser Bot

Schedule message deletion tasks in your Discord servers.

> ⚠️ Under Development - [The Road to MVP](https://github.com/kidthales/discord-eraser-bot/milestone/1) ⚠️

## Development with Docker

### Building the Development Image

Starting in the project's root directory, perform the following steps:

1. `cd docker`
2. `COMPOSE_BAKE=true docker compose build --pull --no-cache`

>[!TIP]
> Run `make build` from the project's root directory.

### Running the Development Container

Starting in the project's root directory, perform the following steps:

1. `cd docker`
2. `docker compose up --detach`

>[!TIP]
> Run `make up` from the project's root directory.

To stop the development container, run `docker compose down --remove-orphans`, while in the `docker` directory.

>[!TIP]
> Run `make down` from the project's root directory.

#### Running the Unit Tests

Starting in the project's root directory, perform the following steps:

1. `cd docker`
2. `docker compose exec -e APP_ENV=test php bin/phpunit`

>[!TIP]
> Run `make test` from the project's root directory.

### Building the Production Image

Starting in the project's root directory, perform the following steps:

1. `cd docker`
2. `IMAGES_PREFIX=my-prefix/ IMAGES_TAG=my-tag docker buildx bake`
