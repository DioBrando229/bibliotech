<?php
require_once '../backend/init.php';

$succes = false;
$erreur = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = nettoyer($_POST['nom']);
    $email = nettoyer($_POST['email']);
    $sujet = nettoyer($_POST['sujet']);
    $message = nettoyer($_POST['message']);
    
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } else {
        // Ici vous pouvez envoyer un email ou sauvegarder dans la BDD
        // Pour l'instant, on simule juste le succès
        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="bg-primary text-white py-4">
        <div class="container">
            <h1 class="mb-2">Contactez-nous</h1>
            <p class="mb-0">Nous sommes à votre écoute</p>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <!-- FORMULAIRE -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Envoyez-nous un message</h3>

                        <?php if ($succes): ?>
                        <div class="alert alert-success">
                            <strong>✓ Message envoyé !</strong> Nous vous répondrons dans les plus brefs délais.
                        </div>
                        <?php endif; ?>

                        <?php if ($erreur): ?>
                        <div class="alert alert-danger">
                            <strong>✗</strong> <?php echo htmlspecialchars($erreur); ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo isset($nom) ? htmlspecialchars($nom) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="sujet" class="form-label">Sujet *</label>
                                <select class="form-select" id="sujet" name="sujet" required>
                                    <option value="">Choisir un sujet</option>
                                    <option value="inscription">Question sur l'inscription</option>
                                    <option value="emprunt">Question sur un emprunt</option>
                                    <option value="catalogue">Question sur le catalogue</option>
                                    <option value="autre">Autre question</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Envoyer le message</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- INFORMATIONS -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">📍 Notre Adresse</h5>
                        <p class="card-text">
                            BiblioTech<br>
                            Abomey-Calavi<br>
                            Bénin
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">📧 Email</h5>
                        <p class="card-text">
                            <a href="mailto:contact@bibliotech.bj">contact@bibliotech.bj</a>
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">📱 Téléphone</h5>
                        <p class="card-text">
                            +229 XX XX XX XX
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">🕐 Horaires</h5>
                        <p class="card-text">
                            <strong>Lun-Ven :</strong> 8h - 18h<br>
                            <strong>Samedi :</strong> 9h - 13h<br>
                            <strong>Dimanche :</strong> Fermé
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
