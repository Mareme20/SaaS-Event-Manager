FROM php:8.2-cli-alpine

ARG CACHEBUST=7

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

RUN mkdir -p storage/logs && touch storage/logs/laravel.log && chmod -R 777 storage bootstrap/cache

EXPOSE 8080

# Génération automatique de l'APP_KEY si manquante, nettoyage des caches, migrations puis démarrage
CMD php artisan key:generate --force && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    php -S 0.0.0.0:8080 -t public
