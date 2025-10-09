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

# 2. CORRIGÉ : On fournit un secret temporaire pour permettre aux scripts de s'exécuter
#    Le secret n'a pas besoin d'être le vrai, il doit juste exister.
#    On enlève --no-scripts pour que l'autoloader de PHPUnit soit correctement généré.
RUN APP_SECRET=dummysecretforbuild DATABASE_URL=mysql://dummy:dummy@dummy/dummy composer install --no-interaction --optimize-autoloader

# 3. Copier le reste du code de l'application
COPY . .

# 4. Créer un fichier .env vide pour que le noyau Symfony puisse démarrer dans les commandes 'exec'
RUN touch .env

# SUPPRIMÉ : Le 'dump-autoload' est maintenant inclus dans 'composer install'
