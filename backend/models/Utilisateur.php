<?php
/**
 * Modèle Utilisateur
 * Gère toutes les opérations liées aux utilisateurs
 */

class Utilisateur {
    private $conn;
    private $table = 'utilisateurs';

    // Propriétés
    public $id;
    public $nom;
    public $prenoms;
    public $email;
    public $telephone;
    public $adresse;
    public $mot_de_passe;
    public $role;
    public $statut;
    public $date_inscription;

    /**
     * Constructeur
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Créer un nouvel utilisateur (inscription)
     * @return bool
     */
    public function creer() {
        $query = "INSERT INTO " . $this->table . " 
                  (nom, prenoms, email, telephone, adresse, mot_de_passe, role, statut) 
                  VALUES 
                  (:nom, :prenoms, :email, :telephone, :adresse, :mot_de_passe, :role, :statut)";

        $stmt = $this->conn->prepare($query);

        // Nettoyage des données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenoms = htmlspecialchars(strip_tags($this->prenoms));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));
        
        // Hash du mot de passe
        $password_hash = password_hash($this->mot_de_passe, PASSWORD_BCRYPT);

        // Liaison des paramètres
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenoms', $this->prenoms);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':adresse', $this->adresse);
        $stmt->bindParam(':mot_de_passe', $password_hash);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':statut', $this->statut);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Connexion (vérifier email et mot de passe)
     * @return bool|array
     */
    public function connexion() {
        $query = "SELECT id, nom, prenoms, email, telephone, adresse, mot_de_passe, role, statut 
                  FROM " . $this->table . " 
                  WHERE email = :email 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier le mot de passe
            if (password_verify($this->mot_de_passe, $row['mot_de_passe'])) {
                // Vérifier si le compte est actif
                if ($row['statut'] !== 'actif') {
                    return false; // Compte non activé
                }
                
                // Mettre à jour la dernière connexion
                $this->updateDerniereConnexion($row['id']);
                
                // Retourner les infos de l'utilisateur
                return [
                    'id' => $row['id'],
                    'nom' => $row['nom'],
                    'prenoms' => $row['prenoms'],
                    'email' => $row['email'],
                    'telephone' => $row['telephone'],
                    'adresse' => $row['adresse'],
                    'role' => $row['role']
                ];
            }
        }

        return false;
    }

    /**
     * Mettre à jour la dernière connexion
     */
    private function updateDerniereConnexion($user_id) {
        $query = "UPDATE " . $this->table . " 
                  SET derniere_connexion = NOW() 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
    }

    /**
     * Vérifier si un email existe déjà
     * @return bool
     */
    public function emailExiste() {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE email = :email 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Obtenir un utilisateur par ID
     * @return array|bool
     */
    public function getById($id) {
        $query = "SELECT id, nom, prenoms, email, telephone, adresse, role, statut, date_inscription 
                  FROM " . $this->table . " 
                  WHERE id = :id 
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
     * Obtenir tous les membres
     * @return array
     */
    public function getTousMembres($statut = null) {
        $query = "SELECT id, nom, prenoms, email, telephone, statut, date_inscription 
                  FROM " . $this->table . " 
                  WHERE role = 'membre'";
        
        if ($statut) {
            $query .= " AND statut = :statut";
        }
        
        $query .= " ORDER BY date_inscription DESC";

        $stmt = $this->conn->prepare($query);
        
        if ($statut) {
            $stmt->bindParam(':statut', $statut);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mettre à jour le profil
     * @return bool
     */
    public function updateProfil() {
        $query = "UPDATE " . $this->table . " 
                  SET nom = :nom, 
                      prenoms = :prenoms, 
                      telephone = :telephone, 
                      adresse = :adresse 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Nettoyage
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->prenoms = htmlspecialchars(strip_tags($this->prenoms));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));

        // Liaison
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':prenoms', $this->prenoms);
        $stmt->bindParam(':telephone', $this->telephone);
        $stmt->bindParam(':adresse', $this->adresse);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }

    /**
     * Changer le statut d'un utilisateur
     * @return bool
     */
    public function changerStatut($user_id, $nouveau_statut) {
        $query = "UPDATE " . $this->table . " 
                  SET statut = :statut 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':statut', $nouveau_statut);
        $stmt->bindParam(':id', $user_id);

        return $stmt->execute();
    }

    /**
     * Compter les membres actifs
     * @return int
     */
    public function compterMembresActifs() {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table . " 
                  WHERE role = 'membre' AND statut = 'actif'";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>
