# Utilise une image PHP 8.3 officielle
FROM php:8.3-fpm-alpine

# Installe les dépendances système et les extensions PHP nécessaires
# On le fait au début car cela change rarement
RUN apk update && apk upgrade && apk add --no-cache \
    $PHPIZE_DEPS \
    mariadb-client
RUN docker-php-ext-install pdo pdo_mysql

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définit le répertoire de travail
WORKDIR /app

# --- ÉTAPE CLÉ DE L'OPTIMISATION ---
# 1. Copier UNIQUEMENT les fichiers de dépendances
COPY composer.json composer.lock ./

# 2. Installer les dépendances. Cette couche sera mise en cache tant que composer.lock ne change pas.
#    --no-scripts est important pour ne pas lancer de scripts qui auraient besoin du reste du code.
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

# 3. Copier le reste du code de l'application
#    Un changement ici n'invalidera que cette couche, pas le "composer install" du dessus.
COPY . .

# 4. Exécute les scripts Composer (comme la génération du cache de Symfony)
RUN composer run-script post-install-cmd --no-dev
