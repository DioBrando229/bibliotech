<?php
/**
 * Modèle Emprunt
 * Gère toutes les opérations liées aux emprunts
 */

class Emprunt {
    private $conn;
    private $table = 'emprunts';

    // Propriétés
    public $id;
    public $utilisateur_id;
    public $livre_id;
    public $date_emprunt;
    public $date_retour_prevue;
    public $date_retour_effective;
    public $statut;
    public $enregistre_par;

    // Durée d'emprunt en jours
    const DUREE_EMPRUNT = 14;
    const MAX_EMPRUNTS = 3;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer un nouvel emprunt
     * @return bool|string
     */
    public function creer() {
        // Vérifier si l'utilisateur peut emprunter
        $verification = $this->peutEmprunter($this->utilisateur_id);
        if ($verification !== true) {
            return $verification; // Retourne le message d'erreur
        }

        // Vérifier la disponibilité du livre
        $livre = new Livre($this->conn);
        if (!$livre->estDisponible($this->livre_id)) {
            return "Ce livre n'est pas disponible";
        }

        // Calculer la date de retour prévue
        $this->date_retour_prevue = date('Y-m-d', strtotime('+' . self::DUREE_EMPRUNT . ' days'));

        $query = "INSERT INTO " . $this->table . " 
                  (utilisateur_id, livre_id, date_emprunt, date_retour_prevue, enregistre_par) 
                  VALUES 
                  (:utilisateur_id, :livre_id, CURDATE(), :date_retour_prevue, :enregistre_par)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':utilisateur_id', $this->utilisateur_id);
        $stmt->bindParam(':livre_id', $this->livre_id);
        $stmt->bindParam(':date_retour_prevue', $this->date_retour_prevue);
        $stmt->bindParam(':enregistre_par', $this->enregistre_par);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return "Erreur lors de l'enregistrement de l'emprunt";
    }

    /**
     * Vérifier si un utilisateur peut emprunter
     * @return bool|string
     */
    private function peutEmprunter($user_id) {
        // Vérifier les emprunts en retard
        $query_retard = "SELECT COUNT(*) as total 
                         FROM " . $this->table . " 
                         WHERE utilisateur_id = :user_id AND statut = 'en_retard'";
        
        $stmt = $this->conn->prepare($query_retard);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] > 0) {
            return "Vous avez des livres en retard. Veuillez les retourner avant d'emprunter.";
        }

        // Vérifier le nombre d'emprunts actifs
        $query_actifs = "SELECT COUNT(*) as total 
                         FROM " . $this->table . " 
                         WHERE utilisateur_id = :user_id AND statut = 'en_cours'";
        
        $stmt = $this->conn->prepare($query_actifs);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row['total'] >= self::MAX_EMPRUNTS) {
            return "Limite de " . self::MAX_EMPRUNTS . " emprunts simultanés atteinte.";
        }

        return true;
    }

    /**
     * Enregistrer le retour d'un livre
     * @return bool
     */
    public function enregistrerRetour($emprunt_id) {
        $query = "UPDATE " . $this->table . " 
                  SET date_retour_effective = CURDATE(), 
                      statut = 'retourne' 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $emprunt_id);

        return $stmt->execute();
    }

    /**
     * Obtenir les emprunts d'un utilisateur
     * @return array
     */
    public function getParUtilisateur($user_id, $statut = null) {
        $query = "SELECT 
                    e.id, e.date_emprunt, e.date_retour_prevue, e.date_retour_effective, e.statut,
                    DATEDIFF(e.date_retour_prevue, CURDATE()) as jours_restants,
                    l.id as livre_id, l.titre, l.auteur, l.image_couverture,
                    c.nom as categorie
                  FROM " . $this->table . " e
                  JOIN livres l ON e.livre_id = l.id
                  LEFT JOIN categories c ON l.categorie_id = c.id
                  WHERE e.utilisateur_id = :user_id";

        if ($statut) {
            $query .= " AND e.statut = :statut";
        }

        $query .= " ORDER BY e.date_emprunt DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($statut) {
            $stmt->bindParam(':statut', $statut);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir tous les emprunts actifs
     * @return array
     */
    public function getTousActifs() {
        $query = "SELECT 
                    e.id, e.date_emprunt, e.date_retour_prevue, e.statut,
                    DATEDIFF(e.date_retour_prevue, CURDATE()) as jours_restants,
                    CONCAT(u.prenoms, ' ', u.nom) as membre,
                    u.email, u.telephone,
                    l.titre, l.auteur
                  FROM " . $this->table . " e
                  JOIN utilisateurs u ON e.utilisateur_id = u.id
                  JOIN livres l ON e.livre_id = l.id
                  WHERE e.statut IN ('en_cours', 'en_retard')
                  ORDER BY e.date_retour_prevue ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtenir les emprunts en retard
     * @return array
     */
    public function getEnRetard() {
        $query = "SELECT 
                    e.id, e.date_emprunt, e.date_retour_prevue,
                    DATEDIFF(CURDATE(), e.date_retour_prevue) as jours_retard,
                    CONCAT(u.prenoms, ' ', u.nom) as membre,
                    u.email, u.telephone,
                    l.titre, l.auteur
                  FROM " . $this->table . " e
                  JOIN utilisateurs u ON e.utilisateur_id = u.id
                  JOIN livres l ON e.livre_id = l.id
                  WHERE e.statut = 'en_retard'
                  ORDER BY e.date_retour_prevue ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compter les emprunts actifs
     * @return int
     */
    public function compterActifs() {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE statut = 'en_cours'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Compter les emprunts du mois
     * @return int
     */
    public function compterDuMois() {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE MONTH(date_emprunt) = MONTH(CURDATE()) 
                  AND YEAR(date_emprunt) = YEAR(CURDATE())";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    /**
     * Mettre à jour automatiquement les emprunts en retard
     * @return bool
     */
    public function updateEmpruntsRetard() {
        $query = "UPDATE " . $this->table . " 
                  SET statut = 'en_retard' 
                  WHERE statut = 'en_cours' 
                  AND date_retour_prevue < CURDATE()";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}
?>
