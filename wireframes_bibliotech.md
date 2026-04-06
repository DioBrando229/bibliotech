# WIREFRAMES - BIBLIOTECH
## Schémas de structure des pages principales

---

## 🏠 PAGE D'ACCUEIL (index.php)

```
┌─────────────────────────────────────────────────────────────┐
│                         HEADER                               │
│  [Logo BiblioTech]    Accueil | Catalogue | À propos        │
│                                    [Connexion] [Inscription] │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                      HERO SECTION                            │
│                                                              │
│        [IMAGE BIBLIOTHÈQUE / ILLUSTRATION LIVRES]           │
│                                                              │
│              BiblioTech - Votre bibliothèque                │
│                    digitale et moderne                       │
│                                                              │
│          [Bouton : Découvrir le catalogue]                  │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│              LIVRES POPULAIRES DU MOMENT                     │
│                                                              │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐       │
│  │ [Image] │  │ [Image] │  │ [Image] │  │ [Image] │       │
│  │  Livre  │  │  Livre  │  │  Livre  │  │  Livre  │       │
│  │  Titre  │  │  Titre  │  │  Titre  │  │  Titre  │       │
│  │ Auteur  │  │ Auteur  │  │ Auteur  │  │ Auteur  │       │
│  │[Réserv.]│  │[Réserv.]│  │[Réserv.]│  │[Réserv.]│       │
│  └─────────┘  └─────────┘  └─────────┘  └─────────┘       │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                    À PROPOS BIBLIOTECH                       │
│                                                              │
│  ┌──────────────┐    BiblioTech est une bibliothèque       │
│  │              │    communautaire moderne qui facilite     │
│  │  [ICÔNE]     │    l'accès à la lecture et au savoir.    │
│  │ BIBLIOTHÈQUE │                                           │
│  │              │    • Plus de 5000 ouvrages               │
│  └──────────────┘    • Gestion digitale                     │
│                      • Réservation en ligne                 │
│                                                              │
│                 [Bouton : En savoir plus]                   │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                  COMMENT ÇA MARCHE ?                         │
│                                                              │
│  ┌──────────┐      ┌──────────┐      ┌──────────┐         │
│  │ ÉTAPE 1  │      │ ÉTAPE 2  │      │ ÉTAPE 3  │         │
│  │          │      │          │      │          │         │
│  │ [ICÔNE]  │      │ [ICÔNE]  │      │ [ICÔNE]  │         │
│  │Inscrivez │      │Recherchez│      │Réservez  │         │
│  │  -vous   │  →   │le livre  │  →   │et venez  │         │
│  │          │      │          │      │retirer   │         │
│  └──────────┘      └──────────┘      └──────────┘         │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                         FOOTER                               │
│                                                              │
│  CONTACT              LIENS RAPIDES        HORAIRES         │
│  Email: ...           • Catalogue          Lun-Ven: 8h-18h  │
│  Tél: ...             • À propos           Sam: 9h-13h      │
│  Adresse: ...         • Inscription        Dim: Fermé       │
│                                                              │
│           © 2026 BiblioTech - Tous droits réservés          │
└─────────────────────────────────────────────────────────────┘
```

---

## 📚 PAGE CATALOGUE PUBLIC (catalogue.php)

