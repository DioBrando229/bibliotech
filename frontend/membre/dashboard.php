<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Emprunt.php';
require_once '../../backend/models/Livre.php';

protegerPage();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

$emprunt = new Emprunt($db);
$emprunt->updateEmpruntsRetard();

// Emprunts actifs (en_cours + en_retard)
$emprunts_en_cours = $emprunt->getParUtilisateur($user['id'], 'en_cours');
$emprunts_en_retard = $emprunt->getParUtilisateur($user['id'], 'en_retard');
$emprunts_actifs = array_merge($emprunts_en_cours, $emprunts_en_retard);

// Réservations actives
$query_reservations = "SELECT r.*, l.titre, l.auteur 
                       FROM reservations r
                       JOIN livres l ON r.livre_id = l.id
                       WHERE r.utilisateur_id = :user_id AND r.statut IN ('en_attente', 'disponible')
                       ORDER BY r.date_reservation DESC";
$stmt = $db->prepare($query_reservations);
$stmt->bindParam(':user_id', $user['id']);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Livres lus ce mois (retournés ce mois-ci)
$query_lus = "SELECT COUNT(*) as total FROM emprunts 
              WHERE utilisateur_id = :user_id 
              AND statut = 'retourne' 
              AND MONTH(date_retour_effective) = MONTH(CURDATE()) 
              AND YEAR(date_retour_effective) = YEAR(CURDATE())";
$stmt = $db->prepare($query_lus);
$stmt->bindParam(':user_id', $user['id']);
$stmt->execute();
$livres_lus_mois = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stats = [
    'emprunts_actifs' => count($emprunts_actifs),
    'reservations'    => count($reservations),
    'livres_lus_mois' => $livres_lus_mois,
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace - <?php echo SITE_NAME; ?></title>
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
                    <h5 class="mb-3">MENU</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="dashboard.php">🏠 Tableau de bord</a>
                        <a class="nav-link" href="profil.php">👤 Mon profil</a>
                        <a class="nav-link" href="emprunts.php">📚 Mes emprunts</a>
                        <a class="nav-link" href="reservations.php">🔖 Mes réservations</a>
                        <a class="nav-link" href="../catalogue.php">📖 Catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2>Bonjour <?php echo htmlspecialchars($user['prenoms']); ?> ! 👋</h2>
                        <p class="text-muted mb-0">Bienvenue sur votre espace personnel BiblioTech</p>
                    </div>
                </div>

                <?php echo afficherMessage(); ?>

                <!-- STATS CLIQUABLES -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <a href="emprunts.php" class="stat-card-link">
                            <div class="stat-card text-center">
                                <div style="font-size: 2.5rem;">📚</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['emprunts_actifs']; ?></div>
                                <div>Emprunts en cours</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="reservations.php" class="stat-card-link">
                            <div class="stat-card success text-center">
                                <div style="font-size: 2.5rem;">🔖</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['reservations']; ?></div>
                                <div>Réservations actives</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="emprunts.php?tab=historique" class="stat-card-link">
                            <div class="stat-card warning text-center">
                                <div style="font-size: 2.5rem;">📖</div>
                                <div style="font-size: 2.5rem; font-weight: bold;"><?php echo $stats['livres_lus_mois']; ?></div>
                                <div>Livres lus ce mois</div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- EMPRUNTS EN COURS -->
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Mes emprunts en cours</h5>
                            <a href="emprunts.php" class="text-decoration-none">Voir tout →</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($emprunts_actifs)): ?>
                        <p class="text-muted mb-0">Aucun emprunt en cours.</p>
                        <?php else: ?>
                        <?php foreach (array_slice($emprunts_actifs, 0, 3) as $emp): ?>
                        <div class="book-item mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-white rounded text-center" style="width:60px;height:80px;display:flex;align-items:center;justify-content:center;">
                                        <span style="font-size:2rem;">📘</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($emp['titre']); ?></h6>
                                    <p class="text-muted small mb-2"><?php echo htmlspecialchars($emp['auteur']); ?></p>
                                    <div class="small">
                                        <span class="me-3">📅 Emprunté le <?php echo formaterDate($emp['date_emprunt']); ?></span>
                                        <span>⏰ Retour : <?php echo formaterDate($emp['date_retour_prevue']); ?></span>
                                    </div>
                                </div>
                                <div>
                                    <?php
                                    $jours = $emp['jours_restants'];
                                    $badge_class = $jours > 5 ? 'success' : ($jours > 0 ? 'warning' : 'danger');
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>">
                                        <?php echo $jours > 0 ? $jours . ' jours restants' : 'En retard'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($emprunts_actifs) > 3): ?>
                        <div class="text-center mt-2">
                            <a href="emprunts.php" class="btn btn-sm btn-outline-primary">Voir les <?php echo count($emprunts_actifs); ?> emprunts →</a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- RÉSERVATIONS -->
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Mes réservations</h5>
                            <a href="reservations.php" class="text-decoration-none">Voir tout →</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($reservations)): ?>
                        <p class="text-muted mb-0">Aucune réservation en cours.</p>
                        <?php else: ?>
                        <?php foreach (array_slice($reservations, 0, 3) as $res): ?>
                        <div class="book-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($res['titre']); ?></h6>
                                    <p class="text-muted small mb-0"><?php echo htmlspecialchars($res['auteur']); ?></p>
                                </div>
                                <span class="badge bg-<?php echo $res['statut'] === 'disponible' ? 'success' : 'info'; ?>">
                                    <?php echo $res['statut'] === 'disponible' ? 'Disponible !' : 'En attente'; ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($reservations) > 3): ?>
                        <div class="text-center mt-2">
                            <a href="reservations.php" class="btn btn-sm btn-outline-primary">Voir les <?php echo count($reservations); ?> réservations →</a>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
