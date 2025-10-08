# Utilise une image PHP 8.3 officielle
FROM php:8.3-fpm-alpine

# Met à jour les paquets pour corriger les vulnérabilités
RUN apk update && apk upgrade

# Installe les dépendances système et les extensions PHP nécessaires
RUN apk add --no-cache $PHPIZE_DEPS mariadb-client
RUN docker-php-ext-install pdo pdo_mysql

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définit le répertoire de travail
WORKDIR /app

# Copie le code de l'application
COPY . .

RUN touch .env
