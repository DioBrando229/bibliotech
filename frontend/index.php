<?php
require_once '../backend/init.php';
require_once '../backend/config/database.php';
require_once '../backend/models/Livre.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Récupérer les livres populaires
$livre = new Livre($db);
$livres_populaires = $livre->getLivresPopulaires(4);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Votre bibliothèque digitale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- HERO SECTION -->
    <section class="hero bg-primary text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-3">BiblioTech</h1>
            <p class="lead mb-4">Votre bibliothèque digitale et moderne</p>
            <p class="mb-4">Accédez à des milliers de livres, réservez en ligne et gérez vos emprunts facilement</p>
            <a href="catalogue.php" class="btn btn-warning btn-lg">Découvrir le catalogue</a>
        </div>
    </section>

    <!-- MESSAGES -->
    <div class="container mt-4">
        <?php echo afficherMessage(); ?>
    </div>

    <!-- LIVRES POPULAIRES -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Livres populaires du moment</h2>
            <div class="row g-4">
                <?php foreach ($livres_populaires as $livre_item): ?>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                            <?php if (isset($livre_item['image_couverture']) && !empty($livre_item['image_couverture'])): ?>
                                <img src="<?php echo htmlspecialchars($livre_item['image_couverture']); ?>" alt="Couverture" class="img-fluid">
                            <?php else: ?>
                                <span class="display-1">📚</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($livre_item['titre']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($livre_item['auteur']); ?></p>
                            <span class="badge bg-<?php echo $livre_item['exemplaires_disponibles'] > 0 ? 'success' : 'danger'; ?>">
                                <?php echo $livre_item['exemplaires_disponibles'] > 0 ? 'Disponible' : 'Emprunté'; ?>
                            </span>
                        </div>
                        <div class="card-footer">
                            <a href="livre-details.php?id=<?php echo $livre_item['id']; ?>" class="btn btn-primary w-100">Voir détails</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- À PROPOS -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center">
                    <span class="display-1">📚</span>
                </div>
                <div class="col-md-6">
                    <h3 class="mb-4">À propos de BiblioTech</h3>
                    <p>BiblioTech est une bibliothèque communautaire qui facilite l'accès à la lecture et au savoir pour tous. Notre plateforme digitale vous permet de gérer vos emprunts en toute simplicité.</p>
                    <ul class="list-unstyled">
                        <li class="mb-2">✓ Plus de 5000 ouvrages disponibles</li>
                        <li class="mb-2">✓ Gestion digitale et intuitive</li>
                        <li class="mb-2">✓ Réservation en ligne 24/7</li>
                        <li class="mb-2">✓ Espace personnel sécurisé</li>
                        <li class="mb-2">✓ Accès gratuit pour tous les membres</li>
                    </ul>
                    <a href="a-propos.php" class="btn btn-primary">En savoir plus</a>
                </div>
            </div>
        </div>
    </section>

    <!-- COMMENT ÇA MARCHE -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Comment ça marche ?</h2>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <span class="fs-1">1</span>
                        </div>
                    </div>
                    <span class="display-4">👤</span>
                    <h4 class="mt-3">Inscrivez-vous</h4>
                    <p class="text-muted">Créez votre compte gratuitement en quelques clics</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <span class="fs-1">2</span>
                        </div>
                    </div>
                    <span class="display-4">🔍</span>
                    <h4 class="mt-3">Recherchez</h4>
                    <p class="text-muted">Parcourez notre catalogue et trouvez le livre parfait</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <span class="fs-1">3</span>
                        </div>
                    </div>
                    <span class="display-4">📖</span>
                    <h4 class="mt-3">Réservez et retirez</h4>
                    <p class="text-muted">Réservez en ligne et venez retirer votre livre</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
