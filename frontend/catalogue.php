<?php
require_once '../backend/init.php';
require_once '../backend/config/database.php';
require_once '../backend/models/Livre.php';

// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

$livre = new Livre($db);

// Récupérer les filtres
$filters = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters['search'] = nettoyer($_GET['search']);
}
if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    $filters['categorie'] = nettoyer($_GET['categorie']);
}
if (isset($_GET['disponible']) && $_GET['disponible'] === '1') {
    $filters['disponible'] = true;
}

// Récupérer les livres
$livres = $livre->getTous($filters);

// Récupérer les catégories pour les filtres
$query_cat = "SELECT * FROM categories ORDER BY nom ASC";
$stmt = $db->prepare($query_cat);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- PAGE HEADER -->
    <div class="bg-primary text-white py-4">
        <div class="container">
            <h1 class="mb-2">📚 Catalogue de Livres</h1>
            <p class="mb-0">Découvrez notre collection de plus de <?php echo count($livres); ?> ouvrages</p>
        </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="container my-4">
        <form method="GET" action="catalogue.php">
            <div class="input-group">
                <input type="text" class="form-control" name="search" 
                       placeholder="Rechercher par titre, auteur, ISBN..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button class="btn btn-primary" type="submit">🔍 Rechercher</button>
            </div>
        </form>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container mb-5">
        <div class="row">
            <!-- SIDEBAR FILTERS -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Filtres</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <!-- Recherche (conserver) -->
                            <?php if (isset($_GET['search'])): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($_GET['search']); ?>">
                            <?php endif; ?>

                            <h6 class="mb-3">Catégories</h6>
                            <div class="mb-3">
                                <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="categorie" 
                                           value="<?php echo $cat['id']; ?>" 
                                           id="cat<?php echo $cat['id']; ?>"
                                           <?php echo (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cat<?php echo $cat['id']; ?>">
                                        <?php echo htmlspecialchars($cat['nom']); ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <h6 class="mb-3">Disponibilité</h6>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="disponible" value="1" 
                                       id="disponible"
                                       <?php echo (isset($_GET['disponible']) && $_GET['disponible'] == '1') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="disponible">
                                    Disponibles uniquement
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Appliquer</button>
                            <a href="catalogue.php" class="btn btn-outline-secondary w-100 mt-2">Réinitialiser</a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- CATALOG -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="mb-0"><strong><?php echo count($livres); ?></strong> livre(s) trouvé(s)</p>
                </div>

                <?php if (empty($livres)): ?>
                <div class="alert alert-info">
                    Aucun livre trouvé avec ces critères. Essayez d'élargir votre recherche.
                </div>
                <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($livres as $livre_item): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                <?php if (isset($livre_item['image_couverture']) && !empty($livre_item['image_couverture'])): ?>
                                    <img src="<?php echo htmlspecialchars($livre_item['image_couverture']); ?>" alt="Couverture" class="img-fluid">
                                <?php else: ?>
                                    <span style="font-size: 4rem;">📚</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <span class="badge bg-secondary mb-2"><?php echo htmlspecialchars($livre_item['categorie']); ?></span>
                                <h6 class="card-title"><?php echo htmlspecialchars($livre_item['titre']); ?></h6>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($livre_item['auteur']); ?></p>
                                <span class="badge bg-<?php echo $livre_item['exemplaires_disponibles'] > 0 ? 'success' : 'danger'; ?>">
                                    <?php echo $livre_item['statut']; ?>
                                </span>
                            </div>
                            <div class="card-footer">
                                <a href="livre-details.php?id=<?php echo $livre_item['id']; ?>" class="btn btn-outline-primary btn-sm w-100">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
