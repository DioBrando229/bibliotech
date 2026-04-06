# 🚀 GUIDE D'INSTALLATION RAPIDE - BIBLIOTECH

## ⚡ Installation en 5 minutes

### 1️⃣ **Installer XAMPP**
- Télécharger : https://www.apachefriends.org
- Installer et lancer XAMPP Control Panel
- Démarrer **Apache** et **MySQL**

### 2️⃣ **Placer les fichiers**
```
1. Copier tout le contenu du projet
2. Coller dans : C:\xampp\htdocs\bibliotech\
```

Votre structure devrait ressembler à :
```
C:\xampp\htdocs\bibliotech\
├── backend/
├── frontend/
├── database/
└── README.md
```

### 3️⃣ **Créer la base de données**
```
1. Ouvrir : http://localhost/phpmyadmin
2. Cliquer sur "Nouveau" (New)
3. Nom : bibliotech
4. Cliquer sur "Créer"
5. Onglet "Importer" (Import)
6. Choisir le fichier : database/bibliotech.sql
7. Cliquer sur "Exécuter"
```

### 4️⃣ **Configurer la connexion** (Facultatif)
Si vous avez un mot de passe MySQL :
```php
Fichier : backend/config/database.php

Modifier la ligne :
define('DB_PASS', 'VOTRE_MOT_DE_PASSE');
```

### 5️⃣ **Lancer le site**
```
Ouvrir dans votre navigateur :
http://localhost/bibliotech/frontend/index.php
```

---

## 🔐 **Comptes de test**

### Administrateur
- **Email** : admin@bibliotech.bj
- **Mot de passe** : password123

### Bibliothécaire
- **Email** : marie.koffi@bibliotech.bj
- **Mot de passe** : password123

### Membre
- **Email** : jean.dupont@email.bj
- **Mot de passe** : password123

---

## ✅ **Vérifier que tout fonctionne**

1. ✅ Page d'accueil s'affiche
2. ✅ Connexion fonctionne
3. ✅ Inscription fonctionne
4. ✅ Catalogue affiche les livres
5. ✅ Données de test présentes

---

## ❌ **Problèmes courants**

### Erreur "Base de données introuvable"
→ Vérifier que la base "bibliotech" existe dans phpMyAdmin

### Page blanche
→ Vérifier que Apache et MySQL sont démarrés dans XAMPP

### Erreur de connexion
→ Vérifier backend/config/database.php

### CSS ne charge pas
→ Vérifier le chemin : frontend/assets/css/style.css existe

---

## 📁 **Structure finale du projet**

```
bibliotech/
│
├── backend/
│   ├── config/
│   │   └── database.php         # Configuration BDD
│   ├── models/
│   │   ├── Utilisateur.php      # Modèle utilisateur
│   │   ├── Livre.php            # Modèle livre
│   │   └── Emprunt.php          # Modèle emprunt
│   └── init.php                 # Initialisation + fonctions
│
├── frontend/
│   ├── assets/
│   │   └── css/
│   │       └── style.css        # Styles personnalisés
│   ├── includes/
│   │   ├── header.php           # En-tête
│   │   └── footer.php           # Pied de page
│   ├── index.php                # Page d'accueil
│   ├── connexion.php            # Connexion
│   ├── inscription.php          # Inscription
│   └── deconnexion.php          # Déconnexion
│
├── database/
│   └── bibliotech.sql           # Script SQL complet
│
└── README.md                    # Documentation
```

---

## 🎯 **Prochaines étapes**

Après installation :
1. Créer les pages du catalogue
2. Créer le tableau de bord membre
3. Créer l'interface bibliothécaire
4. Créer l'interface administrateur
5. Tester toutes les fonctionnalités

---

## 📞 **Besoin d'aide ?**

- Vérifier le README.md complet
- Consulter la documentation PHP : https://www.php.net
- Forum XAMPP : https://community.apachefriends.org

---

**Installation terminée ! Bon développement ! 🚀**
