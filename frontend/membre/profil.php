<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Utilisateur.php';

// Protéger la page
protegerPage();
$user = getUtilisateurConnecte();

$erreur = '';
$succes = false;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = nettoyer($_POST['nom']);
    $prenoms = nettoyer($_POST['prenoms']);
    $telephone = nettoyer($_POST['telephone']);
    $adresse = nettoyer($_POST['adresse']);

    if (empty($nom) || empty($prenoms) || empty($telephone)) {
        $erreur = "Tous les champs marqués * sont obligatoires.";
    } else {
        $database = new Database();
        $db = $database->getConnection();

        $utilisateur = new Utilisateur($db);
        $utilisateur->id = $user['id'];
        $utilisateur->nom = $nom;
        $utilisateur->prenoms = $prenoms;
        $utilisateur->telephone = $telephone;
        $utilisateur->adresse = $adresse;

        if ($utilisateur->updateProfil()) {
            // Mettre à jour la session
            $_SESSION['user']['nom'] = $nom;
            $_SESSION['user']['prenoms'] = $prenoms;
            $_SESSION['user']['telephone'] = $telephone;
            $_SESSION['user']['adresse'] = $adresse;
            
            $user = getUtilisateurConnecte();
            setMessage('success', 'Profil mis à jour avec succès !');
            $succes = true;
        } else {
            $erreur = "Erreur lors de la mise à jour.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <?php include '../includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-md-3">
                <div class="sidebar">
                    <h5 class="mb-3">MENU</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">🏠 Tableau de bord</a>
                        <a class="nav-link active" href="profil.php">👤 Mon profil</a>
                        <a class="nav-link" href="emprunts.php">📚 Mes emprunts</a>
                        <a class="nav-link" href="reservations.php">🔖 Mes réservations</a>
                        <a class="nav-link" href="../catalogue.php">📖 Catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <!-- MAIN CONTENT -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Mon profil</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($erreur): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>✗</strong> <?php echo htmlspecialchars($erreur); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>

                        <?php echo afficherMessage(); ?>

                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenoms" class="form-label">Prénoms *</label>
                                    <input type="text" class="form-control" id="prenoms" name="prenoms" 
                                           value="<?php echo htmlspecialchars($user['prenoms']); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <small class="text-muted">L'email ne peut pas être modifié</small>
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($user['telephone']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <textarea class="form-control" id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($user['adresse'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        </form>

                        <hr class="my-4">

                        <h6>Changer le mot de passe</h6>
                        <p class="text-muted">Pour changer votre mot de passe, veuillez contacter un bibliothécaire.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
