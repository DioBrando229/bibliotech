<?php
require_once '../backend/init.php';
require_once '../backend/config/database.php';
require_once '../backend/models/Livre.php';

// Vérifier l'ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setMessage('error', 'Livre introuvable.');
    rediriger('catalogue.php');
}

$livre_id = (int)$_GET['id'];

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

$livre = new Livre($db);
$livre_info = $livre->getById($livre_id);

if (!$livre_info) {
    setMessage('error', 'Livre introuvable.');
    rediriger('catalogue.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($livre_info['titre']); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container my-5">
        <a href="catalogue.php" class="btn btn-outline-secondary mb-4">← Retour au catalogue</a>

        <?php echo afficherMessage(); ?>

        <div class="row">
            <!-- IMAGE -->
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <?php if (isset($livre_info['image_couverture']) && !empty($livre_info['image_couverture'])): ?>
                            <img src="<?php echo htmlspecialchars($livre_info['image_couverture']); ?>" 
                                 alt="Couverture" class="img-fluid" style="max-height: 500px;">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 500px;">
                                <span style="font-size: 8rem;">📚</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- DÉTAILS -->
            <div class="col-md-8">
                <h1 class="mb-3"><?php echo htmlspecialchars($livre_info['titre']); ?></h1>
                <h5 class="text-muted mb-4">Par <?php echo htmlspecialchars($livre_info['auteur']); ?></h5>

                <div class="mb-4">
                    <span class="badge bg-secondary fs-6">
                        <?php echo htmlspecialchars($livre_info['categorie']); ?>
                    </span>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <th width="200">ISBN :</th>
                        <td><?php echo htmlspecialchars($livre_info['isbn'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Statut :</th>
                        <td>
                            <span class="badge bg-<?php echo $livre_info['exemplaires_disponibles'] > 0 ? 'success' : 'danger'; ?> fs-6">
                                📚 <?php echo $livre_info['statut']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Exemplaires :</th>
                        <td>
                            <?php echo $livre_info['exemplaires_disponibles']; ?> disponible(s) 
                            sur <?php echo $livre_info['nombre_exemplaires']; ?>
                        </td>
                    </tr>
                </table>

                <?php if ($livre_info['description']): ?>
                <div class="mt-4">
                    <h5>Description</h5>
                    <p class="text-justify"><?php echo nl2br(htmlspecialchars($livre_info['description'])); ?></p>
                </div>
                <?php endif; ?>

                <!-- ACTIONS -->
                <div class="mt-4">
                    <?php if (estConnecte()): ?>
                        <?php if ($livre_info['exemplaires_disponibles'] > 0): ?>
                            <div class="alert alert-info">
                                ✓ Ce livre est disponible ! Venez le retirer à la bibliothèque.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Ce livre est actuellement emprunté. Vous pouvez le réserver.
                            </div>
                            <!-- TODO: Ajouter le système de réservation -->
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <strong>Vous devez être connecté</strong> pour emprunter ou réserver ce livre.
                        </div>
                        <a href="connexion.php" class="btn btn-primary">Se connecter</a>
                        <a href="inscription.php" class="btn btn-outline-primary">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
