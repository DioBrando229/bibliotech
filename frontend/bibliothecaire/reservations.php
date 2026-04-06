<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';

protegerPageBibliothecaire();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

// Marquer une réservation comme disponible
if (isset($_POST['marquer_disponible'])) {
    $reservation_id = (int)$_POST['reservation_id'];
    $date_expiration = date('Y-m-d', strtotime('+7 days'));

    $query = "UPDATE reservations 
              SET statut = 'disponible', date_expiration = :date_exp 
              WHERE id = :id AND statut = 'en_attente'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':date_exp', $date_expiration);
    $stmt->bindParam(':id', $reservation_id);
    if ($stmt->execute() && $stmt->rowCount() > 0) {
        setMessage('success', 'Réservation marquée comme disponible. Le membre a 7 jours pour venir récupérer le livre.');
    } else {
        setMessage('error', "Impossible de mettre à jour cette réservation.");
    }
    rediriger('bibliothecaire/reservations.php');
}

// Marquer comme traitée (livre remis au membre)
if (isset($_POST['marquer_traitee'])) {
    $reservation_id = (int)$_POST['reservation_id'];

    $query = "UPDATE reservations SET statut = 'traitee' WHERE id = :id AND statut IN ('en_attente', 'disponible')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $reservation_id);
    if ($stmt->execute() && $stmt->rowCount() > 0) {
        setMessage('success', 'Réservation marquée comme traitée.');
    } else {
        setMessage('error', "Impossible de mettre à jour cette réservation.");
    }
    rediriger('bibliothecaire/reservations.php');
}

// Annuler une réservation
if (isset($_POST['annuler_reservation'])) {
    $reservation_id = (int)$_POST['reservation_id'];

    $query = "UPDATE reservations SET statut = 'annulee' WHERE id = :id AND statut IN ('en_attente', 'disponible')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $reservation_id);
    if ($stmt->execute() && $stmt->rowCount() > 0) {
        setMessage('success', 'Réservation annulée.');
    } else {
        setMessage('error', "Impossible d'annuler cette réservation.");
    }
    rediriger('bibliothecaire/reservations.php');
}

// Réservations en attente
$query_attente = "SELECT r.id, r.date_reservation, r.statut, r.date_expiration,
                         l.titre, l.auteur, l.exemplaires_disponibles,
                         CONCAT(u.prenoms, ' ', u.nom) as membre, u.email, u.telephone
                  FROM reservations r
                  JOIN livres l ON r.livre_id = l.id
                  JOIN utilisateurs u ON r.utilisateur_id = u.id
                  WHERE r.statut = 'en_attente'
                  ORDER BY r.date_reservation ASC";
$stmt = $db->prepare($query_attente);
$stmt->execute();
$reservations_attente = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Réservations disponibles (livre prêt, en attente du membre)
$query_disponibles = "SELECT r.id, r.date_reservation, r.statut, r.date_expiration,
                             l.titre, l.auteur,
                             CONCAT(u.prenoms, ' ', u.nom) as membre, u.email, u.telephone
                      FROM reservations r
                      JOIN livres l ON r.livre_id = l.id
                      JOIN utilisateurs u ON r.utilisateur_id = u.id
                      WHERE r.statut = 'disponible'
                      ORDER BY r.date_expiration ASC";
$stmt = $db->prepare($query_disponibles);
$stmt->execute();
$reservations_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Historique récent
$query_historique = "SELECT r.id, r.date_reservation, r.statut,
                            l.titre,
                            CONCAT(u.prenoms, ' ', u.nom) as membre
                     FROM reservations r
                     JOIN livres l ON r.livre_id = l.id
                     JOIN utilisateurs u ON r.utilisateur_id = u.id
                     WHERE r.statut IN ('annulee', 'traitee')
                     ORDER BY r.updated_at DESC
                     LIMIT 20";
