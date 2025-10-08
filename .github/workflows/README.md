# Workflow de Déploiement Continu (CI/CD)

Créer un repertoire `.github` à la racine, un sous-repertoire `worflows` et placer un fichier `yml`

Ce document explique le fonctionnement du workflow de déploiement continu (`deploy.yml`) pour l'API Symfony sur l'hébergement O2Switch.

## 📖 Principe de fonctionnement

Le workflow est déclenché automatiquement à chaque `push` sur la branche `deploy`. Il exécute une série d'étapes pour préparer et déployer l'application en production de manière sécurisée.

Les grandes étapes sont :

1. **Autorisation d'accès** : Le workflow récupère l'adresse IP du serveur d'exécution (le "runner" GitHub) et l'ajoute temporairement à la liste blanche du pare-feu SSH de cPanel. Cela permet aux étapes suivantes de se connecter au serveur.

2. **Préparation de l'application** :
    - Le code source est récupéré.
    - PHP et Composer sont installés.
    - Les fichiers de configuration de production (`.env.local`, clés JWT) sont créés à partir des secrets GitHub.
    - Les dépendances PHP sont installées en mode production (`composer install --no-dev`).

3. **Déploiement** :
    - L'intégralité du projet préparé est envoyée sur le serveur O2Switch via SCP, en écrasant la version précédente.
    - Un script `deploy.sh` (qui doit exister à la racine de votre projet sur le serveur) est exécuté pour finaliser l'installation (ex: vider le cache, lancer les migrations Doctrine).

    ```#!/bin/bash deploy.sh
        set -e # Arrête immédiatement le script si une commande échoue

        echo "🚀 Lancement du script de post-déploiement..."

        # Applique les migrations de la base de données pour l'environnement de production.
        # L'option --no-interaction est cruciale pour un script automatisé.
        php bin/console doctrine:migrations:migrate --no-interaction --env=prod

        # Vide le cache de l'application pour l'environnement de production.
        php bin/console cache:clear --env=prod

        echo "✅ Déploiement terminé avec succès !"```

4. **Nettoyage** : L'adresse IP du runner est systématiquement retirée du pare-feu SSH, que le déploiement ait réussi ou échoué, pour ne laisser aucune porte d'accès ouverte.

## 🔐 Secrets Requis

Pour que ce workflow fonctionne, vous devez configurer les "Repository secrets" dans les paramètres de votre dépôt GitHub (`Settings > Secrets and variables > Actions`).

Voici la liste des secrets nécessaires et comment obtenir leurs valeurs :

| Secret | Description | Où le trouver |
| :--- | :--- | :--- |
| `CPANEL_USERNAME` | Votre nom d'utilisateur cPanel. | Fourni dans votre email d'accueil O2Switch. |
| `CPANEL_API_TOKEN` | Un jeton d'API pour interagir avec cPanel. | Dans cPanel, allez dans "Gérer les jetons d'API", créez un nouveau jeton et copiez-le. |
| `CPANEL_SERVER` | Le nom d'hôte de votre serveur. | Fourni dans votre email d'accueil O2Switch (ex: `moncompte.o2switch.net`). |
| `SSH_PRIVATE_KEY` | La clé SSH privée pour se connecter au serveur sans mot de passe. | C'est la partie privée de la paire de clés que vous avez générée. La clé publique correspondante doit être autorisée dans cPanel ("Accès SSH"). |
| `TARGET_DIR` | Le chemin absolu du répertoire de destination sur le serveur. | Exemple : `/home/moncompte/public_html/api-biblio` |
| `DATABASE_URL` | L'URL de connexion à la base de données de production. | Format : `mysql://user:password@127.0.0.1:3306/db_name?serverVersion=mariadb-10.4.27` |
| `APP_SECRET` | Une chaîne de caractères aléatoire et secrète pour sécuriser l'application Symfony. | Vous pouvez en générer une avec `php -r 'echo bin2hex(random_bytes(16));'` |
| `JWT_PASSPHRASE` | La phrase secrète utilisée pour générer les clés JWT. | Choisissez une phrase secrète robuste. |
| `JWT_PRIVATE_KEY` | Le contenu de votre clé JWT privée. | Copiez le contenu du fichier `config/jwt/private.pem` généré localement. |
| `JWT_PUBLIC_KEY` | Le contenu de votre clé JWT publique. | Copiez le contenu du fichier `config/jwt/public.pem` généré localement. |

### Comment générer les clés JWT localement

Si vous n'avez pas encore les clés, exécutez ces commandes dans votre projet Symfony en local :

```bash
# 1. Créer le répertoire s'il n'existe pas
mkdir -p config/jwt

# 2. Générer les clés
php bin/console lexik:jwt:generate-keypair
```

Utilisez ensuite le contenu des fichiers `config/jwt/private.pem` et `config/jwt/public.pem` pour les secrets `JWT_PRIVATE_KEY` et `JWT_PUBLIC_KEY`.

---
