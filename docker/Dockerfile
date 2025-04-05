#syntax=docker/dockerfile:1

# Versions
FROM dunglas/frankenphp:1-php8.4-alpine AS frankenphp_upstream

# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target

# Base app image
FROM frankenphp_upstream AS app_base

WORKDIR /app

# hadolint ignore=DL3018
RUN apk add --no-cache \
		acl \
		bash \
		file \
		gettext \
		git \
		tzdata \
	;

RUN set -eux; \
	install-php-extensions \
		@composer \
		apcu \
		intl \
		opcache \
		zip \
	;

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

COPY --link docker/frankenphp/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 docker/frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link docker/frankenphp/Caddyfile /etc/caddy/Caddyfile

ENTRYPOINT ["docker-entrypoint"]

# Dev app image
FROM app_base AS app_dev

ENV APP_ENV=dev
ENV XDEBUG_MODE=off
ENV FRANKENPHP_WORKER_CONFIG=watch

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
	install-php-extensions \
		xdebug \
	;

# Squelch "fatal: detected dubious ownership in repository at '/app'" warning when using 'composer require' command in
# the development container running on a Linux host.
RUN set -eux; \
	git config --global --add safe.directory /app

COPY --link docker/frankenphp/conf.d/20-app.dev.ini $PHP_INI_DIR/app.conf.d/

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch" ]

# Prod app image
FROM app_base AS app_prod

VOLUME /app/var/

ENV APP_ENV=prod

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --link docker/frankenphp/conf.d/20-app.prod.ini $PHP_INI_DIR/app.conf.d/

# prevent the reinstallation of vendors at every changes in the source code
COPY --link composer.* symfony.* ./
RUN set -eux; \
	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# copy sources
COPY --link . ./
RUN rm -Rf docker/frankenphp/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;

HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]
