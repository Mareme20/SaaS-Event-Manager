# Stage 1: Composer (Backend Dependencies)
FROM php:8.2-fpm-alpine AS composer-builder

# Install system dependencies for Composer
RUN apk add --no-cache \
    zip \
    libzip-dev \
    unzip \
    git \
    oniguruma-dev \
    curl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files
COPY composer*.json ./

# Install PHP dependencies
# Note: --no-scripts is used here to avoid issues with missing files during the initial install
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy the rest of the application
COPY . .

# Finish composer install with autoloader and scripts
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Node.js (Frontend)
FROM node:20-alpine AS frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
# Copy vendor from composer-builder for Ziggy
COPY --from=composer-builder /app/vendor ./vendor
RUN npm run build

# Stage 3: PHP (Final)
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
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
