# CAHIER DES CHARGES
## Système de Gestion de Bibliothèque Communautaire en Ligne

---

## 1. PRÉSENTATION DU PROJET

### 1.1 Contexte
Dans le contexte béninois, l'accès à l'information et à la lecture reste un défi pour de nombreuses communautés. Les bibliothèques communautaires jouent un rôle crucial dans la promotion de l'éducation et de la culture, mais leur gestion manuelle limite leur efficacité et leur accessibilité.

### 1.2 Problématique
Les bibliothèques communautaires au Bénin font face à plusieurs défis :
- Gestion manuelle des registres d'emprunts (cahiers physiques)
- Difficulté à suivre les livres empruntés et les retours
- Absence de système de réservation
- Temps d'attente important pour les usagers
- Difficulté à générer des statistiques d'utilisation
- Perte ou détérioration des registres papier

### 1.3 Objectif général
Développer une application web simple et efficace pour faciliter la gestion quotidienne d'une bibliothèque communautaire et améliorer l'expérience des lecteurs.

### 1.4 Objectifs spécifiques
- Digitaliser le catalogue des livres disponibles
- Automatiser le processus d'emprunt et de retour
- Permettre aux membres de réserver des livres en ligne
- Faciliter la gestion administrative de la bibliothèque
- Fournir des statistiques d'utilisation simples

---

## 2. PUBLIC CIBLE

### 2.1 Utilisateurs finaux
- **Lecteurs/Membres** : Jeunes, étudiants, adultes de la communauté
- **Bibliothécaires** : Personnel chargé de la gestion quotidienne
- **Administrateur** : Responsable de la bibliothèque

### 2.2 Besoins utilisateurs
**Lecteurs :**
- Consulter le catalogue en ligne
- Rechercher des livres disponibles
- Réserver des livres
- Voir l'historique de leurs emprunts

**Bibliothécaires :**
- Enregistrer les emprunts et retours
- Gérer les membres
- Consulter les réservations

**Administrateur :**
- Gérer le catalogue complet
- Ajouter/modifier/supprimer des livres
- Gérer les utilisateurs (bibliothécaires et membres)
- Consulter les statistiques

---

## 3. FONCTIONNALITÉS DU SYSTÈME

### 3.1 ESPACE PUBLIC (Sans connexion)
- **Page d'accueil** : Présentation de la bibliothèque
- **Catalogue public** : Consultation des livres disponibles
- **Recherche** : Par titre, auteur, catégorie
- **Inscription en ligne** : Devenir membre

### 3.2 ESPACE MEMBRE (Lecteur connecté)
#### Fonctionnalités de base :
- **Connexion/Déconnexion**
- **Tableau de bord personnel** :
  - Emprunts en cours
  - Historique des emprunts
  - Réservations actives
- **Recherche avancée de livres**
- **Réservation de livres** (si disponibles)
- **Annulation de réservation**
- **Modification du profil** (téléphone, adresse)

### 3.3 ESPACE BIBLIOTHÉCAIRE
#### Fonctionnalités de gestion :
- **Gestion des emprunts** :
  - Enregistrer un nouvel emprunt
  - Enregistrer un retour
  - Voir les emprunts en cours
  - Voir les emprunts en retard
- **Gestion des réservations** :
  - Voir les réservations en attente
  - Valider une réservation
  - Annuler une réservation
- **Gestion des membres** :
  - Liste des membres actifs
  - Recherche de membre
  - Modifier le statut d'un membre

