-- ============================================
-- BASE DE DONNÉES BIBLIOTECH
-- Système de Gestion de Bibliothèque
-- ============================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS bibliotech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bibliotech;

-- ============================================
-- TABLE 1 : UTILISATEURS (tous types)
-- ============================================
CREATE TABLE utilisateurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenoms VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('membre', 'bibliothecaire', 'admin') DEFAULT 'membre',
    statut ENUM('actif', 'inactif', 'en_attente') DEFAULT 'en_attente',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 2 : CATÉGORIES DE LIVRES
-- ============================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 3 : LIVRES
-- ============================================
CREATE TABLE livres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(255) NOT NULL,
    auteur VARCHAR(150) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    categorie_id INT NOT NULL,
    description TEXT,
    image_couverture VARCHAR(255),
    nombre_exemplaires INT DEFAULT 1,
    exemplaires_disponibles INT DEFAULT 1,
    date_ajout DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_titre (titre),
    INDEX idx_auteur (auteur),
    INDEX idx_isbn (isbn),
    INDEX idx_categorie (categorie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 4 : EMPRUNTS
-- ============================================
CREATE TABLE emprunts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    livre_id INT NOT NULL,
    date_emprunt DATE NOT NULL DEFAULT (CURRENT_DATE),
    date_retour_prevue DATE NOT NULL,
    date_retour_effective DATE,
    statut ENUM('en_cours', 'retourne', 'en_retard') DEFAULT 'en_cours',
    enregistre_par INT, -- ID du bibliothécaire/admin
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (livre_id) REFERENCES livres(id) ON DELETE CASCADE,
    FOREIGN KEY (enregistre_par) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_livre (livre_id),
    INDEX idx_statut (statut),
    INDEX idx_dates (date_emprunt, date_retour_prevue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE 5 : RÉSERVATIONS
-- ============================================
CREATE TABLE reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    livre_id INT NOT NULL,
    date_reservation DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'disponible', 'annulee', 'traitee') DEFAULT 'en_attente',
    date_expiration DATE, -- 7 jours après disponibilité
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (livre_id) REFERENCES livres(id) ON DELETE CASCADE,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_livre (livre_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERTION DES DONNÉES DE TEST
-- ============================================

-- Catégories
INSERT INTO categories (nom, description) VALUES
('Roman', 'Romans et littérature générale'),
('Science', 'Livres scientifiques et techniques'),
('Histoire', 'Histoire et géographie'),
('Jeunesse', 'Livres pour enfants et adolescents'),
('Philosophie', 'Philosophie et essais'),
('Littérature africaine', 'Œuvres d\'auteurs africains'),
('Science-Fiction', 'Romans de science-fiction et fantasy'),
('Biographie', 'Biographies et autobiographies');

-- Utilisateurs (mot de passe: "password123")
-- Hash généré avec votre version de PHP pour compatibilité
INSERT INTO utilisateurs (nom, prenoms, email, telephone, adresse, mot_de_passe, role, statut) VALUES
-- Admin
('Admin', 'BiblioTech', 'admin@bibliotech.bj', '+229 97 00 00 00', 'Abomey-Calavi, Bénin', '$2y$10$Z1YWV5qI8g4fxAmsmCC9A.7Z3Iru8zpw/GuQ4jThHUDw9Tu2kPg1q', 'admin', 'actif'),
-- Bibliothécaires
('Koffi', 'Marie', 'marie.koffi@bibliotech.bj', '+229 97 11 11 11', 'Cotonou, Bénin', '$2y$10$Z1YWV5qI8g4fxAmsmCC9A.7Z3Iru8zpw/GuQ4jThHUDw9Tu2kPg1q', 'bibliothecaire', 'actif'),
-- Membres
('Dupont', 'Jean', 'jean.dupont@email.bj', '+229 97 22 22 22', 'Abomey-Calavi', '$2y$10$Z1YWV5qI8g4fxAmsmCC9A.7Z3Iru8zpw/GuQ4jThHUDw9Tu2kPg1q', 'membre', 'actif'),
('Sossa', 'Aminata', 'aminata.sossa@email.bj', '+229 97 33 33 33', 'Porto-Novo', '$2y$10$Z1YWV5qI8g4fxAmsmCC9A.7Z3Iru8zpw/GuQ4jThHUDw9Tu2kPg1q', 'membre', 'actif'),
('Houngbedji', 'Paul', 'paul.h@email.bj', '+229 97 44 44 44', 'Cotonou', '$2y$10$Z1YWV5qI8g4fxAmsmCC9A.7Z3Iru8zpw/GuQ4jThHUDw9Tu2kPg1q', 'membre', 'en_attente');

-- Livres
INSERT INTO livres (titre, auteur, isbn, categorie_id, description, nombre_exemplaires, exemplaires_disponibles) VALUES
-- Romans
('L\'Étranger', 'Albert Camus', '978-2070360024', 1, 'Un homme ordinaire commet un meurtre sur une plage algéroise. C\'est le point de départ de ce roman existentialiste majeur.', 3, 2),
('Les Misérables', 'Victor Hugo', '978-2253096337', 1, 'Une fresque sociale qui suit le destin de Jean Valjean, ancien bagnard en quête de rédemption dans la France du XIXe siècle.', 2, 2),
('Le Petit Prince', 'Antoine de Saint-Exupéry', '978-2070612758', 4, 'Un conte poétique et philosophique sur l\'enfance, l\'amitié et le sens de la vie.', 5, 5),

-- Science-Fiction
('1984', 'George Orwell', '978-2070368228', 7, 'Dans un monde totalitaire, Winston Smith tente de préserver son individualité et sa liberté de pensée.', 2, 1),
('Le Meilleur des mondes', 'Aldous Huxley', '978-2266283892', 7, 'Une dystopie visionnaire sur une société future où le bonheur est imposé par la science.', 2, 2),

-- Littérature africaine
('Une si longue lettre', 'Mariama Bâ', '978-2253038269', 6, 'À travers une lettre, Ramatoulaye raconte sa vie de femme africaine moderne face aux traditions.', 3, 2),
('Les Soleils des indépendances', 'Ahmadou Kourouma', '978-2020238984', 6, 'Le destin tragique de Fama, prince déchu après les indépendances africaines.', 2, 2),

-- Histoire et Science
('Sapiens', 'Yuval Noah Harari', '978-2226257017', 2, 'Une brève histoire de l\'humanité, de l\'âge de pierre à l\'ère moderne.', 3, 2),
('Cosmos', 'Carl Sagan', '978-0345539434', 2, 'Une exploration poétique de l\'univers et de notre place dans le cosmos.', 2, 1),

-- Philosophie
('Le Mythe de Sisyphe', 'Albert Camus', '978-2070322886', 5, 'Un essai philosophique sur l\'absurde et le sens de la vie.', 2, 2),
('Ainsi parlait Zarathoustra', 'Friedrich Nietzsche', '978-2253006329', 5, 'L\'œuvre majeure de Nietzsche présentant sa philosophie du surhomme.', 2, 2),

-- Biographies
('Une vie', 'Simone Veil', '978-2253088257', 8, 'L\'autobiographie bouleversante de Simone Veil, rescapée d\'Auschwitz et femme politique engagée.', 2, 2);

-- Emprunts (quelques emprunts actifs)
INSERT INTO emprunts (utilisateur_id, livre_id, date_emprunt, date_retour_prevue, date_retour_effective, statut, enregistre_par) VALUES
-- Emprunts de Jean Dupont (id: 3) — en cours, retour prévu mi-avril
(3, 1, '2026-04-01', '2026-04-15', NULL, 'en_cours', 2),
(3, 6, '2026-04-07', '2026-04-21', NULL, 'en_cours', 2),
-- Emprunt d'Aminata (id: 4) — un en retard, un en cours
(4, 4, '2026-03-15', '2026-03-29', NULL, 'en_retard', 2),
(4, 8, '2026-04-03', '2026-04-17', NULL, 'en_cours', 2),
-- Retours déjà effectués en avril (pour livres lus ce mois)
(3, 2, '2026-03-20', '2026-04-03', '2026-04-04', 'retourne', 2),
(4, 5, '2026-03-18', '2026-04-01', '2026-04-05', 'retourne', 2);

-- Réservations
INSERT INTO reservations (utilisateur_id, livre_id, statut) VALUES
(3, 4, 'en_attente'), -- Jean réserve "1984" qui est emprunté
(4, 10, 'en_attente'); -- Aminata réserve "Le Mythe de Sisyphe"

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue : Livres avec informations de catégorie
CREATE VIEW vue_livres_complet AS
SELECT 
    l.id,
    l.titre,
    l.auteur,
    l.isbn,
    l.description,
    l.image_couverture,
    l.nombre_exemplaires,
    l.exemplaires_disponibles,
    c.nom AS categorie,
    l.date_ajout,
    CASE 
        WHEN l.exemplaires_disponibles > 0 THEN 'Disponible'
        ELSE 'Emprunté'
    END AS statut
FROM livres l
JOIN categories c ON l.categorie_id = c.id;

-- Vue : Emprunts en cours avec détails
CREATE VIEW vue_emprunts_actifs AS
SELECT 
    e.id,
    e.date_emprunt,
    e.date_retour_prevue,
    DATEDIFF(e.date_retour_prevue, CURDATE()) AS jours_restants,
    e.statut,
    CONCAT(u.prenoms, ' ', u.nom) AS membre,
    u.email,
    u.telephone,
    l.titre AS livre,
    l.auteur,
    CONCAT(b.prenoms, ' ', b.nom) AS enregistre_par
FROM emprunts e
JOIN utilisateurs u ON e.utilisateur_id = u.id
JOIN livres l ON e.livre_id = l.id
LEFT JOIN utilisateurs b ON e.enregistre_par = b.id
WHERE e.statut IN ('en_cours', 'en_retard')
ORDER BY e.date_retour_prevue ASC;

-- Vue : Statistiques globales
CREATE VIEW vue_statistiques AS
SELECT 
    (SELECT COUNT(*) FROM livres) AS total_livres,
    (SELECT SUM(nombre_exemplaires) FROM livres) AS total_exemplaires,
    (SELECT COUNT(*) FROM utilisateurs WHERE role = 'membre' AND statut = 'actif') AS membres_actifs,
    (SELECT COUNT(*) FROM emprunts WHERE statut IN ('en_cours', 'en_retard')) AS emprunts_actifs,
    (SELECT COUNT(*) FROM emprunts WHERE statut IN ('en_cours', 'en_retard') AND date_retour_prevue < CURDATE()) AS emprunts_retard,
    (SELECT COUNT(*) FROM reservations WHERE statut = 'en_attente') AS reservations_attente;

-- ============================================
-- TRIGGERS (automatisation)
-- ============================================

-- Trigger : Mettre à jour le statut d'emprunt en retard
DELIMITER $$
CREATE TRIGGER update_emprunt_retard
BEFORE UPDATE ON emprunts
FOR EACH ROW
BEGIN
    IF NEW.statut = 'en_cours' AND NEW.date_retour_prevue < CURDATE() THEN
        SET NEW.statut = 'en_retard';
    END IF;
END$$
DELIMITER ;

-- Trigger : Décrémenter exemplaires disponibles lors d'un emprunt
DELIMITER $$
CREATE TRIGGER decrement_exemplaires
AFTER INSERT ON emprunts
FOR EACH ROW
BEGIN
    UPDATE livres 
    SET exemplaires_disponibles = exemplaires_disponibles - 1 
    WHERE id = NEW.livre_id;
END$$
DELIMITER ;

-- Trigger : Incrémenter exemplaires disponibles lors d'un retour
DELIMITER $$
CREATE TRIGGER increment_exemplaires
AFTER UPDATE ON emprunts
FOR EACH ROW
BEGIN
    IF NEW.date_retour_effective IS NOT NULL AND OLD.date_retour_effective IS NULL THEN
        UPDATE livres 
        SET exemplaires_disponibles = exemplaires_disponibles + 1 
        WHERE id = NEW.livre_id;
    END IF;
END$$
DELIMITER ;

-- ============================================
-- PROCÉDURES STOCKÉES UTILES
-- ============================================

-- Procédure : Obtenir les livres les plus empruntés
DELIMITER $$
CREATE PROCEDURE livres_populaires(IN limite INT)
BEGIN
    SELECT 
        l.id,
        l.titre,
        l.auteur,
        c.nom AS categorie,
        COUNT(e.id) AS nombre_emprunts
    FROM livres l
    LEFT JOIN emprunts e ON l.id = e.livre_id
    LEFT JOIN categories c ON l.categorie_id = c.id
    GROUP BY l.id, l.titre, l.auteur, c.nom
    ORDER BY nombre_emprunts DESC
    LIMIT limite;
END$$
DELIMITER ;

-- Procédure : Vérifier si un membre peut emprunter
DELIMITER $$
CREATE PROCEDURE peut_emprunter(IN user_id INT, OUT peut BOOLEAN, OUT raison VARCHAR(255))
BEGIN
    DECLARE emprunts_actifs INT;
    DECLARE emprunts_retard INT;
    
    -- Compter emprunts actifs
    SELECT COUNT(*) INTO emprunts_actifs 
    FROM emprunts 
    WHERE utilisateur_id = user_id AND statut = 'en_cours';
    
    -- Compter emprunts en retard
    SELECT COUNT(*) INTO emprunts_retard 
    FROM emprunts 
    WHERE utilisateur_id = user_id AND statut = 'en_retard';
    
    IF emprunts_retard > 0 THEN
        SET peut = FALSE;
        SET raison = 'Vous avez des livres en retard';
    ELSEIF emprunts_actifs >= 3 THEN
        SET peut = FALSE;
        SET raison = 'Limite de 3 emprunts simultanés atteinte';
    ELSE
        SET peut = TRUE;
        SET raison = 'OK';
    END IF;
END$$
DELIMITER ;

-- ============================================
-- INDEX SUPPLÉMENTAIRES POUR PERFORMANCES
-- ============================================
CREATE INDEX idx_emprunt_statut_date ON emprunts(statut, date_retour_prevue);
CREATE INDEX idx_livre_disponibilite ON livres(exemplaires_disponibles);

-- ============================================
-- FIN DU SCRIPT
-- ============================================

-- Afficher les statistiques initiales
SELECT * FROM vue_statistiques;

-- Afficher quelques livres
SELECT * FROM vue_livres_complet LIMIT 5;

-- Note : Le mot de passe par défaut pour tous les utilisateurs est "password123"
-- En production, utilisez password_hash() et password_verify() de PHP
