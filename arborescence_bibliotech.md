# ARBORESCENCE DU SITE - BIBLIOTECH
## Système de Gestion de Bibliothèque Communautaire en Ligne

---

## STRUCTURE GÉNÉRALE DU SITE

```
BIBLIOTECH
│
├── ESPACE PUBLIC (Non connecté)
│   ├── Accueil
│   ├── Catalogue
│   ├── À propos
│   ├── Contact
│   ├── Inscription
│   └── Connexion
│
├── ESPACE MEMBRE (Lecteur connecté)
│   ├── Tableau de bord
│   ├── Mon profil
│   ├── Mes emprunts
│   ├── Mes réservations
│   ├── Catalogue (recherche avancée)
│   └── Déconnexion
│
├── ESPACE BIBLIOTHÉCAIRE
│   ├── Tableau de bord
│   ├── Gestion des emprunts
│   │   ├── Nouvel emprunt
│   │   ├── Enregistrer un retour
│   │   ├── Emprunts en cours
│   │   └── Emprunts en retard
│   ├── Gestion des réservations
│   │   ├── Réservations en attente
│   │   └── Historique réservations
│   ├── Gestion des membres
│   │   ├── Liste des membres
│   │   ├── Rechercher un membre
│   │   └── Modifier statut membre
│   └── Déconnexion
│
└── ESPACE ADMINISTRATEUR
    ├── Tableau de bord (statistiques)
    ├── Gestion du catalogue
    │   ├── Liste des livres
    │   ├── Ajouter un livre
    │   ├── Modifier un livre
    │   └── Supprimer un livre
    ├── Gestion des utilisateurs
    │   ├── Liste utilisateurs
    │   ├── Créer bibliothécaire
    │   ├── Activer/Désactiver compte
    │   └── Gérer les rôles
    ├── Statistiques
    └── Déconnexion
```

---

## DÉTAIL DES PAGES

### 📌 ESPACE PUBLIC

#### 1. **Page d'accueil** (`index.php`)
**Contenu :**
- Header avec logo BiblioTech + menu navigation
- Hero section (image + slogan)
- Section : Livres populaires (6 livres)
- Section : À propos (résumé)
- Section : Comment ça marche (3 étapes)
- Footer (contact, horaires, réseaux sociaux)

**Navigation :**
→ Catalogue | À propos | Contact | Connexion | Inscription

---

#### 2. **Catalogue public** (`catalogue.php`)
**Contenu :**
- Barre de recherche (titre, auteur, catégorie)
- Filtres : Catégories, Disponibilité
- Grille de livres (image, titre, auteur, statut)
- Pagination

**Actions :**
- Clic sur livre → Détails du livre
- "Réserver" → Redirection vers connexion (si non connecté)

---

#### 3. **Détails d'un livre** (`livre-details.php?id=X`)
**Contenu :**
- Image de couverture
- Titre, auteur, ISBN, catégorie
- Description/résumé
- Nombre d'exemplaires disponibles
- Statut (disponible/emprunté)
- Bouton "Réserver" (si connecté)

---

#### 4. **À propos** (`a-propos.php`)
**Contenu :**
- Présentation de BiblioTech
- Mission et vision
- Services offerts
- Horaires d'ouverture
- Localisation (carte fictive)

---

#### 5. **Contact** (`contact.php`)
**Contenu :**
- Formulaire de contact (nom, email, message)
- Coordonnées de la bibliothèque
- Plan d'accès

---

#### 6. **Inscription** (`inscription.php`)
**Contenu :**
- Formulaire d'inscription :
  - Nom et prénoms
  - Email
  - Téléphone
  - Adresse
  - Mot de passe
  - Confirmation mot de passe
- Bouton "S'inscrire"
- Lien vers connexion

**Action :**
→ Inscription → Message "Compte créé, en attente de validation"

---

