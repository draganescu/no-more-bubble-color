FROM node:20-alpine AS frontend
WORKDIR /build/app
COPY ./app/package.json ./app/package-lock.json* ./
RUN npm install
COPY ./app/ .
RUN npm run build

FROM dunglas/frankenphp:latest-php8.3
RUN install-php-extensions pdo_sqlite
COPY ./server /app/server
COPY ./public /app/landing
COPY --from=frontend /build/app/dist /app/public
COPY Caddyfile /etc/caddy/Caddyfile
ENV MERCURE_PUBLISHER_JWT_KEY='!ChangeMe!'
ENV MERCURE_SUBSCRIBER_JWT_KEY='!ChangeMe!'
