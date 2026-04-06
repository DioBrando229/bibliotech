<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';

protegerPage();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

// Annuler une réservation
if (isset($_POST['annuler_reservation'])) {
    $reservation_id = (int)$_POST['reservation_id'];

    $query_annuler = "UPDATE reservations 
                      SET statut = 'annulee' 
                      WHERE id = :id AND utilisateur_id = :user_id AND statut = 'en_attente'";
    $stmt = $db->prepare($query_annuler);
    $stmt->bindParam(':id', $reservation_id);
    $stmt->bindParam(':user_id', $user['id']);
    if ($stmt->execute() && $stmt->rowCount() > 0) {
        setMessage('success', 'Réservation annulée avec succès.');
    } else {
        setMessage('error', "Impossible d'annuler cette réservation.");
    }
    rediriger('membre/reservations.php');
}

// Réservations actives (en_attente ou disponible)
$query_actives = "SELECT r.id, r.date_reservation, r.statut, r.date_expiration,
                         l.titre, l.auteur, l.image_couverture
                  FROM reservations r
                  JOIN livres l ON r.livre_id = l.id
                  WHERE r.utilisateur_id = :user_id AND r.statut IN ('en_attente', 'disponible')
                  ORDER BY r.date_reservation DESC";
$stmt = $db->prepare($query_actives);
$stmt->bindParam(':user_id', $user['id']);
$stmt->execute();
$reservations_actives = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Historique (annulee, traitee)
$query_historique = "SELECT r.id, r.date_reservation, r.statut,
                            l.titre, l.auteur
                     FROM reservations r
                     JOIN livres l ON r.livre_id = l.id
                     WHERE r.utilisateur_id = :user_id AND r.statut IN ('annulee', 'traitee')
                     ORDER BY r.date_reservation DESC";
$stmt = $db->prepare($query_historique);
$stmt->bindParam(':user_id', $user['id']);
$stmt->execute();
$reservations_historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réservations - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <?php include '../includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                    <h5 class="mb-3">MENU</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">🏠 Tableau de bord</a>
                        <a class="nav-link" href="profil.php">👤 Mon profil</a>
                        <a class="nav-link" href="emprunts.php">📚 Mes emprunts</a>
                        <a class="nav-link active" href="reservations.php">🔖 Mes réservations</a>
                        <a class="nav-link" href="../catalogue.php">📖 Catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="mb-4">Mes réservations</h2>

                <?php echo afficherMessage(); ?>

                <!-- RÉSERVATIONS ACTIVES -->
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">🔖 Réservations en cours (<?php echo count($reservations_actives); ?>)</h5>
                        <a href="../catalogue.php" class="btn btn-sm btn-primary">+ Réserver un livre</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($reservations_actives)): ?>
                        <div class="text-center py-4">
                            <span style="font-size: 3rem;">📚</span>
                            <p class="text-muted mt-2 mb-0">Aucune réservation en cours.</p>
                            <a href="../catalogue.php" class="btn btn-primary mt-3">Parcourir le catalogue</a>
                        </div>
                        <?php else: ?>
                        <?php foreach ($reservations_actives as $res): ?>
                        <div class="book-item mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex">
                                    <div class="me-3 text-center" style="width:50px;">
                                        <span style="font-size: 2rem;">📘</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1"><?php echo htmlspecialchars($res['titre']); ?></h6>
                                        <p class="text-muted small mb-1"><?php echo htmlspecialchars($res['auteur']); ?></p>
                                        <small class="text-muted">Réservé le <?php echo formaterDate($res['date_reservation']); ?></small>
                                        <?php if ($res['statut'] === 'disponible' && $res['date_expiration']): ?>
                                        <br><small class="text-warning fw-bold">⚠️ Disponible jusqu'au <?php echo formaterDate($res['date_expiration']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <?php if ($res['statut'] === 'en_attente'): ?>
                                        <span class="badge bg-warning text-dark">En attente</span>
                                        <form method="POST" onsubmit="return confirm('Annuler cette réservation ?')">
                                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                            <button type="submit" name="annuler_reservation" class="btn btn-sm btn-outline-danger">Annuler</button>
                                        </form>
                                    <?php elseif ($res['statut'] === 'disponible'): ?>
                                        <span class="badge bg-success">Disponible !</span>
                                        <small class="text-muted text-end" style="max-width:150px;">Présentez-vous à la bibliothèque</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- HISTORIQUE -->
                <?php if (!empty($reservations_historique)): ?>
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">📋 Historique des réservations</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Livre</th>
                                        <th>Auteur</th>
                                        <th>Date réservation</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations_historique as $res): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($res['titre']); ?></td>
                                        <td><?php echo htmlspecialchars($res['auteur']); ?></td>
                                        <td><?php echo formaterDate($res['date_reservation']); ?></td>
                                        <td>
                                            <?php if ($res['statut'] === 'annulee'): ?>
                                                <span class="badge bg-secondary">Annulée</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Traitée</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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