#### 7. **Connexion** (`connexion.php`)
**Contenu :**
- Formulaire de connexion (email, mot de passe)
- Bouton "Se connecter"
- Lien "Mot de passe oublié ?"
- Lien vers inscription

**Action :**
→ Connexion → Redirection selon le rôle (membre/bibliothécaire/admin)

---

### 📌 ESPACE MEMBRE (Lecteur)

#### 8. **Tableau de bord membre** (`membre/dashboard.php`)
**Contenu :**
- Message de bienvenue (Bonjour [Prénom])
- Statistiques personnelles :
  - Emprunts en cours (nombre)
  - Réservations actives (nombre)
  - Livres lus ce mois
- Section "Mes emprunts en cours" (liste rapide)
- Section "Mes réservations" (liste rapide)
- Accès rapides (boutons)

**Navigation latérale :**
- Tableau de bord
- Mon profil
- Mes emprunts
- Mes réservations
- Catalogue
- Déconnexion

---

#### 9. **Mon profil** (`membre/profil.php`)
**Contenu :**
- Informations personnelles :
  - Nom, prénoms
  - Email
  - Téléphone
  - Adresse
- Bouton "Modifier"
- Changer mot de passe

---

#### 10. **Mes emprunts** (`membre/emprunts.php`)
**Contenu :**
- Onglets :
  - Emprunts en cours
  - Historique des emprunts
- Tableau :
  - Image livre
  - Titre
  - Date d'emprunt
  - Date de retour prévue
  - Statut (en cours/en retard)

---

#### 11. **Mes réservations** (`membre/reservations.php`)
**Contenu :**
- Liste des réservations actives :
  - Livre réservé
  - Date de réservation
  - Statut (en attente/disponible)
  - Bouton "Annuler réservation"

---

#### 12. **Catalogue membre** (`membre/catalogue.php`)
**Contenu :**
- Même que catalogue public mais avec :
  - Bouton "Réserver" fonctionnel
  - Indication "Déjà emprunté" si applicable

---

### 📌 ESPACE BIBLIOTHÉCAIRE

#### 13. **Tableau de bord bibliothécaire** (`bibliothecaire/dashboard.php`)
**Contenu :**
- Statistiques du jour :
  - Emprunts du jour
  - Retours du jour
  - Réservations en attente
  - Emprunts en retard
- Actions rapides (boutons)
- Liste des dernières activités

**Navigation latérale :**
- Tableau de bord
- Gestion emprunts
- Gestion réservations
- Gestion membres
- Déconnexion

---

#### 14. **Gestion des emprunts** (`bibliothecaire/emprunts.php`)
**Sous-pages :**

**14.1 Nouvel emprunt** (`bibliothecaire/nouvel-emprunt.php`)
- Rechercher un membre (email/nom)
- Rechercher un livre (titre/ISBN)
- Afficher infos membre (emprunts en cours)
- Date de retour prévue (auto-calculée : +14 jours)
- Bouton "Enregistrer l'emprunt"

**14.2 Enregistrer un retour** (`bibliothecaire/enregistrer-retour.php`)
- Rechercher membre ou scanner ISBN
- Afficher livres empruntés
- Sélectionner livre à retourner
- Vérifier retard (calcul automatique)
- Bouton "Enregistrer le retour"

**14.3 Emprunts en cours** (`bibliothecaire/emprunts-en-cours.php`)
- Tableau de tous les emprunts actifs :
  - Membre
  - Livre
  - Date emprunt
  - Date retour prévue
  - Jours restants
  - Actions (enregistrer retour)

**14.4 Emprunts en retard** (`bibliothecaire/emprunts-retard.php`)
- Tableau des emprunts en retard :
  - Membre (nom, téléphone)
  - Livre
  - Jours de retard
  - Actions (contacter, enregistrer retour)

---

#### 15. **Gestion des réservations** (`bibliothecaire/reservations.php`)
**Contenu :**
- Onglets :
  - Réservations en attente
  - Réservations validées
  - Historique

