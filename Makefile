init: down-clear app-clear node-clear \
	build up app-init node-init node-ready

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

restart: down up

down-clear:
	docker-compose down -v --remove-orphans

build:
	docker-compose build --pull

restart: down up

app-clear:
	docker run --rm -v ${PWD}:/app -w /app alpine sh -c 'rm -rf bootstrap/cache/* storage/logs/*'

app-init: app-permissions app-composer-install app-wait-db app-migrations

app-permissions:
	docker run --rm -v ${PWD}:/app -w /app alpine chmod -R 777 storage bootstrap/cache

app-composer-install:
	docker-compose run --rm app-cli composer install

app-composer-update:
	docker-compose run --rm app-cli composer update

app-wait-db:
	docker-compose run --rm app-cli wait-for-it db:3306 -t 30

app-migrations:
	docker-compose run --rm app-cli php artisan migrate

test:
	docker-compose exec app-cli vendor/bin/phpunit

node-clear:
	docker run --rm -v ${PWD}:/app -w /app alpine sh -c 'rm -rf .ready build'

node-init: node-install

node-install:
	docker-compose run --rm node-cli yarn install

node-upgrade:
	docker-compose run --rm node-cli yarn upgrade

node-ready:
	docker run --rm -v ${PWD}:/app -w /app alpine touch .ready

queue:
	docker-compose exec php-cli php artisan queue:work