### 3.4 ESPACE ADMINISTRATEUR
#### Fonctionnalités administratives :
- **Gestion du catalogue** :
  - Ajouter un livre (titre, auteur, ISBN, catégorie, nombre d'exemplaires)
  - Modifier les informations d'un livre
  - Supprimer un livre
  - Upload d'image de couverture
- **Gestion des utilisateurs** :
  - Créer un compte bibliothécaire
  - Activer/Désactiver des comptes
  - Gérer les rôles
- **Statistiques simples** :
  - Nombre total de livres
  - Nombre de membres actifs
  - Nombre d'emprunts du mois
  - Livres les plus empruntés
  - Taux d'occupation de la bibliothèque

---

## 4. CONTRAINTES TECHNIQUES

### 4.1 Contraintes fonctionnelles
- **Simplicité d'utilisation** : Interface intuitive adaptée à tous niveaux
- **Responsive design** : Accessible sur mobile, tablette, ordinateur (important au Bénin)
- **Performance** : Chargement rapide même avec connexion lente
- **Accessibilité** : Textes lisibles, contrastes suffisants

### 4.2 Contraintes techniques
- **Backend simplifié** : Architecture simple et maintenable
- **Base de données légère** : Structure claire et optimisée
- **Pas de paiement en ligne** : Gestion des amendes en présentiel
- **Pas de notifications SMS** : Système de notifications simple (email optionnel)

### 4.3 Contraintes de sécurité
- **Authentification sécurisée** : Mots de passe hashés
- **Gestion des sessions**
- **Protection contre les injections SQL**
- **Validation des formulaires** (front et back)

---

## 5. RÈGLES DE GESTION

### 5.1 Gestion des emprunts
- Durée maximale d'emprunt : **14 jours**
- Nombre maximum de livres empruntés simultanément : **3 livres**
- Un membre ne peut pas emprunter si un livre est en retard
- Un livre emprunté ne peut pas être réservé

### 5.2 Gestion des réservations
- Un livre peut être réservé uniquement s'il est actuellement emprunté
- Durée de validité d'une réservation : **7 jours** après disponibilité
- Maximum **1 réservation active** par membre

### 5.3 Gestion des retours
- Retour possible avant la date limite sans pénalité
- Retard signalé dans le système (gestion d'amende en présentiel)

### 5.4 Gestion des membres
- Inscription validée par un bibliothécaire/administrateur
- Compte désactivé après 6 mois d'inactivité
- Possibilité de réactivation sur demande

---

## 6. TECHNOLOGIES PROPOSÉES (Backend simplifié)

### 6.1 Frontend
- **HTML5 / CSS3** : Structure et design
- **JavaScript** : Interactivité
- **Bootstrap 5** ou **Tailwind CSS** : Framework CSS pour le responsive

### 6.2 Backend (Simple)
**Option 1 : PHP Natif + MySQL**
- PHP 7.4+ (simple et direct)
- MySQL (base de données)
- Architecture MVC basique
- Sessions PHP pour l'authentification

**Option 2 : Node.js + Express (si JavaScript préféré)**
- Node.js + Express.js
- MySQL ou SQLite
- EJS pour les templates

### 6.3 Base de données
- **MySQL** ou **SQLite** (selon hébergement)
- Structure simple : 5-7 tables maximum

### 6.4 Hébergement
- **Gratuit** : 000webhost, InfinityFree, Vercel
- **Payant local** : Hébergeurs béninois si budget disponible

---

## 7. PLANNING PRÉVISIONNEL

### Phase 1 : Conception (Semaines 1-2)
- Veille technologique et concurrentielle
- Étude UX/UI
- Création des wireframes et maquettes
- Validation du design

### Phase 2 : Développement Frontend (Semaines 3-4)
- Pages statiques (accueil, catalogue, connexion)
- Responsive design
- Interface membre
- Interface administrateur

### Phase 3 : Développement Backend (Semaines 5-6)
- Mise en place de la base de données
- Système d'authentification
- API/Routes pour les CRUD
- Gestion des emprunts et réservations

### Phase 4 : Intégration et Tests (Semaine 7)
- Connexion front-back
- Tests fonctionnels
- Correction des bugs
- Optimisation

### Phase 5 : Déploiement et Documentation (Semaine 8)
- Mise en ligne
- Rédaction du dossier final
- Préparation de la présentation

---

## 8. LIVRABLES ATTENDUS

### 8.1 Livrables techniques
- Application web fonctionnelle et déployée
- Code source commenté (GitHub)
- Base de données exportée (.sql)
- Documentation technique

### 8.2 Livrables pédagogiques
- Dossier projet complet (selon plan école)
- Support de présentation PowerPoint
- Vidéo de démonstration (optionnel)

---

## 9. CRITÈRES DE RÉUSSITE

Le projet sera considéré comme réussi si :
- ✅ Le système permet l'inscription et la connexion sécurisée
- ✅ Le catalogue est consultable et fonctionnel
- ✅ Les emprunts et retours peuvent être enregistrés
- ✅ Les réservations fonctionnent correctement
- ✅ L'interface est responsive et intuitive
- ✅ Le code est propre et commenté
- ✅ L'application est déployée en ligne

---

## 10. RISQUES ET SOLUTIONS

| Risque | Impact | Solution |
|--------|--------|----------|
| Complexité backend trop élevée | Retard projet | Utiliser PHP natif, garder architecture simple |
| Problèmes d'hébergement | Site inaccessible | Prévoir plusieurs options d'hébergement |
| Bugs lors des tests | Projet non fonctionnel | Tests réguliers dès le début |
| Manque de temps | Projet incomplet | Prioriser les fonctionnalités essentielles |

---

## 11. BUDGET

- **Hébergement** : 0€ (hébergement gratuit)
- **Nom de domaine** : Optionnel (~ 5000 FCFA/an)
- **Outils** : Gratuits (VS Code, XAMPP, Figma, etc.)

**Budget total estimé : 0 - 5000 FCFA**

---

## 12. GLOSSAIRE

- **CRUD** : Create, Read, Update, Delete (opérations de base)
- **MVC** : Model-View-Controller (architecture logicielle)
- **Responsive** : Adaptation aux différentes tailles d'écran
- **Backend** : Partie serveur de l'application
- **Frontend** : Partie visible par l'utilisateur

---

**Document rédigé le** : [Date]  
**Auteur** : [Votre Nom]  
**Projet** : Certification EIG 2026 - Développement Web