**15.1 Réservations en attente**
- Tableau :
  - Membre
  - Livre réservé
  - Date réservation
  - Actions (valider/annuler)

---

#### 16. **Gestion des membres** (`bibliothecaire/membres.php`)
**Contenu :**
- Barre de recherche membre
- Filtres (actifs/inactifs)
- Tableau :
  - Nom
  - Email
  - Téléphone
  - Statut (actif/inactif)
  - Emprunts en cours
  - Actions (voir détails, modifier statut)

**16.1 Détails membre** (`bibliothecaire/membre-details.php?id=X`)
- Infos personnelles
- Historique d'emprunts
- Emprunts en cours
- Réservations actives

---

### 📌 ESPACE ADMINISTRATEUR

#### 17. **Tableau de bord admin** (`admin/dashboard.php`)
**Contenu :**
- Statistiques globales :
  - Nombre total de livres
  - Nombre de membres actifs
  - Emprunts du mois
  - Taux d'occupation
- Graphiques simples :
  - Évolution des emprunts (12 derniers mois)
  - Livres les plus empruntés (Top 10)
  - Catégories populaires
- Actions rapides

**Navigation latérale :**
- Tableau de bord
- Gestion catalogue
- Gestion utilisateurs
- Statistiques
- Déconnexion

---

#### 18. **Gestion du catalogue** (`admin/catalogue.php`)
**Sous-pages :**

**18.1 Liste des livres** (`admin/catalogue.php`)
- Barre de recherche
- Filtres (catégorie, disponibilité)
- Tableau :
  - Image
  - Titre
  - Auteur
  - ISBN
  - Catégorie
  - Exemplaires (total/disponibles)
  - Actions (modifier/supprimer)

**18.2 Ajouter un livre** (`admin/ajouter-livre.php`)
- Formulaire :
  - Titre
  - Auteur
  - ISBN
  - Catégorie (liste déroulante)
  - Description
  - Nombre d'exemplaires
  - Image de couverture (upload)
- Bouton "Ajouter"

**18.3 Modifier un livre** (`admin/modifier-livre.php?id=X`)
- Même formulaire pré-rempli
- Bouton "Mettre à jour"

---

#### 19. **Gestion des utilisateurs** (`admin/utilisateurs.php`)
**Contenu :**
- Onglets :
  - Membres
  - Bibliothécaires
  - Administrateurs

**Tableau (pour chaque onglet) :**
- Nom
- Email
- Rôle
- Statut (actif/inactif)
- Date d'inscription
- Actions (modifier/activer/désactiver)

**19.1 Créer bibliothécaire** (`admin/creer-bibliothecaire.php`)
- Formulaire similaire à inscription
- Attribution du rôle "bibliothécaire"

---

#### 20. **Statistiques** (`admin/statistiques.php`)
**Contenu :**
- Filtres par période (semaine/mois/année)
- Rapports :
  - Nombre d'emprunts par période
  - Livres les plus empruntés
  - Membres les plus actifs
  - Taux de retour à temps
  - Catégories populaires
- Export PDF (optionnel)

---

## ÉLÉMENTS COMMUNS À TOUTES LES PAGES

### **Header**
- Logo BiblioTech (gauche)
- Navigation (centre)
- Bouton connexion/Profil utilisateur (droite)

### **Footer**
- Informations de contact
- Liens rapides
- Horaires d'ouverture
- Réseaux sociaux
- Copyright © 2026 BiblioTech

### **Messages système**
- Messages de succès (vert)
- Messages d'erreur (rouge)
- Messages d'information (bleu)

---

## NAVIGATION RESPONSIVE (Mobile)

- Menu burger pour mobile
- Navigation simplifiée
- Boutons tactiles plus grands
- Formulaires adaptés

---

**Total : 20+ pages principales**  
**Architecture : Simple et logique**  
**Navigation : Intuitive et claire**
