FROM php:8.2-cli

# Installation des outils nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    nodejs \
    npm

# Installation de Composer de manière sécurisée
RUN curl -sS https://getcomposer.org | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . .

# Installation des dépendances du projet
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs || true
RUN npm install && npm run build || true

EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
