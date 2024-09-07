# Template projet CI/CD : Drupal

Ce repo contient le template pour toutes les applications dévélopé avec le framework **drupal**.

## Comment utiliser ce template

### Faire un fork du  template

Faite un clique sur le bouton fork pour créer votre repo.

### Déploiements du code source

Les sources du projet sont à déposer dans le repertoire **src**

### Créer les différentes branches du projet :

Par convention, nous utilisons deux branches supplémentaires en plus de la branche `master` créee par défaut : `staging`, `develop`

Example: 

```bash
git checkout -b staging
git push -u origin staging
```

```bash
git checkout -b develop
git push -u origin develop
```

### Créer les variables Projets

Les variables projets sont à définir dans **Settings** > **CI / CD** > **Secret Variables** :

Variable | Default value | Description
---------|---------------|-----------------------
`APP_NAME` | _obligatoire_ | Nom d'application
`APP_URL`  | _Optionnel_  | URL de l'application, plusieurs variables peuvent être défini en fonction de l'environnement

### Inventaire Ansible

Les groupes Ansible sont à renseigner en ajoutant les serveurs sur lesquels l'application doit être déployée dans le fichier **ansible/inventory/hosts** du projet.

Groupe Ansible | Description
---------------|--------------
`development` | Serveurs de développement
`staging`     | Serveurs de pré-production
`production`  | Serveurs de production

## Initier un déploiement

### Nomenclature

Pour déclencher sur une pipeline, 
il faut créer et pusher un tag avec la nomenclature obligatoire suivante:

Nom | Environnement
---------------|--------------
`dev-*` | Serveurs de développement
`stg-*` | Serveurs de pré-production
`prod-*` | Serveurs de production

le `*` étant un wildcard, il vous permet d'ajouter ce que vous voulez comme numéro de version, `dev-1.0`, `stg-1.0`, `prod-1.0` sont des valeurs possibles.

### Les tags

Git possède une fonctionnalité d'étiquettage qui permet de marquer des points clé au cours du développement. Il est surtout utilisé pour créer des numéro de version (1.0, v2.0) comme on en connait beaucoup en informatique. Nous allons utiliser cette fonctionnalité pour initier notre pipeline. 
Les tags sont liés à des commits ce qui signifie qu'il sera lié au dernier commit effectué.
pour plus d'informations sur les tags suivez le lien suivant [Les bases de l'étiquettage](https://git-scm.com/book/fr/v1/Les-bases-de-Git-%C3%89tiquetage)

### Créer un tag 

```bash
git tag -a dev-1.4 -m "my version 1.4"
```

### Pusher un tag 

```bash
git push origin dev-1.4
```

