# 📚 BIBLIOTECH - Documentation du Projet

## Vue d'ensemble
BiblioTech est un système de gestion de bibliothèque communautaire développé avec **PHP natif** et **MySQL**.

---

## 📁 Structure du Projet

```
bibliotech/
│
├── database/
│   └── bibliotech.sql          # Script SQL complet de la base de données
│
├── backend/
│   ├── config/
│   │   └── database.php        # Configuration de connexion à la BDD
│   │
│   └── models/
│       ├── Utilisateur.php     # Modèle pour la gestion des utilisateurs
│       ├── Livre.php           # Modèle pour la gestion des livres
│       └── Emprunt.php         # Modèle pour la gestion des emprunts
│
├── maquettes/
│   ├── 01_accueil.html         # Maquette page d'accueil
│   ├── 02_catalogue.html       # Maquette catalogue
│   └── 03_dashboard_membre.html # Maquette tableau de bord membre
│
└── README.md                    # Ce fichier
```

---

## 🗄️ Base de Données

### Tables principales :
1. **utilisateurs** - Membres, bibliothécaires et administrateurs
2. **categories** - Catégories de livres
3. **livres** - Catalogue des livres
4. **emprunts** - Gestion des emprunts et retours
5. **reservations** - Réservations de livres

### Fonctionnalités automatiques :
- ✅ Triggers pour gérer automatiquement les exemplaires disponibles
- ✅ Mise à jour automatique du statut en retard
- ✅ Vues SQL pour statistiques et rapports
- ✅ Procédures stockées pour opérations complexes

---

## 🔧 Installation

### Prérequis :
- **XAMPP** ou **WAMP** ou **MAMP** (contient Apache + MySQL + PHP)
- **PHP 7.4+**
- **MySQL 5.7+**

### Étapes d'installation :

#### 1. Installer XAMPP
- Télécharger : [https://www.apachefriends.org](https://www.apachefriends.org)
- Installer et démarrer Apache et MySQL

#### 2. Créer la base de données
```bash
1. Ouvrir phpMyAdmin : http://localhost/phpmyadmin
2. Créer une nouvelle base de données nommée "bibliotech"
3. Importer le fichier : database/bibliotech.sql
```

#### 3. Configurer la connexion
Modifier `backend/config/database.php` si nécessaire :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'bibliotech');
define('DB_USER', 'root');
define('DB_PASS', '');  // Vide par défaut sur XAMPP
```

#### 4. Placer les fichiers
```bash
- Copier le dossier du projet dans : C:/xampp/htdocs/bibliotech/
```

#### 5. Accéder au site
```
http://localhost/bibliotech/
```

---

## 👥 Comptes de Test

### Administrateur :
- **Email** : admin@bibliotech.bj
- **Mot de passe** : password123

### Bibliothécaire :
- **Email** : marie.koffi@bibliotech.bj
- **Mot de passe** : password123

### Membre :
- **Email** : jean.dupont@email.bj
- **Mot de passe** : password123

---

## 🎨 Architecture Backend (MVC Simplifié)

### Models (Modèles)
Chaque modèle représente une table et contient toutes les opérations CRUD :

**Utilisateur.php**
- `creer()` - Créer un nouveau compte
- `connexion()` - Authentification
- `getById()` - Récupérer un utilisateur
- `updateProfil()` - Modifier le profil
- `changerStatut()` - Activer/Désactiver un compte

**Livre.php**
- `getTous()` - Liste de tous les livres
- `getById()` - Détails d'un livre
- `creer()` - Ajouter un livre
- `update()` - Modifier un livre
- `rechercher()` - Recherche de livres

**Emprunt.php**
- `creer()` - Créer un emprunt
- `enregistrerRetour()` - Retourner un livre
- `getParUtilisateur()` - Emprunts d'un membre
- `getTousActifs()` - Tous les emprunts en cours
- `getEnRetard()` - Emprunts en retard

### Controllers (à créer)
Les contrôleurs géreront les requêtes HTTP et appelleront les modèles.

### Views (à créer)
Les vues afficheront les données aux utilisateurs (HTML/PHP).

---

## 🔐 Sécurité

### Mesures implémentées :
- ✅ Mots de passe hashés avec `password_hash()` (BCRYPT)
- ✅ Requêtes préparées (PDO) contre les injections SQL
- ✅ Nettoyage des données avec `htmlspecialchars()`
- ✅ Validation côté serveur
- ✅ Gestion des sessions sécurisée

---

## 📊 Règles de Gestion

### Emprunts :
- Durée : **14 jours**
- Maximum simultané : **3 livres par membre**
- Blocage si retard existant

### Réservations :
- Maximum : **1 réservation active** par membre
- Validité : **7 jours** après disponibilité
- Possible uniquement si livre emprunté

### Membres :
- Inscription nécessite validation d'un bibliothécaire
- Désactivation automatique après 6 mois d'inactivité

---

## 🚀 Prochaines Étapes de Développement

1. ✅ Base de données - **TERMINÉ**
2. ✅ Modèles PHP - **TERMINÉ**
3. ⏳ Créer les contrôleurs (Controllers)
4. ⏳ Créer les vues (Pages PHP)
5. ⏳ Système d'authentification complet
6. ⏳ Interface administrateur
7. ⏳ Tests et débogage
8. ⏳ Déploiement

---

## 📝 Exemple d'utilisation des Modèles

### Connexion d'un utilisateur :
```php
<?php
require_once 'backend/config/database.php';
require_once 'backend/models/Utilisateur.php';

