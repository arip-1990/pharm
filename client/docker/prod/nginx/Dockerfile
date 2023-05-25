FROM node:20-alpine as builder

WORKDIR /app

COPY ./package.json ./yarn.lock ./

RUN yarn install && yarn cache clean

COPY ./ ./

RUN yarn build

FROM nginx:1.25-alpine

RUN apk add --no-cache curl

COPY ./docker/prod/nginx/conf.d /etc/nginx/conf.d

WORKDIR /app

RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

COPY --from=builder /app/next.config.js ./
COPY --from=builder /app/public ./public
COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static

HEALTHCHECK --interval=5s --timeout=3s --start-period=1s CMD curl --fail http://127.0.0.1/health || exit 1
