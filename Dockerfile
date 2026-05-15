FROM php:8.2-cli-alpine

ARG CACHEBUST=8

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

# 1. Création du fichier .env à partir du fichier d'exemple si absent
# 2. Génération de la clé, nettoyage, migration et démarrage du serveur de développement
CMD cp -n .env.example .env || true && \
    php artisan key:generate --force && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    php -S 0.0.0.0:8080 -t public
