init: init-ci site-node-ready panel-ready
init-ci: docker-down-clear \
	site-clear panel-clear \
	docker-pull docker-build docker-up \
	site-init panel-init
up: docker-up
down: docker-down
restart: down up

update-deps: site-composer-update site-node-upgrade panel-yarn-upgrade restart

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --pull

site-clear:
	docker run --rm -v ${PWD}/site:/app -w /app alpine sh -c 'rm -rf .ready storage/framework/cache/data/* storage/framework/sessions/* storage/framework/testing/* storage/framework/views/* storage/logs/*'

site-init: site-permissions site-composer-install site-wait-db site-migrations site-node-install

site-permissions:
	docker run --rm -v ${PWD}/site:/app -w /app alpine chmod 777 -R storage bootstrap/cache

site-composer-install:
	docker-compose run --rm site-php-cli composer install

site-composer-update:
	docker-compose run --rm site-php-cli composer update

site-wait-db:
	docker-compose run --rm site-php-cli wait-for-it db-postgres:5432 -t 30

site-migrations:
	docker-compose run --rm site-php-cli php artisan migrate --no-interaction

site-backup:
	docker-compose run --rm site-postgres-backup

site-node-install:
	docker-compose run --rm site-node-cli yarn install

site-node-upgrade:
	docker-compose run --rm site-node-cli yarn upgrade

site-node-ready:
	docker run --rm -v ${PWD}/site:/app -w /app alpine touch .ready


panel-clear:
	docker run --rm -v ${PWD}/panel:/app -w /app alpine sh -c 'rm -rf .ready build'

panel-init: panel-yarn-install

panel-yarn-install:
	docker-compose run --rm panel-node-cli yarn install

panel-yarn-upgrade:
	docker-compose run --rm panel-node-cli yarn upgrade

panel-ready:
	docker run --rm -v ${PWD}/panel:/app -w /app alpine touch .ready

build: build-panel build-site

build-panel:
	docker --log-level=debug build --pull --file=panel/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/pharm-panel:${IMAGE_TAG} panel

build-site:
	docker --log-level=debug build --pull --file=site/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/pharm-site:${IMAGE_TAG} site
	docker --log-level=debug build --pull --file=site/docker/prod/php-fpm/Dockerfile --tag=${REGISTRY}/pharm-site-php-fpm:${IMAGE_TAG} site
	docker --log-level=debug build --pull --file=site/docker/prod/php-cli/Dockerfile --tag=${REGISTRY}/pharm-site-php-cli:${IMAGE_TAG} site
	docker --log-level=debug build --pull --file=site/docker/common/postgres-backup/Dockerfile --tag=${REGISTRY}/pharm-db-backup:${IMAGE_TAG} site/docker/common

try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build

testing-init:
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml up -d
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml run --rm site-php-cli wait-for-it db-postgres:5432 -t 60
	COMPOSE_PROJECT_NAME=testing docker-compose -f docker-compose-testing.yml run --rm site-php-cli php artisan migrate --no-interaction
	sleep 15

push: push-panel push-site

push-panel:
	docker push ${REGISTRY}/pharm-panel:${IMAGE_TAG}

push-site:
	docker push ${REGISTRY}/pharm-site:${IMAGE_TAG}
	docker push ${REGISTRY}/pharm-site-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY}/pharm-site-php-cli:${IMAGE_TAG}
	docker push ${REGISTRY}/pharm-db-backup:${IMAGE_TAG}

deploy:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'docker network create --driver=overlay traefik-public || true'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'rm -rf pharm_${BUILD_NUMBER} && mkdir pharm_${BUILD_NUMBER}'

	envsubst < docker-compose-prod.yml > docker-compose-prod-env.yml
	scp -o StrictHostKeyChecking=no -P ${PORT} docker-compose-prod-env.yml deploy@${HOST}:pharm_${BUILD_NUMBER}/docker-compose.yml
	rm -f docker-compose-prod-env.yml

	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd pharm_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml pharm --with-registry-auth --prune'

deploy-clean:
	rm -f docker-compose-prod-env.yml

rollback:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd pharm_${BUILD_NUMBER} && docker stack deploy --compose-file docker-compose.yml pharm --with-registry-auth --prune'