```
┌─────────────────────────────────────────────────────────────┐
│                         HEADER                               │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                     CATALOGUE DE LIVRES                      │
│                                                              │
│  ┌────────────────────────────────────────────────────┐    │
│  │  🔍  Rechercher un livre (titre, auteur...)       │    │
│  └────────────────────────────────────────────────────┘    │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ FILTRES              │          RÉSULTATS (24 livres)       │
│                      │                                       │
│ Catégories:          │  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐   │
│ □ Roman              │  │Image│ │Image│ │Image│ │Image│   │
│ □ Science            │  │     │ │     │ │     │ │     │   │
│ □ Histoire           │  │Titre│ │Titre│ │Titre│ │Titre│   │
│ □ Jeunesse           │  │Aute.│ │Aute.│ │Aute.│ │Aute.│   │
│ □ Autres             │  │[Dét]│ │[Dét]│ │[Dét]│ │[Dét]│   │
│                      │  └─────┘ └─────┘ └─────┘ └─────┘   │
│ Disponibilité:       │                                       │
│ ○ Tous               │  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐   │
│ ○ Disponibles        │  │Image│ │Image│ │Image│ │Image│   │
│ ○ Empruntés          │  │     │ │     │ │     │ │     │   │
│                      │  │Titre│ │Titre│ │Titre│ │Titre│   │
│ [Appliquer filtres]  │  │Aute.│ │Aute.│ │Aute.│ │Aute.│   │
│                      │  │[Dét]│ │[Dét]│ │[Dét]│ │[Dét]│   │
│                      │  └─────┘ └─────┘ └─────┘ └─────┘   │
│                      │                                       │
│                      │      ← 1 2 3 4 5 →  (Pagination)    │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                         FOOTER                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 📖 DÉTAILS D'UN LIVRE (livre-details.php)

```
┌─────────────────────────────────────────────────────────────┐
│                         HEADER                               │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  ← Retour au catalogue                                       │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                                                              │
│  ┌──────────────────┐                                       │
│  │                  │    TITRE DU LIVRE                     │
│  │                  │    Par Nom de l'auteur                │
│  │  IMAGE DE        │                                       │
│  │  COUVERTURE      │    Catégorie : Roman                  │
│  │  DU LIVRE        │    ISBN : 978-XXXXXXXXX               │
│  │                  │                                       │
│  │   (300x450px)    │    📚 Statut : Disponible             │
│  │                  │    Exemplaires : 3 disponibles / 5    │
│  │                  │                                       │
│  └──────────────────┘    ┌──────────────────────┐          │
│                          │  [RÉSERVER CE LIVRE] │          │
│                          └──────────────────────┘          │
│                                                              │
│  ────────────────────────────────────────────────────       │
│                                                              │
│  DESCRIPTION / RÉSUMÉ :                                     │
│  Lorem ipsum dolor sit amet, consectetur adipiscing         │
│  elit. Vivamus lacinia odio vitae vestibulum vestibulum.   │
│  Cras porttitor nunc sed nulla euismod, nec finibus        │
│  massa facilisis. Integer vel eros at nisi hendrerit...    │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                         FOOTER                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔐 PAGE CONNEXION (connexion.php)

```
┌─────────────────────────────────────────────────────────────┐
│                         HEADER                               │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                                                              │
│                   ┌───────────────────────┐                 │
│                   │                       │                 │
│                   │    CONNEXION          │                 │
│                   │                       │                 │
│                   │  Email :              │                 │
│                   │  ┌─────────────────┐  │                 │
│                   │  │                 │  │                 │
│                   │  └─────────────────┘  │                 │
│                   │                       │                 │
│                   │  Mot de passe :       │                 │
│                   │  ┌─────────────────┐  │                 │
│                   │  │                 │  │                 │
│                   │  └─────────────────┘  │                 │
│                   │                       │                 │
│                   │  ┌─────────────────┐  │                 │
│                   │  │ SE CONNECTER    │  │                 │
│                   │  └─────────────────┘  │                 │
│                   │                       │                 │
│                   │  Mot de passe oublié? │                 │
│                   │                       │                 │
│                   │  Pas encore membre ?  │                 │
│                   │  [S'inscrire]         │                 │
│                   │                       │                 │
│                   └───────────────────────┘                 │
│                                                              │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                         FOOTER                               │
└─────────────────────────────────────────────────────────────┘
```

---

## 👤 TABLEAU DE BORD MEMBRE (membre/dashboard.php)

```
┌─────────────────────────────────────────────────────────────┐
│  [Logo] BiblioTech                    [👤 Jean Dupont ▼]   │
└─────────────────────────────────────────────────────────────┘

┌──────────┬──────────────────────────────────────────────────┐
│          │                                                  │
│ MENU     │  Bonjour Jean ! 👋                               │
│          │                                                  │
│ 🏠 Accueil│  ┌─────────┐  ┌─────────┐  ┌─────────┐        │
│ 👤 Profil │  │   2     │  │   1     │  │   5     │        │
│ 📚 Emprunts│  │Emprunts │  │Réserva- │  │Livres   │        │
│ 🔖 Réserv.│  │en cours │  │tion     │  │lus      │        │
│ 📖 Catalog│  └─────────┘  └─────────┘  └─────────┘        │
│ 🚪 Déconn.│                                                  │
│          │  ────────────────────────────────────────        │
│          │                                                  │
│          │  MES EMPRUNTS EN COURS :                        │
│          │                                                  │
│          │  ┌────────────────────────────────────────┐     │
│          │  │ [📘] Titre livre 1 | Retour: 5 jours   │     │
│          │  ├────────────────────────────────────────┤     │
│          │  │ [📗] Titre livre 2 | Retour: 12 jours  │     │
│          │  └────────────────────────────────────────┘     │
│          │                                                  │
│          │  MES RÉSERVATIONS :                             │
│          │                                                  │
│          │  ┌────────────────────────────────────────┐     │
│          │  │ [📙] Titre livre 3 | En attente        │     │
│          │  └────────────────────────────────────────┘     │
│          │                                                  │
└──────────┴──────────────────────────────────────────────────┘
```

---

## 📋 TABLEAU DE BORD BIBLIOTHÉCAIRE (bibliothecaire/dashboard.php)

```
┌─────────────────────────────────────────────────────────────┐
│  [Logo] BiblioTech               [👤 Marie Bibliothécaire ▼]│
└─────────────────────────────────────────────────────────────┘

┌──────────┬──────────────────────────────────────────────────┐
│          │                                                  │
│ MENU     │  TABLEAU DE BORD - AUJOURD'HUI                  │
│          │                                                  │
│ 🏠 Accueil│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌────┐│
│ 📚 Empru. │  │   8     │  │   5     │  │   3     │  │ 2  ││
│ 🔖 Réserv.│  │Emprunts │  │Retours  │  │Réserva- │  │Ret.││
│ 👥 Membres│  │du jour  │  │du jour  │  │tions    │  │ard ││
│ 🚪 Déconn.│  └─────────┘  └─────────┘  └─────────┘  └────┘│
│          │                                                  │
│          │  ACTIONS RAPIDES :                              │
│          │  ┌────────────────┐  ┌────────────────┐        │
│          │  │ Nouvel emprunt │  │ Enreg. retour  │        │
│          │  └────────────────┘  └────────────────┘        │
│          │                                                  │
│          │  DERNIÈRES ACTIVITÉS :                          │
│          │  ┌──────────────────────────────────────────┐  │
│          │  │ 14:30 - Emprunt: Jean D. - Livre XYZ    │  │
│          │  │ 13:15 - Retour: Marie K. - Livre ABC    │  │
│          │  │ 11:00 - Réservation: Paul M. - Livre... │  │
│          │  └──────────────────────────────────────────┘  │
│          │                                                  │
└──────────┴──────────────────────────────────────────────────┘
```

---

## ⚙️ TABLEAU DE BORD ADMIN (admin/dashboard.php)

```
┌─────────────────────────────────────────────────────────────┐
│  [Logo] BiblioTech                  [👤 Admin BiblioTech ▼] │
└─────────────────────────────────────────────────────────────┘

┌──────────┬──────────────────────────────────────────────────┐
│          │                                                  │
│ MENU     │  STATISTIQUES GLOBALES                          │
│          │                                                  │
│ 🏠 Accueil│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌────┐│
│ 📚 Catalog│  │  1,234  │  │   156   │  │   89    │  │75% ││
│ 👥 Utilis.│  │ Livres  │  │Membres  │  │Emprunts │  │Tx  ││
│ 📊 Stats  │  │ total   │  │ actifs  │  │ce mois  │  │occ.││
│ 🚪 Déconn.│  └─────────┘  └─────────┘  └─────────┘  └────┘│
│          │                                                  │
│          │  ÉVOLUTION DES EMPRUNTS (12 mois)               │
│          │  ┌──────────────────────────────────────────┐  │
│          │  │    📊 GRAPHIQUE EN BARRES / COURBE       │  │
│          │  │                                          │  │
│          │  │    █                                     │  │
│          │  │    █     █         █                     │  │
│          │  │    █  █  █  █   █  █  █                  │  │
│          │  │ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━         │  │
│          │  │  J F M A M J J A S O N D                 │  │
│          │  └──────────────────────────────────────────┘  │
│          │                                                  │
│          │  LIVRES LES PLUS EMPRUNTÉS (Top 5)              │
│          │  1. Titre livre 1 ━━━━━━━━━━━━ 45 emprunts    │
│          │  2. Titre livre 2 ━━━━━━━━━━ 38 emprunts      │
│          │  3. Titre livre 3 ━━━━━━━━ 32 emprunts        │
│          │  4. Titre livre 4 ━━━━━━ 28 emprunts          │
│          │  5. Titre livre 5 ━━━━━ 25 emprunts           │
│          │                                                  │
└──────────┴──────────────────────────────────────────────────┘
```

---

## 📱 VERSION MOBILE - EXEMPLE ACCUEIL

```
┌─────────────────────┐
│ ☰  BiblioTech  🔍  │
├─────────────────────┤
│                     │
│   [IMAGE HERO]      │
│                     │
│   BiblioTech        │
│ Votre bibliothèque  │
│   digitale          │
│                     │
│ [Découvrir]         │
│                     │
├─────────────────────┤
│ LIVRES POPULAIRES   │
│                     │
│ ┌─────────────────┐ │
│ │ [Image]         │ │
│ │ Titre livre     │ │
│ │ Auteur          │ │
│ │ [Réserver]      │ │
│ └─────────────────┘ │
│                     │
│ ┌─────────────────┐ │
│ │ [Image]         │ │
│ │ Titre livre     │ │
│ │ Auteur          │ │
│ │ [Réserver]      │ │
│ └─────────────────┘ │
│                     │
│    [Voir plus]      │
│                     │
├─────────────────────┤
│ À PROPOS            │
│ ...                 │
└─────────────────────┘
```

---

**Ces wireframes servent de base pour les maquettes haute-fidélité.**
**Prochaine étape : Design visuel avec couleurs et typographie.**
