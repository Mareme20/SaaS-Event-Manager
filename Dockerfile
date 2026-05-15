FROM php:8.2-fpm-alpine

ARG CACHEBUST=19

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

# Création du vrai dossier physique à la place du lien symbolique
RUN rm -rf public/storage && mkdir -p public/storage storage/app/public
RUN chmod -R 777 storage bootstrap/cache public/storage

RUN echo ':{env.PORT} {' > /etc/Caddyfile && \
    echo '    root * /var/www/html/public' >> /etc/Caddyfile && \
    echo '    php_fastcgi 127.0.0.1:9000' >> /etc/Caddyfile && \
    echo '    file_server' >> /etc/Caddyfile && \
    echo '}' >> /etc/Caddyfile

EXPOSE 8080

# Script en tâche de fond qui copie en continu les images reçues vers le dossier accessible par Caddy
CMD cp -n .env.example .env || true && \
    php artisan key:generate --force && \
    php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force && \
    (while true; do cp -r storage/app/public/* public/storage/ 2>/dev/null || true; sleep 2; done &) && \
    php-fpm -D && caddy run --config /etc/Caddyfile --adapter caddyfile
