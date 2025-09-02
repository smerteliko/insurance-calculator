.PHONY: up down build start stop restart logs bash composer install update test sf clear-cache

# Docker commands
up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build --no-cache

start:
	docker compose start

stop:
	docker compose stop

restart:
	docker compose restart

logs:
	docker compose logs -f $(filter-out $@,$(MAKECMDGOALS))

bash:
	docker compose exec php bash

# Composer commands
composer:
	docker compose exec php composer $(filter-out $@,$(MAKECMDGOALS))

install:
	docker compose exec php composer install

update:
	docker compose exec php composer update

# Symfony commands
sf:
	docker compose exec php bin/console $(filter-out $@,$(MAKECMDGOALS))

# Test commands
test:
	docker compose exec php ./vendor/bin/phpunit

test-coverage:
	docker compose exec php ./vendor/bin/phpunit --coverage-html var/coverage

# Cache commands
clear-cache:
	docker compose exec php bin/console cache:clear

# Database commands
db-migrate:
	docker compose exec php bin/console doctrine:migrations:migrate

db-diff:
	docker compose exec php bin/console doctrine:migrations:diff

db-reset:
	docker compose exec php bin/console doctrine:database:drop --force --if-exists
	docker compose exec php bin/console doctrine:database:create
	docker compose exec php bin/console doctrine:migrations:migrate

# Code quality
cs-fix:
	docker compose exec php ./vendor/bin/php-cs-fixer fix

cs-check:
	docker compose exec php ./vendor/bin/php-cs-fixer fix --dry-run

phpstan:
	docker compose exec php ./vendor/bin/phpstan analyse

%:
	@: