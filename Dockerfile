FROM php:8.2-apache

# 1. Installation des extensions Linux et PHP indispensables à Laravel
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 2. Importation sécurisée et native de Composer depuis son image Docker officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Résolution de l'erreur MPM Apache
RUN a2dismod mpm_event || true && a2enmod mpm_prefork || true

# 4. Activation du module de réécriture Apache pour les routes Laravel
RUN a2enmod rewrite

# 5. Modification de la racine vers le dossier public obligatoire de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Configuration dynamique du port Apache requis par Railway
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .

# 7. Installation des modules PHP de votre application Laravel
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 8. Build des assets front-end (Vite / Mix / Tailwind)
RUN npm install && npm run build || true

# 9. Attribution des permissions système pour le stockage Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENV PORT=80
EXPOSE ${PORT}

CMD ["apache2-foreground"]
