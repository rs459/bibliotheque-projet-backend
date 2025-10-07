#!/bin/bash
set -e # Arrête immédiatement le script si une commande échoue

echo "🚀 Lancement du script de post-déploiement..."

# Applique les migrations de la base de données pour l'environnement de production.
# L'option --no-interaction est cruciale pour un script automatisé.
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Vide le cache de l'application pour l'environnement de production.
php bin/console cache:clear --env=prod

echo "✅ Déploiement terminé avec succès !"
