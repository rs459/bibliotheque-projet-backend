#!/bin/bash
# deploy.sh (version finale et normale)
set -e

echo "Lancement du script de déploiement..."

# Appliquer les migrations sur la base de données vide
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Vider le cache
php bin/console cache:clear --env=prod

echo "Déploiement terminé."
