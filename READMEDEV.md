
# Projet Veliko : DEV 

## Installation

### Étape 1 : Cloner le projet
Cloner le dépôt Git du projet :
```bash
git clone  git@github.com:ort-montreuil/BTS-SIO-G3-2025-VELIKO-Web.git
```

### Étape 2 : Installation des dépendances
Installer les dépendances avec Composer :
```bash
composer install
```

### Étape 3 : Configuration du fichier .env
Créer un fichier `.env` à la racine du projet, le renommer en `.env`, puis modifier la ligne 3 pour remplacer `!ChangeMe!` par votre identifiant et mot de passe de base de données.

### Étape 4 : Création de la base de données
Démarrer les services Docker pour configurer la base de données :
```bash
docker compose up -d
```

### Étape 5 : Migration de la base de données
Lancer la migration pour créer les tables nécessaires :
```bash
php bin/console doctrine:migrations:migrate
```

### Étape 6 : Remplir la base de données avec les stations
Exécuter la commande pour insérer les stations dans la base de données :
```bash
php bin/console pp:fetch-stations

```

### Étape 7 : Lancer le serveur de développement
Démarrer le serveur Symfony :
```bash
symfony serve:start
```

