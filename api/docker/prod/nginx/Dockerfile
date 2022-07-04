FROM nginx:1.21-alpine

RUN apk add --no-cache curl

COPY ./docker/prod/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

COPY ./public ./public
