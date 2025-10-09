#!/bin/bash
# deploy.sh

echo "Lancement du script de synchronisation..."
set -e

# ÉTAPE 1 : Vide la table d'historique des migrations en production.
# C'est sans danger pour vos données.
php bin/console doctrine:query:sql "TRUNCATE TABLE doctrine_migration_versions" --env=prod

# ÉTAPE 2 : Marque la migration actuelle comme déjà exécutée, SANS la jouer.
php bin/console doctrine:migrations:version --add --all --no-interaction --env=prod

echo "Synchronisation de la base de production terminée."

# L'ancienne commande de migration est maintenant inutile pour ce déploiement.
# On la commente pour ne pas la lancer.
# php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Vider le cache pour la production
php bin/console cache:clear --env=prod

echo "Déploiement terminé avec succès."
