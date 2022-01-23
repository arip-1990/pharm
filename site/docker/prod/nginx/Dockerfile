FROM node:16-alpine as builder

WORKDIR /app

COPY ./package.json ./yarn.lock ./
RUN yarn install && yarn cache clean

COPY ./ ./
RUN yarn prod

FROM nginx:1.21-alpine

RUN apk add --no-cache curl

COPY ./docker/prod/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

COPY --from=builder /app/public ./public

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s CMD curl --fail http://pharm.test/health || exit 1
