<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Emprunt.php';

protegerPageAdmin();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

$emprunt_model = new Emprunt($db);
$emprunt_model->updateEmpruntsRetard();

// Statistiques
$query_stats = "SELECT * FROM vue_statistiques";
$stmt = $db->prepare($query_stats);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Livres populaires
$query_pop = "CALL livres_populaires(5)";
$stmt_pop = $db->prepare($query_pop);
$stmt_pop->execute();
$livres_populaires = $stmt_pop->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stat-card-link { text-decoration: none; display: block; }
        .stat-card-link .stat-card { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
        .stat-card-link:hover .stat-card { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.2) !important; }
    </style>
</head>
<body class="bg-light">
    <?php include '../includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                    <h5 class="mb-3">ADMIN</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">📊 Tableau de bord</a>
                        <a class="nav-link" href="catalogue.php">📚 Gestion catalogue</a>
                        <a class="nav-link" href="utilisateurs.php">👥 Gestion utilisateurs</a>
                        <a class="nav-link" href="../catalogue.php">📖 Voir catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="mb-4">Tableau de bord administrateur</h2>
                <?php echo afficherMessage(); ?>

                <!-- STATS CLIQUABLES -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <a href="catalogue.php" class="stat-card-link">
                            <div class="stat-card text-center">
                                <div style="font-size: 2rem;">📚</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['total_livres']; ?></div>
                                <div>Livres au total</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="utilisateurs.php" class="stat-card-link">
                            <div class="stat-card success text-center">
                                <div style="font-size: 2rem;">👥</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['membres_actifs']; ?></div>
                                <div>Membres actifs</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../bibliothecaire/emprunts.php" class="stat-card-link">
                            <div class="stat-card warning text-center">
                                <div style="font-size: 2rem;">📖</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['emprunts_actifs']; ?></div>
                                <div>Emprunts actifs</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../bibliothecaire/emprunts.php?tab=retard" class="stat-card-link">
                            <div class="stat-card danger text-center">
                                <div style="font-size: 2rem;">⏰</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['emprunts_retard']; ?></div>
                                <div>En retard</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- LIVRES POPULAIRES -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">📈 Livres les plus empruntés</h5>
                        <a href="catalogue.php" class="text-decoration-none small">Voir catalogue →</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Auteur</th>
                                        <th>Catégorie</th>
                                        <th>Emprunts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($livres_populaires as $livre): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($livre['titre']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($livre['auteur']); ?></td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo !empty($livre['categorie']) ? htmlspecialchars($livre['categorie']) : 'N/A'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo $livre['nombre_emprunts']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ACTIONS RAPIDES -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>📚 Ajouter un livre</h5>
                                <p class="text-muted">Ajouter un nouveau livre au catalogue</p>
                                <a href="catalogue.php" class="btn btn-primary">Aller au catalogue</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5>👤 Créer un bibliothécaire</h5>
                                <p class="text-muted">Ajouter un nouveau compte bibliothécaire</p>
                                <a href="utilisateurs.php" class="btn btn-primary">Gérer utilisateurs</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
