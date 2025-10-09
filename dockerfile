# Utilise une image PHP 8.3 officielle
FROM php:8.3-fpm-alpine

# Installe les dépendances système et les extensions PHP nécessaires
RUN apk update && apk upgrade && apk add --no-cache \
    $PHPIZE_DEPS \
    mariadb-client
RUN docker-php-ext-install pdo pdo_mysql

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définit le répertoire de travail
WORKDIR /app

# 1. Copier UNIQUEMENT les fichiers de dépendances
COPY composer.json composer.lock ./

# 2. Installer les dépendances SANS exécuter de scripts
RUN composer install --no-scripts --no-interaction --optimize-autoloader

# 3. Copier le reste du code de l'application
COPY . .

# 4. AJOUT : Créer un fichier .env vide pour que le noyau Symfony puisse démarrer
RUN touch .env

# 5. Générer l'autoloader optimisé
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative
