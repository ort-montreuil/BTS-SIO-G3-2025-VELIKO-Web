
# Projet Veliko : DEV 

**Membres de l'équipe :**

- Shirel Marciano, Maxime Benoit Legrand (coté DEV) 
- Aston Montabord , Aviel Israel (coté Réseau)


## Installation

### Étape 1 : Cloner le projet
Cloner le dépôt Git du projet en ssh ou https : 
```bash
 git clone  git@github.com:ort-montreuil/BTS-SIO-G3-2025-VELIKO-Web.git
```
```bash
git clone  https://github.com/ort-montreuil/BTS-SIO-G3-2025-VELIKO-Web.git
```

### Etape 2 : Configuration du fichier .env
Créer un fichier `.env` à la racine du projet, le renommer en `.env`, puis modifier la ligne 3 pour remplacer `!ChangeMe!` par votre identifiant et mot de passe de base de données.
Voir le .env.example pour mieux comprendre .


### Étape 3 : Installation des dépendances
Installer les dépendances avec Composer :
```bash
composer install
```


### Étape 4 : Création de la base de données
Démarrer les services Docker pour configurer la base de données :
```bash
docker compose up -d
```

### Étape 5 : Migration de la base de données
Lancer la migration pour créer les tables nécessaires :
```bash
symfony console doctrine:migrations:migrate
```


### Étape 6 : Remplir la base de données avec les stations
Exécuter la commande pour insérer les stations dans la base de données :
```bash
symfony console doctrine:fixtures:load
```
### pour se connecter voici les identifiants :
- email : user-0@gmail.com
- mot de passe : Motdepasse123//


### Etape 7 : Mettre les stations dans la base de donnée
````bash
php bin/console app:fetch-stations  # pour mettre les stations dans la base de donnée
````

### Étape 8 : Lancer le serveur de développement
Démarrer le serveur Symfony :
```bash
symfony serve:start
```


# Projet Veliko : PROD

## Installation

### Étape 1 : Configuration du fichier .env-local
  APP_ENV=prod
  APP_DEBUG=0
```bash
- composer dump-env prod
- composer dump-env prod --empty
```
### Étape 3 : Installations et Mises à jour  des dépendances
Installation et MAJ avec Composer :
```bash
composer install --no-dev --optimize-autoloader
```

### Étape 4 : Nettoyer le cache
```bash
APP_ENV=prod APP_DEBUG=0 php bin/console cache:clears
```

