# Médiathèque PHP

Une application web simple de gestion de médiathèque développée en PHP orienté objet avec architecture MVC. Elle permet de gérer des livres, films et albums, avec fonctionnalités d’emprunt, retour, ajout, modification et suppression.

---

##  Fonctionnalités

-  Inscription et connexion utilisateur
-  Ajout de médias : livres, films, albums
-  Recherche et tri des médias
-  Emprunter / Rendre un média
-  Upload d’illustrations
-  Interface administrateur avec tableau de bord
-  Architecture MVC (Modèle-Vue-Contrôleur)
-  Interface stylisée avec Tailwind CSS

---

##  Technologies utilisées

- PHP 8+
- MySQL 
- Tailwind.CSS 
- HTML5 / CSS3
- PDO pour la base de données

---

##  Structure du projet

/projet
/controllers
    MediaController.php
    LivreController.php
/models
│ Media.php
│ Livre.php

/views
│ dashboard
│ edit
│ show
│ login
│ register

/assets
│   /uploads

index.php       
.htaccess       



---

##  Installation

1. **Cloner le projet** dans votre serveur local XAMPP :
   git clone https://github.com/votre-utilisateur/mediatheque-php.git

2. Créer la base de données MySQL :
   CREATE DATABASE mediatheque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

3. Importer le fichier SQL
4. Configurer la connexion dans /config/Database.php :
5. Lancer l’application via http://localhost/mediatheque/index.php

Nom utilisateur : tintin

Mdp : Milou1760*
