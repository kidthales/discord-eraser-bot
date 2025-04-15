# Paths (local)
ROOT_PATH  := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
DOCKER_PATH = $(ROOT_PATH)/docker

# Executables (local)
AWK         = awk
CD          = cd
DOCKER_COMP = $(CD) $(DOCKER_PATH) && COMPOSE_BAKE=true docker compose
GREP        = grep
SED         = sed

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs bash test cov test-db composer vendor sf cc own

## —— 🔥✉️🔥 Discord Eraser Bot Makefile 🔥✉️🔥 ————————————————————————————————
help: ## Outputs this help screen
	@$(GREP) -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | $(AWK) 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | $(SED) -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

bash: ## Connect to the PHP container via bash
	@$(PHP_CONT) bash

test: test-db ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

cov: test-db ## Start tests with phpunit & generates coverage text & html, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test -e XDEBUG_MODE=coverage php bin/phpunit --coverage-text --coverage-html coverage $(c)

test-db: ## Create test database & run migrations
	@$(SYMFONY) -e test doctrine:database:create
	@$(SYMFONY) -e test doctrine:migrations:migrate --no-interaction

docs: ## Run phpDocumentor to generate this project's documentation
	docker run --rm -v $(ROOT_PATH):/data phpdoc/phpdoc

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— Troubleshooting 🔎 ———————————————————————————————————————————————————————
own: ## On Linux host, set current user as owner of the project files that were created by the docker container
	@$(DOCKER_COMP) run --rm php chown -R $$(id -u):$$(id -g) .
