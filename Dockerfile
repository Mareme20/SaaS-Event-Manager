FROM php:8.2-cli-alpine

# 1. Installation des dépendances système et extensions PHP requises par Laravel
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

# 2. Importation de Composer depuis son conteneur officiel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# 3. Installation des dépendances PHP et compilation du Front-End (Vite/Tailwind)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN npm install && npm run build || true

# 4. Configuration des permissions d'écriture pour Laravel
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-80} -t public"]
