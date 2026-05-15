FROM php:8.2-cli-alpine

ARG CACHEBUST=4

RUN apk add --no-cache \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-install pdo_mysql bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN npm install && npm run build || true

# Forcer les permissions totales sur le dossier de stockage de Laravel
RUN mkdir -p storage/logs && chmod -R 777 storage bootstrap/cache

EXPOSE 8080
CMD php artisan migrate --force && php -S 0.0.0.0:8080 -t public
