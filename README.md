
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
Créez un fichier .env à la racine du projet en copiant le contenu du fichier .env.example, puis modifiez la ligne contenant les informations de connexion à la base de données en y renseignant votre identifiant et votre mot de passe ainsi que le nom de la table . 
(Vous pouvez consulter le fichier .env.example pour mieux comprendre.)


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
### pour se connecter en tant qu'utilisateur voici les identifiants :
- email : user-0@gmail.com
- mot de passe : Motdepasse123/


### Etape 7 : Mettre les stations dans la base de donnée
````bash
php bin/console app:fetch-stations  # pour mettre les stations dans la base de donnée
````

### Étape 8 : Lancer le serveur de développement
Démarrer le serveur Symfony :
```bash
symfony serve:start
```



