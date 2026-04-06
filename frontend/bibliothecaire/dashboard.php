<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Emprunt.php';

protegerPageBibliothecaire();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

$emprunt = new Emprunt($db);
$emprunt->updateEmpruntsRetard();

// Statistiques
$query_stats = "SELECT 
    (SELECT COUNT(*) FROM emprunts WHERE statut IN ('en_cours', 'en_retard')) as emprunts_en_cours,
    (SELECT COUNT(*) FROM reservations WHERE statut IN ('en_attente', 'disponible')) as reservations_attente,
    (SELECT COUNT(*) FROM emprunts WHERE statut IN ('en_cours', 'en_retard') AND date_retour_prevue < CURDATE()) as emprunts_retard,
    (SELECT COUNT(*) FROM emprunts WHERE DATE(date_emprunt) = CURDATE()) as emprunts_jour,
    (SELECT COUNT(*) FROM emprunts WHERE DATE(date_retour_effective) = CURDATE()) as retours_jour";
$stmt = $db->prepare($query_stats);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

$emprunts_retard = $emprunt->getEnRetard();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothécaire - <?php echo SITE_NAME; ?></title>
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
                    <h5 class="mb-3">BIBLIOTHÉCAIRE</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">📊 Tableau de bord</a>
                        <a class="nav-link" href="emprunts.php">📚 Gestion emprunts</a>
                        <a class="nav-link" href="reservations.php">🔖 Gestion réservations</a>
                        <a class="nav-link" href="membres.php">👥 Gestion membres</a>
                        <a class="nav-link" href="../catalogue.php">📖 Voir catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="mb-4">Tableau de bord bibliothécaire</h2>
                <?php echo afficherMessage(); ?>

                <!-- STATS CLIQUABLES : 3 cartes pertinentes -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <a href="emprunts.php" class="stat-card-link">
                            <div class="stat-card text-center">
                                <div style="font-size: 2rem;">📚</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['emprunts_en_cours']; ?></div>
                                <div>Emprunts en cours</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="reservations.php" class="stat-card-link">
                            <div class="stat-card warning text-center">
                                <div style="font-size: 2rem;">🔖</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['reservations_attente']; ?></div>
                                <div>Réservations</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="emprunts.php?tab=retard" class="stat-card-link">
                            <div class="stat-card danger text-center">
                                <div style="font-size: 2rem;">⏰</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['emprunts_retard']; ?></div>
                                <div>En retard</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- ACTIVITÉ DU JOUR + ACTIONS RAPIDES -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">📅 Activité du jour</h6>
                            </div>
                            <div class="card-body d-flex gap-3 align-items-center">
                                <div class="text-center flex-fill">
                                    <div style="font-size:1.8rem;font-weight:bold;color:#3498DB"><?php echo $stats['emprunts_jour']; ?></div>
                                    <small class="text-muted">Emprunts</small>
                                </div>
                                <div class="vr"></div>
                                <div class="text-center flex-fill">
                                    <div style="font-size:1.8rem;font-weight:bold;color:#27AE60"><?php echo $stats['retours_jour']; ?></div>
                                    <small class="text-muted">Retours</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">⚡ Actions rapides</h6>
                            </div>
                            <div class="card-body d-flex gap-2 align-items-center">
                                <a href="emprunts.php?action=nouveau" class="btn btn-primary flex-fill">📚 Nouvel emprunt</a>
                                <a href="emprunts.php?action=retour" class="btn btn-success flex-fill">↩️ Retour</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EMPRUNTS EN RETARD -->
                <?php if (!empty($emprunts_retard)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">⚠️ Emprunts en retard</h5>
                        <a href="emprunts.php?tab=retard" class="text-white text-decoration-none small">Voir tout →</a>
                    </div>
                    <div class="card-body">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Membre</th>
                                    <th>Livre</th>
                                    <th>Retard</th>
                                    <th>Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($emprunts_retard, 0, 5) as $emp): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($emp['membre']); ?></td>
                                    <td><?php echo htmlspecialchars($emp['titre']); ?></td>
                                    <td><span class="badge bg-danger"><?php echo $emp['jours_retard']; ?> jours</span></td>
                                    <td><?php echo htmlspecialchars($emp['telephone']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>


            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
