<?php
require_once '../backend/init.php';
require_once '../backend/config/database.php';
require_once '../backend/models/Utilisateur.php';

// Si déjà connecté, rediriger
if (estConnecte()) {
    rediriger('membre/dashboard.php');
}

$erreur = '';
$succes = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = nettoyer($_POST['nom']);
    $prenoms = nettoyer($_POST['prenoms']);
    $email = nettoyer($_POST['email']);
    $telephone = nettoyer($_POST['telephone']);
    $adresse = nettoyer($_POST['adresse']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmer_mdp = $_POST['confirmer_mdp'];

    // Validation
    if (empty($nom) || empty($prenoms) || empty($email) || empty($telephone) || empty($mot_de_passe)) {
        $erreur = "Tous les champs marqués * sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Format d'email invalide.";
    } elseif (strlen($mot_de_passe) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($mot_de_passe !== $confirmer_mdp) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Connexion à la base de données
        $database = new Database();
        $db = $database->getConnection();

        $utilisateur = new Utilisateur($db);
        $utilisateur->email = $email;

        // Vérifier si l'email existe déjà
        if ($utilisateur->emailExiste()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            // Créer le compte
            $utilisateur->nom = $nom;
            $utilisateur->prenoms = $prenoms;
            $utilisateur->telephone = $telephone;
            $utilisateur->adresse = $adresse;
            $utilisateur->mot_de_passe = $mot_de_passe;
            $utilisateur->role = 'membre';
            $utilisateur->statut = 'en_attente'; // Nécessite validation

            if ($utilisateur->creer()) {
                $succes = true;
            } else {
                $erreur = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-md-6">
                <div class="text-center mb-4">
                    <h1 class="fw-bold">
                        <span class="text-primary">Biblio</span><span class="text-info">Tech</span>
                    </h1>
                </div>
                
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">Inscription</h3>

                        <?php if ($succes): ?>
                        <div class="alert alert-success" role="alert">
                            <h4 class="alert-heading">✓ Inscription réussie !</h4>
                            <p>Votre compte a été créé avec succès. Il sera activé par un bibliothécaire dans les plus brefs délais.</p>
                            <p class="mb-0">Vous recevrez un email de confirmation une fois votre compte activé.</p>
                            <hr>
                            <a href="connexion.php" class="btn btn-success">Se connecter</a>
                        </div>
                        <?php else: ?>

                        <?php if ($erreur): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>✗</strong> <?php echo htmlspecialchars($erreur); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?php echo isset($nom) ? htmlspecialchars($nom) : ''; ?>" 
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="prenoms" class="form-label">Prénoms *</label>
                                    <input type="text" class="form-control" id="prenoms" name="prenoms" 
                                           value="<?php echo isset($prenoms) ? htmlspecialchars($prenoms) : ''; ?>" 
                                           required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?php echo isset($telephone) ? htmlspecialchars($telephone) : ''; ?>" 
                                       placeholder="+229 XX XX XX XX"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <textarea class="form-control" id="adresse" name="adresse" rows="2"><?php echo isset($adresse) ? htmlspecialchars($adresse) : ''; ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="mot_de_passe" class="form-label">Mot de passe *</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" 
                                       minlength="6" required>
                                <div class="form-text">Minimum 6 caractères</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirmer_mdp" class="form-label">Confirmer le mot de passe *</label>
                                <input type="password" class="form-control" id="confirmer_mdp" name="confirmer_mdp" 
                                       minlength="6" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="accepter" required>
                                <label class="form-check-label" for="accepter">
                                    J'accepte les conditions d'utilisation
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
                        </form>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <p class="mb-0">Déjà membre ? 
                            <a href="connexion.php" class="text-decoration-none fw-bold">Se connecter</a>
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
    <script>
        // Validation côté client
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('mot_de_passe').value;
            const confirm = document.getElementById('confirmer_mdp').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas !');
                return false;
            }
        });
    </script>
</body>
</html>