$stmt = $db->prepare($query_historique);
$stmt->execute();
$reservations_historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion réservations - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <?php include '../includes/header.php'; ?>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-3">
                <div class="sidebar">
                    <h5 class="mb-3">BIBLIOTHÉCAIRE</h5>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">📊 Tableau de bord</a>
                        <a class="nav-link" href="emprunts.php">📚 Gestion emprunts</a>
                        <a class="nav-link active" href="reservations.php">🔖 Gestion réservations</a>
                        <a class="nav-link" href="membres.php">👥 Gestion membres</a>
                        <a class="nav-link" href="../catalogue.php">📖 Voir catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="mb-4">Gestion des réservations</h2>

                <?php echo afficherMessage(); ?>

                <!-- ONGLETS -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#attente">
                            En attente
                            <?php if (count($reservations_attente) > 0): ?>
                            <span class="badge bg-warning text-dark ms-1"><?php echo count($reservations_attente); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#disponibles">
                            Disponibles
                            <?php if (count($reservations_disponibles) > 0): ?>
                            <span class="badge bg-success ms-1"><?php echo count($reservations_disponibles); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#historique">
                            Historique
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- EN ATTENTE -->
                    <div class="tab-pane fade show active" id="attente">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($reservations_attente)): ?>
                                <p class="text-muted text-center py-3">Aucune réservation en attente.</p>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Membre</th>
                                                <th>Livre</th>
                                                <th>Date réservation</th>
                                                <th>Dispo. en stock</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservations_attente as $res): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($res['membre']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($res['telephone']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($res['titre']); ?><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($res['auteur']); ?></small>
                                                </td>
                                                <td><?php echo formaterDate($res['date_reservation']); ?></td>
                                                <td>
                                                    <?php if ($res['exemplaires_disponibles'] > 0): ?>
                                                        <span class="badge bg-success"><?php echo $res['exemplaires_disponibles']; ?> dispo.</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Indisponible</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 flex-wrap">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                                            <button type="submit" name="marquer_disponible" class="btn btn-sm btn-success"
                                                                <?php echo $res['exemplaires_disponibles'] == 0 ? 'disabled title="Aucun exemplaire disponible"' : ''; ?>>
                                                                ✅ Disponible
                                                            </button>
                                                        </form>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Annuler cette réservation ?')">
                                                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                                            <button type="submit" name="annuler_reservation" class="btn btn-sm btn-outline-danger">Annuler</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- DISPONIBLES -->
                    <div class="tab-pane fade" id="disponibles">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($reservations_disponibles)): ?>
                                <p class="text-muted text-center py-3">Aucune réservation disponible en attente de remise.</p>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Membre</th>
                                                <th>Livre</th>
                                                <th>Date réservation</th>
                                                <th>Expire le</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservations_disponibles as $res): ?>
                                            <?php
                                            $expire_dans = $res['date_expiration'] ? (int)((strtotime($res['date_expiration']) - time()) / 86400) : null;
                                            $expire_class = ($expire_dans !== null && $expire_dans <= 1) ? 'text-danger fw-bold' : 'text-muted';
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($res['membre']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($res['telephone']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($res['titre']); ?></td>
                                                <td><?php echo formaterDate($res['date_reservation']); ?></td>
                                                <td>
                                                    <?php if ($res['date_expiration']): ?>
                                                        <span class="<?php echo $expire_class; ?>">
                                                            <?php echo formaterDate($res['date_expiration']); ?>
                                                            <?php if ($expire_dans !== null && $expire_dans >= 0): ?>
                                                                (<?php echo $expire_dans; ?>j)
                                                            <?php elseif ($expire_dans !== null): ?>
                                                                <span class="badge bg-danger">Expiré</span>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php else: ?>
                                                        —
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Confirmer la remise du livre au membre ?')">
                                                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                                            <button type="submit" name="marquer_traitee" class="btn btn-sm btn-primary">📚 Remis</button>
                                                        </form>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Annuler cette réservation ?')">
                                                            <input type="hidden" name="reservation_id" value="<?php echo $res['id']; ?>">
                                                            <button type="submit" name="annuler_reservation" class="btn btn-sm btn-outline-danger">Annuler</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- HISTORIQUE -->
                    <div class="tab-pane fade" id="historique">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($reservations_historique)): ?>
                                <p class="text-muted text-center py-3">Aucun historique disponible.</p>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Membre</th>
                                                <th>Livre</th>
                                                <th>Date réservation</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($reservations_historique as $res): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($res['membre']); ?></td>
                                                <td><?php echo htmlspecialchars($res['titre']); ?></td>
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
                                <?php endif; ?>
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
