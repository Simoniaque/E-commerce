# Ouvrir la Documentaton tehnique.

## Rappel des instructions

### Si le dossier vendor est déjà présent :
 - Supprimer le dossier vendor
 - Ouvrir le terminal et saisir "composer install" pour installer les dépendances du projet

### Packets utilisés :
 - microsoft/azure-storage-blob : 1.5.4
 - mailjet/mailjet-apiv3-ph : 1.6.3
 
### Base de données :
- Créer une base MySql "newvet" puis importer le script "newvet.sql"

### Mail Jet :
- Créer un compte Mailjet

### Azure Blop :
- Ajouter un container sur Azure avec 3 blobs publiques

### Fichier config.php :
- Modifier les acces pour azure et mailjet depuis le fichier config.php
