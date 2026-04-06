<?php
/**
 * Modèle Livre
 * Gère toutes les opérations liées aux livres
 */

class Livre {
    private $conn;
    private $table = 'livres';

    // Propriétés
    public $id;
    public $titre;
    public $auteur;
    public $isbn;
    public $categorie_id;
    public $description;
    public $image_couverture;
    public $nombre_exemplaires;
    public $exemplaires_disponibles;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtenir tous les livres avec informations de catégorie
     * @return array
     */
    public function getTous($filters = []) {
        $query = "SELECT 
                    l.id, l.titre, l.auteur, l.isbn, l.description, 
                    l.image_couverture, l.nombre_exemplaires, l.exemplaires_disponibles,
                    c.nom as categorie,
                    CASE 
                        WHEN l.exemplaires_disponibles > 0 THEN 'Disponible'
                        ELSE 'Emprunté'
                    END AS statut
                  FROM " . $this->table . " l
                  LEFT JOIN categories c ON l.categorie_id = c.id
                  WHERE 1=1";

        // Filtres
        if (isset($filters['categorie']) && !empty($filters['categorie'])) {
            $query .= " AND l.categorie_id = :categorie";
        }

        if (isset($filters['disponible']) && $filters['disponible'] === true) {
            $query .= " AND l.exemplaires_disponibles > 0";
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= " AND (l.titre LIKE :search_titre OR l.auteur LIKE :search_auteur OR l.isbn LIKE :search_isbn)";
        }

        $query .= " ORDER BY l.date_ajout DESC";

        $stmt = $this->conn->prepare($query);

        // Bind des paramètres
        if (isset($filters['categorie']) && !empty($filters['categorie'])) {
            $stmt->bindValue(':categorie', $filters['categorie'], PDO::PARAM_INT);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $search_term = "%" . $filters['search'] . "%";
            $stmt->bindValue(':search_titre', $search_term, PDO::PARAM_STR);
            $stmt->bindValue(':search_auteur', $search_term, PDO::PARAM_STR);
            $stmt->bindValue(':search_isbn', $search_term, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir un livre par ID
     * @return array|bool
     */
    public function getById($id) {
        $query = "SELECT 
                    l.id, l.titre, l.auteur, l.isbn, l.description, 
                    l.image_couverture, l.nombre_exemplaires, l.exemplaires_disponibles,
                    l.categorie_id, c.nom as categorie,
                    CASE 
                        WHEN l.exemplaires_disponibles > 0 THEN 'Disponible'
                        ELSE 'Emprunté'
                    END AS statut
                  FROM " . $this->table . " l
                  LEFT JOIN categories c ON l.categorie_id = c.id
                  WHERE l.id = :id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return false;
    }

    /**
     * Créer un nouveau livre
     * @return bool
     */
    public function creer() {
        $query = "INSERT INTO " . $this->table . " 
                  (titre, auteur, isbn, categorie_id, description, image_couverture, nombre_exemplaires, exemplaires_disponibles) 
                  VALUES 
                  (:titre, :auteur, :isbn, :categorie_id, :description, :image_couverture, :nombre_exemplaires, :exemplaires_disponibles)";

        $stmt = $this->conn->prepare($query);

        // Nettoyage
        $this->titre = htmlspecialchars(strip_tags($this->titre));
        $this->auteur = htmlspecialchars(strip_tags($this->auteur));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Liaison
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':auteur', $this->auteur);
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':categorie_id', $this->categorie_id);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':image_couverture', $this->image_couverture);
        $stmt->bindParam(':nombre_exemplaires', $this->nombre_exemplaires);
        $stmt->bindParam(':exemplaires_disponibles', $this->exemplaires_disponibles);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Mettre à jour un livre
     * @return bool
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET titre = :titre, 
                      auteur = :auteur, 
                      isbn = :isbn, 
                      categorie_id = :categorie_id, 
                      description = :description,
                      nombre_exemplaires = :nombre_exemplaires
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage
        $this->titre = htmlspecialchars(strip_tags($this->titre));
        $this->auteur = htmlspecialchars(strip_tags($this->auteur));
        $this->isbn = htmlspecialchars(strip_tags($this->isbn));
        $this->description = htmlspecialchars(strip_tags($this->description));

        // Liaison
        $stmt->bindParam(':titre', $this->titre);
        $stmt->bindParam(':auteur', $this->auteur);
        $stmt->bindParam(':isbn', $this->isbn);
        $stmt->bindParam(':categorie_id', $this->categorie_id);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':nombre_exemplaires', $this->nombre_exemplaires);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Supprimer un livre
     * @return bool
     */
    public function supprimer($id) {
        // Vérifier d'abord si le livre n'a pas d'emprunts actifs
        $query_check = "SELECT COUNT(*) as total 
                        FROM emprunts 
                        WHERE livre_id = :id AND statut = 'en_cours'";
        
        $stmt_check = $this->conn->prepare($query_check);
        $stmt_check->bindParam(':id', $id);
        $stmt_check->execute();
        
        $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] > 0) {
            return false; // Ne peut pas supprimer un livre emprunté
        }

        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Rechercher des livres
     * @return array
     */
    public function rechercher($terme) {
        $query = "SELECT 
                    l.id, l.titre, l.auteur, l.isbn, 
                    l.exemplaires_disponibles, c.nom as categorie
                  FROM " . $this->table . " l
                  LEFT JOIN categories c ON l.categorie_id = c.id
                  WHERE l.titre LIKE :terme 
                     OR l.auteur LIKE :terme 
                     OR l.isbn LIKE :terme
                  ORDER BY l.titre ASC";

        $stmt = $this->conn->prepare($query);
        $search_term = "%" . $terme . "%";
        $stmt->bindParam(':terme', $search_term);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les livres populaires
     * @return array
     */
    public function getLivresPopulaires($limite = 10) {
        $query = "SELECT 
                    l.id, l.titre, l.auteur, c.nom as categorie,
                    l.exemplaires_disponibles,
                    COUNT(e.id) as nombre_emprunts
                  FROM " . $this->table . " l
                  LEFT JOIN categories c ON l.categorie_id = c.id
                  LEFT JOIN emprunts e ON l.id = e.livre_id
                  GROUP BY l.id, l.titre, l.auteur, c.nom, l.exemplaires_disponibles
                  ORDER BY nombre_emprunts DESC
                  LIMIT :limite";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compter le nombre total de livres
     * @return int
     */
    public function compterTotal() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Vérifier la disponibilité d'un livre
     * @return bool
     */
    public function estDisponible($livre_id) {
        $query = "SELECT exemplaires_disponibles 
                  FROM " . $this->table . " 
                  WHERE id = :id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $livre_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['exemplaires_disponibles'] > 0;
        }

        return false;
    }
}
?>
