<?php
require_once '../backend/init.php';
require_once '../backend/config/database.php';
require_once '../backend/models/Utilisateur.php';

// Si déjà connecté, rediriger
if (estConnecte()) {
    $user = getUtilisateurConnecte();
    switch ($user['role']) {
        case 'admin':
            rediriger('admin/dashboard.php');
            break;
        case 'bibliothecaire':
            rediriger('bibliothecaire/dashboard.php');
            break;
        default:
            rediriger('membre/dashboard.php');
            break;
    }
}

$erreur = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = nettoyer($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    if (empty($email) || empty($mot_de_passe)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        // Connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();

        $utilisateur = new Utilisateur($db);
        $utilisateur->email = $email;
        $utilisateur->mot_de_passe = $mot_de_passe;

        $result = $utilisateur->connexion();

        if ($result) {
            // Connexion réussie
            $_SESSION['user'] = $result;
            setMessage('success', 'Connexion réussie ! Bienvenue ' . $result['prenoms'] . ' !');

            // Redirection selon le rôle
            switch ($result['role']) {
                case 'admin':
                    rediriger('admin/dashboard.php');
                    break;
                case 'bibliothecaire':
                    rediriger('bibliothecaire/dashboard.php');
                    break;
                default:
                    rediriger('membre/dashboard.php');
                    break;
            }
        } else {
            $erreur = "Email ou mot de passe incorrect, ou compte non activé.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <h1 class="fw-bold">
                        <span class="text-primary">Biblio</span><span class="text-info">Tech</span>
                    </h1>
                </div>
                
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">Connexion</h3>

                        <?php if ($erreur): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>✗</strong> <?php echo htmlspecialchars($erreur); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                       required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="se_souvenir">
                                <label class="form-check-label" for="se_souvenir">Se souvenir de moi</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Se connecter</button>

                            <div class="text-center">
                                <a href="#" class="text-decoration-none small">Mot de passe oublié ?</a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <p class="mb-0">Pas encore membre ? 
                            <a href="inscription.php" class="text-decoration-none fw-bold">S'inscrire</a>
                        </p>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none">← Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
