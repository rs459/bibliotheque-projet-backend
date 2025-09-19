# expoBiblio-backend

Ce dépôt contient le backend de l'application expoBiblio, développé avec le framework Symfony. Il fournit une API REST pour gérer les livres, auteurs et éditeurs d'une bibliothèque.

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les outils suivants sur votre machine :

- PHP (version 8.1 ou supérieure)
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download)
- Un serveur de base de données (par exemple, MySQL, MariaDB, PostgreSQL)

## Installation et Configuration

Suivez ces étapes pour mettre en place l'environnement de développement.

### 1. Installation des dépendances

Une fois que vous avez cloné le projet et que vous êtes dans le répertoire `bibliotheque-projet-backend`, installez les dépendances PHP avec Composer :

```bash
composer install
```

### 2. Configuration de l'environnement

Le projet utilise un fichier `.env` pour gérer les variables d'environnement. Pour la configuration locale, il est recommandé de créer un fichier `.env.local` qui ne sera pas suivi par Git.

Copiez le fichier `.env` existant :

```bash
cp .env .env.local
```

Ensuite, modifiez le fichier `.env.local` pour configurer votre base de données. La variable la plus importante est `DATABASE_URL`.

**Exemple pour MySQL/MariaDB :**

```text
# .env.local
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=mariadb-10.4.27&charset=utf8mb4"
```

**Exemple pour PostgreSQL :**

```text
# .env.local
DATABASE_URL="postgresql://db_user:db_password@127.0.0.1:5432/db_name?serverVersion=16&charset=utf8"
```

Remplacez `db_user`, `db_password` et `db_name` par vos propres informations. Assurez-vous que la base de données (`db_name`) existe sur votre serveur SGBD.

### 3. Mise en place de la base de données

Une fois votre fichier `.env.local` configuré, vous pouvez créer la base de données et appliquer les schémas.

1. **Créer la base de données (si elle n'existe pas déjà) :**

    ```bash
    php bin/console doctrine:database:create
    ```

2. **Appliquer les migrations :**

    Cette commande créera les tables (`author`, `editor`, `book`) dans votre base de données.

    ```bash
    php bin/console doctrine:migrations:migrate
    ```

### 4. Charger les données de test (Fixtures)

Le projet inclut des "fixtures" pour peupler la base de données avec des données de test générées aléatoirement.

Pour charger ces données, exécutez la commande suivante. **Attention, cette commande purge toutes les données existantes dans les tables concernées.**

```bash
php bin/console doctrine:fixtures:load
```

## Lancer le serveur de développement

Vous pouvez maintenant lancer le serveur web local de Symfony :

```bash
symfony server:start
```

Votre API sera accessible à l'adresse indiquée dans le terminal (généralement `https://127.0.0.1:8000`).
