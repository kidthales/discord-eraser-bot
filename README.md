# Discord Eraser Bot

Schedule message deletion tasks in your Discord servers.

> ⚠️ Under Development - [The Road to MVP](https://github.com/kidthales/discord-eraser-bot/milestone/1) ⚠️

## Development with Docker

1. `cd docker`
2. `docker compose build --pull --no-cache`
3. `docker compose up --detach`
    - (Optional) `docker compose logs --tail=0 --follow`
4. `docker compose down --remove-orphans`

To build production image:
1. `cd docker`
2. `IMAGES_PREFIX=my-prefix/ IMAGES_TAG=my-tag docker buildx bake`
