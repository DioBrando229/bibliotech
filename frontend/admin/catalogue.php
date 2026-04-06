<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Livre.php';

protegerPageAdmin();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

// Récupérer toutes les catégories
$query_cat = "SELECT * FROM categories ORDER BY nom ASC";
$stmt_cat = $db->prepare($query_cat);
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Traitement ajout livre
if (isset($_POST['ajouter_livre'])) {
    $titre = nettoyer($_POST['titre']);
    $auteur = nettoyer($_POST['auteur']);
    $isbn = nettoyer($_POST['isbn']);
    $categorie_id = (int)$_POST['categorie_id'];
    $description = nettoyer($_POST['description']);
    $nombre_exemplaires = (int)$_POST['nombre_exemplaires'];
    
    $livre = new Livre($db);
    $livre->titre = $titre;
    $livre->auteur = $auteur;
    $livre->isbn = $isbn;
    $livre->categorie_id = $categorie_id;
    $livre->description = $description;
    $livre->nombre_exemplaires = $nombre_exemplaires;
    $livre->exemplaires_disponibles = $nombre_exemplaires;
    
    if ($livre->creer()) {
        setMessage('success', 'Livre ajouté avec succès !');
    } else {
        setMessage('error', 'Erreur lors de l\'ajout du livre.');
    }
    rediriger('admin/catalogue.php');
}

// Récupérer tous les livres
$livre = new Livre($db);
$livres = $livre->getTous();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Catalogue - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <?php include '../includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                    <h5 class="mb-3">ADMIN</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">📊 Tableau de bord</a>
                        <a class="nav-link active" href="catalogue.php">📚 Gestion catalogue</a>
                        <a class="nav-link" href="utilisateurs.php">👥 Gestion utilisateurs</a>
                        <a class="nav-link" href="../catalogue.php">📖 Voir catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestion du Catalogue</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajouterLivreModal">
                        ➕ Ajouter un livre
                    </button>
                </div>

                <?php echo afficherMessage(); ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Auteur</th>
                                        <th>ISBN</th>
                                        <th>Exemplaires</th>
                                        <th>Disponibles</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($livres as $livre_item): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($livre_item['titre']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($livre_item['auteur']); ?></td>
                                        <td><?php echo htmlspecialchars($livre_item['isbn'] ?? 'N/A'); ?></td>
                                        <td><?php echo $livre_item['nombre_exemplaires']; ?></td>
                                        <td><?php echo $livre_item['exemplaires_disponibles']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $livre_item['exemplaires_disponibles'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $livre_item['statut']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AJOUTER LIVRE -->
    <div class="modal fade" id="ajouterLivreModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un nouveau livre</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Titre *</label>
                                <input type="text" class="form-control" name="titre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Auteur *</label>
                                <input type="text" class="form-control" name="auteur" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ISBN</label>
                                <input type="text" class="form-control" name="isbn">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Catégorie *</label>
                                <select class="form-select" name="categorie_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre d'exemplaires *</label>
                            <input type="number" class="form-control" name="nombre_exemplaires" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="ajouter_livre" class="btn btn-primary">Ajouter le livre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
