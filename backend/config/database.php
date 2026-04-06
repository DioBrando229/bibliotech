<?php
/**
 * Configuration de la base de données BiblioTech
 * 
 * Ce fichier contient les paramètres de connexion à la base de données
 * et la classe Database pour gérer la connexion
 */

// Paramètres de connexion
define('DB_HOST', 'localhost');
define('DB_NAME', 'bibliotech');
define('DB_USER', 'root');  // À modifier selon votre configuration
define('DB_PASS', '');      // À modifier selon votre configuration
define('DB_CHARSET', 'utf8mb4');

/**
 * Classe Database
 * Gère la connexion à la base de données avec PDO
 */
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $conn = null;

    /**
     * Établir la connexion à la base de données
     * @return PDO|null
     */
    public function getConnection() {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            return $this->conn;
        } catch(PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            return null;
        }
    }

    /**
     * Fermer la connexion
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
