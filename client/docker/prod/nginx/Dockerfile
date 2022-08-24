FROM node:17-alpine as builder

WORKDIR /app

COPY ./package.json ./yarn.lock ./

RUN yarn install && yarn cache clean

COPY ./ ./

RUN yarn build

FROM nginx:1.21-alpine

RUN apk add --no-cache curl

COPY ./docker/prod/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

COPY --from=builder /app/next.config.js ./
COPY --from=builder /app/public ./public
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static
