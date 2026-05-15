FROM php:8.2-fpm-alpine

ARG CACHEBUST=12

# 1. Installation des extensions PHP requises et de Caddy
RUN apk add --no-cache \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    caddy \
    && docker-php-ext-install pdo_mysql bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN npm ci && npm run build || true

RUN mkdir -p storage/logs && chmod -R 777 storage bootstrap/cache

# 2. Configuration instantanée de Caddy pour Laravel
RUN echo ':$PORT {' > /etc/Caddyfile && \
    echo '    root * /var/www/html/public' >> /etc/Caddyfile && \
    echo '    php_fastcgi 127.0.0.1:9000' >> /etc/Caddyfile && \
    echo '    file_server' >> /etc/Caddyfile && \
    echo '}' >> /etc/Caddyfile

EXPOSE 8080

# 3. Démarrage de PHP-FPM en tâche de fond + Serveur Caddy
CMD cp -n .env.example .env || true && \
    php artisan key:generate --force && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    php-fpm -D && caddy run --config /etc/Caddyfile --adapter caddyfile
