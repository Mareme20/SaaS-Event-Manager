FROM php:8.2-cli-alpine

ARG CACHEBUST=10

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

# Installation propre des dépendances PHP et JavaScript
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN npm ci

# Compilation forcée des assets pour la production (génère les fichiers CSS/JS)
RUN npm run build

# Configuration des permissions globales
RUN mkdir -p storage/logs && touch storage/logs/laravel.log && chmod -R 777 storage bootstrap/cache

EXPOSE 8080

# Forcer la réinitialisation complète de la configuration au démarrage
CMD cp -n .env.example .env || true && \
    php artisan key:generate --force && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear && \
    php artisan route:clear && \
    php artisan migrate --force && \
    php -S 0.0.0.0:8080 -t public public/index.php
