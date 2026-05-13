# Stage 1: Base PHP with extensions
FROM php:8.2-fpm-alpine AS php-base

# Install system dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    unzip \
    git \
    oniguruma-dev \
    curl \
    icu-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring gd zip bcmath intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

# Stage 2: Composer (Backend Dependencies)
FROM php-base AS composer-builder
WORKDIR /app

# Copy composer files - use explicit names to ensure lock file is included
COPY composer.json composer.lock* ./

# Install PHP dependencies
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Finish composer install with autoloader and scripts
RUN composer install --no-dev --optimize-autoloader

# Stage 3: Node.js (Frontend)
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
# Copy vendor from composer-builder for Ziggy
COPY --from=composer-builder /app/vendor ./vendor
RUN npm run build

# Stage 4: PHP (Final)
FROM php-base

# Install Nginx
RUN apk add --no-cache nginx

WORKDIR /var/www/html

# Copy project files
COPY . .

# Copy composer vendor from builder
COPY --from=composer-builder /app/vendor ./vendor

# Copy built assets from frontend stage
COPY --from=frontend-builder /app/public/build ./public/build

# Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Nginx config
COPY deployment/nginx.conf /etc/nginx/http.d/default.conf

# Entrypoint script
COPY deployment/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
