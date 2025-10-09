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

# --- SÉQUENCE CORRIGÉE ---

# 1. Copier UNIQUEMENT les fichiers de dépendances
COPY composer.json composer.lock ./

# 2. Installer les dépendances SANS exécuter de scripts.
#    Ceci crée une couche de cache stable pour vos vendors.
RUN composer install --no-scripts --no-interaction --optimize-autoloader

# 3. Copier le reste du code de l'application (incluant bin/console)
COPY . .

# 4. Exécuter les scripts MAINTENANT que tout le code est présent.
#    On fournit les variables temporaires nécessaires pour que ça ne plante pas.
RUN APP_SECRET=dummysecretforbuild DATABASE_URL=mysql://dummy:dummy@dummy/dummy composer run-script post-install-cmd

# 5. Créer un fichier .env vide pour les commandes 'exec' futures
RUN touch .env
