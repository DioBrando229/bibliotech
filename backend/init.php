<?php
/**
 * Fichier d'initialisation
 * À inclure au début de chaque page
 */

// Démarrer la session si pas encore démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fuseau horaire
date_default_timezone_set('Africa/Porto-Novo');

// Constantes de l'application
define('SITE_NAME', 'BiblioTech');
define('BASE_URL', 'http://localhost/bibliotech/frontend/');

/**
 * Vérifier si l'utilisateur est connecté
 * @return bool
 */
function estConnecte() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

/**
 * Obtenir l'utilisateur connecté
 * @return array|null
 */
function getUtilisateurConnecte() {
    return estConnecte() ? $_SESSION['user'] : null;
}

/**
 * Vérifier le rôle de l'utilisateur
 * @param string $role
 * @return bool
 */
function aLeRole($role) {
    $user = getUtilisateurConnecte();
    return $user && $user['role'] === $role;
}

/**
 * Rediriger vers une page
 * @param string $page
 */
function rediriger($page) {
    header("Location: " . BASE_URL . $page);
    exit();
}

/**
 * Protéger une page (nécessite connexion)
 */
function protegerPage() {
    if (!estConnecte()) {
        $_SESSION['message_erreur'] = "Vous devez être connecté pour accéder à cette page.";
        rediriger('connexion.php');
    }
}

/**
 * Protéger une page admin
 */
function protegerPageAdmin() {
    protegerPage();
    if (!aLeRole('admin')) {
        $_SESSION['message_erreur'] = "Accès non autorisé.";
        rediriger('index.php');
    }
}

/**
 * Protéger une page bibliothécaire
 */
function protegerPageBibliothecaire() {
    protegerPage();
    if (!aLeRole('bibliothecaire') && !aLeRole('admin')) {
        $_SESSION['message_erreur'] = "Accès non autorisé.";
        rediriger('index.php');
    }
}

/**
 * Afficher un message flash
 * @param string $type (success, error, info, warning)
 * @param string $message
 */
function setMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Récupérer et afficher le message flash
 * @return string|null
 */
function afficherMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        
        $class = '';
        switch ($msg['type']) {
            case 'success':
                $class = 'alert-success';
                $icon = '✓';
                break;
            case 'error':
                $class = 'alert-danger';
                $icon = '✗';
                break;
            case 'warning':
                $class = 'alert-warning';
                $icon = '⚠';
                break;
            case 'info':
            default:
                $class = 'alert-info';
                $icon = 'ℹ';
                break;
        }
        
        return '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
                    <strong>' . $icon . '</strong> ' . htmlspecialchars($msg['message']) . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
    }
    
    return null;
}

/**
 * Nettoyer les données d'entrée
 * @param string $data
 * @return string
 */
function nettoyer($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Formater une date
 * @param string $date
 * @param string $format
 * @return string
 */
function formaterDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

/**
 * Calculer les jours restants
 * @param string $date_future
 * @return int
 */
function joursRestants($date_future) {
    $aujourd_hui = new DateTime();
    $date = new DateTime($date_future);
    $interval = $aujourd_hui->diff($date);
    return $interval->days * ($interval->invert ? -1 : 1);
}

/**
 * Déconnexion
 */
function deconnecter() {
    session_unset();
    session_destroy();
    rediriger('index.php');
}

/**
 * Générer un token CSRF
 * @return string
 */
function genererTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier le token CSRF
 * @param string $token
 * @return bool
 */
function verifierTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
