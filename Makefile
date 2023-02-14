init: init-ci panel-ready client-ready
init-ci: docker-down-clear panel-clear api-clear client-clear \
	docker-pull docker-build docker-up \
	panel-init client-init api-init
up: docker-up
down: docker-down
restart: down up

update-deps: api-composer-update panel-yarn-upgrade client-yarn-upgrade restart

docker-up:
	docker compose up -d

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build --pull


panel-clear:
	docker run --rm -v ${PWD}/panel:/app -w /app alpine sh -c 'rm -rf .ready build'

panel-init: panel-yarn-install

panel-yarn-install:
	docker compose run --rm panel-node-cli yarn install

panel-yarn-upgrade:
	docker compose run --rm panel-node-cli yarn upgrade

panel-ready:
	docker run --rm -v ${PWD}/panel:/app -w /app alpine touch .ready


client-clear:
	docker run --rm -v ${PWD}/client:/app -w /app alpine sh -c 'rm -rf .ready build'

client-init: client-yarn-install

client-yarn-install:
	docker compose run --rm client-node-cli yarn install

client-yarn-upgrade:
	docker compose run --rm client-node-cli yarn upgrade

client-ready:
	docker run --rm -v ${PWD}/client:/app -w /app alpine touch .ready


api-clear:
	docker run --rm -v ${PWD}/api:/app -w /app alpine sh -c 'rm -rf .ready storage/framework/cache/data/* storage/framework/sessions/* storage/framework/testing/* storage/framework/views/* storage/logs/*'

api-init: api-permissions api-composer-install api-wait-db api-migrations

api-permissions:
	docker run --rm -v ${PWD}/api:/app -w /app alpine chmod 777 -R storage bootstrap/cache

api-composer-install:
	docker compose run --rm api-php-cli composer install

api-composer-update:
	docker compose run --rm api-php-cli composer update

api-wait-db:
	docker compose run --rm api-php-cli wait-for-it api-db:5432 -t 30

api-migrations:
	docker compose run --rm api-php-cli php artisan migrate --force

api-backup:
	docker compose run --rm api-postgres-backup


build: build-client build-panel build-parser build-bot build-api

build-client:
	docker --log-level=debug build --pull --file=client/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/pharm-client:${IMAGE_TAG} client
	docker --log-level=debug build --pull --file=client/docker/prod/node/Dockerfile --tag=${REGISTRY}/pharm-client-node:${IMAGE_TAG} client

build-panel:
	docker --log-level=debug build --pull --file=panel/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/pharm-panel:${IMAGE_TAG} panel

build-parser:
	docker --log-level=debug build --pull --file=parser/docker/Dockerfile --tag=${REGISTRY}/pharm-parser:${IMAGE_TAG} parser

build-bot:
	docker --log-level=debug build --pull --file=bot/docker/Dockerfile --tag=${REGISTRY}/pharm-bot:${IMAGE_TAG} bot

build-api:
	docker --log-level=debug build --pull --file=api/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/pharm-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/prod/php-fpm/Dockerfile --tag=${REGISTRY}/pharm-api-php-fpm:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/prod/php-cli/Dockerfile --tag=${REGISTRY}/pharm-api-php-cli:${IMAGE_TAG} api
	# docker --log-level=debug build --pull --file=api/docker/common/postgres-backup/Dockerfile --tag=${REGISTRY}/pharm-db-backup:${IMAGE_TAG} api/docker/common

push: push-client push-panel push-parser push-bot push-api

push-client:
	docker push ${REGISTRY}/pharm-client:${IMAGE_TAG}
	docker push ${REGISTRY}/pharm-client-node:${IMAGE_TAG}

push-panel:
	docker push ${REGISTRY}/pharm-panel:${IMAGE_TAG}

push-parser:
	docker push ${REGISTRY}/pharm-parser:${IMAGE_TAG}

push-bot:
	docker push ${REGISTRY}/pharm-bot:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/pharm-api:${IMAGE_TAG}
	docker push ${REGISTRY}/pharm-api-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/pharm-api-php-cli:${IMAGE_TAG}
	# docker push ${REGISTRY}/pharm-db-backup:${IMAGE_TAG}

deploy:
	ssh -o StrictHostKeyChecking=no arip@${HOST} 'docker network create --driver=overlay traefik-public || true'
	ssh -o StrictHostKeyChecking=no arip@${HOST} 'rm -rf pharm_${BUILD_NUMBER} && mkdir pharm_${BUILD_NUMBER}'

	envsubst < docker-compose-prod.yml > docker-compose-prod-env.yml
	scp -o StrictHostKeyChecking=no docker-compose-prod-env.yml arip@${HOST}:pharm_${BUILD_NUMBER}/docker-compose.yml
	rm -f docker-compose-prod-env.yml

	ssh -o StrictHostKeyChecking=no arip@${HOST} 'cd pharm_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml pharm --with-registry-auth --prune'

rollback:
	ssh -o StrictHostKeyChecking=no arip@${HOST} 'cd pharm_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml pharm --with-registry-auth --prune'
