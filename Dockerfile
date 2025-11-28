FROM php:8.5-cli-alpine

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache git zip unzip

COPY . .

RUN composer install --no-interaction --optimize-autoloader

CMD ["php", "-v"]
