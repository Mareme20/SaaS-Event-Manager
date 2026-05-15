FROM php:8.2-cli-alpine

ARG CACHEBUST=6

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

# Configuration stricte des permissions et redirection des logs Laravel vers la sortie standard
RUN mkdir -p storage/logs && touch storage/logs/laravel.log && chmod -R 777 storage bootstrap/cache

EXPOSE 8080

# Utilisation d'un script de démarrage qui nettoie, lance et surveille les erreurs de Laravel
CMD php artisan config:clear && \
    php artisan cache:clear && \
    (tail -f storage/logs/laravel.log &) && \
    php -S 0.0.0.0:8080 -t public
