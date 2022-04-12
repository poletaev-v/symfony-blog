include ./.env

start: dc-up composer-install migration-up fixtures-load

stop: migration-down dc-down

dc-up:
	docker-compose -f docker-compose.yml up -d

dc-down:
	docker-compose -f docker-compose.yml down -v

composer-install:
	docker exec -it app /bin/sh -c "/usr/local/bin/composer install --ignore-platform-reqs"

migration-up:
	docker exec -it app /bin/sh -c "/usr/local/bin/php bin/console doctrine:migrations:migrate --no-interaction"

migration-down:
	docker exec -it app /bin/sh -c "/usr/local/bin/php bin/console doctrine:migrations:execute --down --no-interaction \"DoctrineMigrations\Version20220411060903\""

fixtures-load:
	docker exec -it app /bin/sh -c "/usr/local/bin/php bin/console doctrine:fixtures:load -n"