$database = new Database();
$db = $database->getConnection();

$utilisateur = new Utilisateur($db);
$utilisateur->email = "jean.dupont@email.bj";
$utilisateur->mot_de_passe = "password123";

$result = $utilisateur->connexion();

if ($result) {
    // Connexion réussie
    $_SESSION['user'] = $result;
    header("Location: dashboard.php");
} else {
    // Échec de connexion
    echo "Email ou mot de passe incorrect";
}
?>
```

### Rechercher des livres :
```php
<?php
require_once 'backend/config/database.php';
require_once 'backend/models/Livre.php';

$database = new Database();
$db = $database->getConnection();

$livre = new Livre($db);

// Recherche
$livres = $livre->rechercher("Camus");

// Affichage
foreach ($livres as $livre) {
    echo $livre['titre'] . " - " . $livre['auteur'] . "<br>";
}
?>
```

### Créer un emprunt :
```php
<?php
require_once 'backend/config/database.php';
require_once 'backend/models/Emprunt.php';

$database = new Database();
$db = $database->getConnection();

$emprunt = new Emprunt($db);
$emprunt->utilisateur_id = 3;  // Jean Dupont
$emprunt->livre_id = 1;        // L'Étranger
$emprunt->enregistre_par = 2;  // Marie (bibliothécaire)

$result = $emprunt->creer();

if ($result === true) {
    echo "Emprunt créé avec succès !";
} else {
    echo "Erreur : " . $result;
}
?>
```

---

## 🛠️ Outils Recommandés

- **Éditeur** : Visual Studio Code
- **Serveur local** : XAMPP
- **Base de données** : phpMyAdmin
- **Gestionnaire Git** : GitHub Desktop
- **Navigateur** : Chrome/Firefox (avec DevTools)

---

## 📚 Ressources

- [Documentation PHP](https://www.php.net/manual/fr/)
- [Documentation MySQL](https://dev.mysql.com/doc/)
- [PDO Tutorial](https://www.php.net/manual/fr/book.pdo.php)
- [Bootstrap 5](https://getbootstrap.com/docs/5.0/)

---

## ✨ Fonctionnalités Implémentées

### Backend :
- ✅ Connexion base de données (PDO)
- ✅ Modèle Utilisateur (CRUD complet)
- ✅ Modèle Livre (CRUD complet)
- ✅ Modèle Emprunt (gestion complète)
- ✅ Sécurité (hash, requêtes préparées)
- ✅ Règles métier (limites, vérifications)

### Base de données :
- ✅ 5 tables principales
- ✅ Relations et contraintes
- ✅ Triggers automatiques
- ✅ Vues SQL
- ✅ Données de test

### Design :
- ✅ 3 maquettes HTML/CSS
- ✅ Design responsive
- ✅ Palette de couleurs professionnelle

---

## 📧 Support

Pour toute question sur le projet :
- **Email** : [Votre email]
- **École** : EIG Bénin

---

**Projet réalisé dans le cadre de la certification EIG 2026**  
**Filière** : Développement Web  
**Auteur** : [Votre Nom]

---

© 2026 BiblioTech - Tous droits réservés
