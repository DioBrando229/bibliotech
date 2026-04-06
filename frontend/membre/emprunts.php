<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Emprunt.php';

protegerPage();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

$emprunt = new Emprunt($db);
$emprunt->updateEmpruntsRetard();
$emprunts_en_cours = $emprunt->getParUtilisateur($user['id'], 'en_cours');
$emprunts_en_retard = $emprunt->getParUtilisateur($user['id'], 'en_retard');
$emprunts_actifs = array_merge($emprunts_en_cours, $emprunts_en_retard);
$emprunts_historique = $emprunt->getParUtilisateur($user['id'], 'retourne');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes emprunts - <?php echo SITE_NAME; ?></title>
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
                        <a class="nav-link active" href="emprunts.php">📚 Mes emprunts</a>
                        <a class="nav-link" href="reservations.php">🔖 Mes réservations</a>
                        <a class="nav-link" href="../catalogue.php">📖 Catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="mb-4">Mes emprunts</h2>

                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#encours">En cours (<?php echo count($emprunts_actifs); ?>)</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#historique">Historique (<?php echo count($emprunts_historique); ?>)</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="encours">
                        <?php if (empty($emprunts_actifs)): ?>
                        <div class="alert alert-info">Aucun emprunt en cours.</div>
                        <?php else: ?>
                        <?php foreach ($emprunts_actifs as $emp): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($emp['titre']); ?></h5>
                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($emp['auteur']); ?></p>
                                        <small>
                                            📅 Emprunté le <?php echo formaterDate($emp['date_emprunt']); ?> •
                                            ⏰ Retour prévu: <?php echo formaterDate($emp['date_retour_prevue']); ?>
                                        </small>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php
                                        $jours = $emp['jours_restants'];
                                        $badge_class = $jours > 5 ? 'success' : ($jours > 0 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?php echo $badge_class; ?> fs-6">
                                            <?php echo $jours > 0 ? $jours . ' jours restants' : 'EN RETARD'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane fade" id="historique">
                        <?php if (empty($emprunts_historique)): ?>
                        <div class="alert alert-info">Aucun historique d'emprunts.</div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Livre</th>
                                        <th>Date emprunt</th>
                                        <th>Date retour</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($emprunts_historique as $emp): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($emp['titre']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($emp['auteur']); ?></small>
                                        </td>
                                        <td><?php echo formaterDate($emp['date_emprunt']); ?></td>
                                        <td><?php echo formaterDate($emp['date_retour_effective']); ?></td>
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

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab === 'historique') {
            const el = document.querySelector('a[href="#historique"]');
            if (el) new bootstrap.Tab(el).show();
        }
    </script>
</body>
</html>
