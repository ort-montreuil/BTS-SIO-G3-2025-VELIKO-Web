
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

