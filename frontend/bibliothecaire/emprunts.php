<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Emprunt.php';
require_once '../../backend/models/Livre.php';
require_once '../../backend/models/Utilisateur.php';

protegerPageBibliothecaire();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

// Traitement nouveau emprunt
if (isset($_POST['creer_emprunt'])) {
    $utilisateur_id = (int)$_POST['utilisateur_id'];
    $livre_id = (int)$_POST['livre_id'];
    
    $emprunt = new Emprunt($db);
    $emprunt->utilisateur_id = $utilisateur_id;
    $emprunt->livre_id = $livre_id;
    $emprunt->enregistre_par = $user['id'];
    
    $result = $emprunt->creer();
    
    if ($result === true) {
        setMessage('success', 'Emprunt créé avec succès !');
    } else {
        setMessage('error', $result);
    }
    rediriger('bibliothecaire/emprunts.php');
}

// Traitement retour
if (isset($_POST['enregistrer_retour'])) {
    $emprunt_id = (int)$_POST['emprunt_id'];
    
    $emprunt = new Emprunt($db);
    if ($emprunt->enregistrerRetour($emprunt_id)) {
        setMessage('success', 'Retour enregistré avec succès !');
    } else {
        setMessage('error', 'Erreur lors de l\'enregistrement du retour.');
    }
    rediriger('bibliothecaire/emprunts.php');
}

$emprunt = new Emprunt($db);
$emprunt->updateEmpruntsRetard();
$emprunts_actifs = $emprunt->getTousActifs();
$emprunts_retard = $emprunt->getEnRetard();

// Pour le formulaire
$livre = new Livre($db);
$livres = $livre->getTous();

$utilisateur_model = new Utilisateur($db);
$membres = $utilisateur_model->getTousMembres('actif');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion emprunts - <?php echo SITE_NAME; ?></title>
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
                        <a class="nav-link active" href="emprunts.php">📚 Gestion emprunts</a>
                        <a class="nav-link" href="reservations.php">🔖 Gestion réservations</a>
                        <a class="nav-link" href="membres.php">👥 Gestion membres</a>
                        <a class="nav-link" href="../catalogue.php">📖 Voir catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="mb-4">Gestion des emprunts</h2>

                <?php echo afficherMessage(); ?>

                <!-- BOUTONS ACTIONS -->
                <div class="mb-4">
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#nouveauEmpruntModal">
                        ➕ Nouvel emprunt
                    </button>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#retourModal">
                        ↩️ Enregistrer retour
                    </button>
                </div>

                <!-- ONGLETS -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#encours">
                            En cours (<?php echo count($emprunts_actifs); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#retard">
                            En retard (<?php echo count($emprunts_retard); ?>)
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- EMPRUNTS EN COURS -->
                    <div class="tab-pane fade show active" id="encours">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Membre</th>
                                                <th>Livre</th>
                                                <th>Date emprunt</th>
                                                <th>Retour prévu</th>
                                                <th>Jours restants</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($emprunts_actifs as $emp): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($emp['membre']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($emp['telephone']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($emp['titre']); ?></td>
                                                <td><?php echo formaterDate($emp['date_emprunt']); ?></td>
                                                <td><?php echo formaterDate($emp['date_retour_prevue']); ?></td>
                                                <td>
                                                    <?php
                                                    $jours = $emp['jours_restants'];
                                                    $badge_class = $jours > 5 ? 'success' : ($jours > 0 ? 'warning' : 'danger');
                                                    ?>
                                                    <span class="badge bg-<?php echo $badge_class; ?>">
                                                        <?php echo $jours > 0 ? $jours . ' jours' : 'RETARD'; ?>
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

                    <!-- EMPRUNTS EN RETARD -->
                    <div class="tab-pane fade" id="retard">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($emprunts_retard)): ?>
                                <p class="text-muted">Aucun emprunt en retard.</p>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Membre</th>
                                                <th>Livre</th>
                                                <th>Retour prévu</th>
                                                <th>Jours de retard</th>
                                                <th>Contact</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($emprunts_retard as $emp): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($emp['membre']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($emp['titre']); ?></td>
                                                <td><?php echo formaterDate($emp['date_retour_prevue']); ?></td>
                                                <td><span class="badge bg-danger"><?php echo $emp['jours_retard']; ?> jours</span></td>
                                                <td><?php echo htmlspecialchars($emp['telephone']); ?></td>
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

    <!-- MODAL NOUVEL EMPRUNT -->
    <div class="modal fade" id="nouveauEmpruntModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Nouvel emprunt</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Membre *</label>
                            <select class="form-select" name="utilisateur_id" required>
                                <option value="">Sélectionner un membre</option>
                                <?php foreach ($membres as $membre): ?>
                                <option value="<?php echo $membre['id']; ?>">
                                    <?php echo htmlspecialchars($membre['prenoms'] . ' ' . $membre['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Livre *</label>
                            <select class="form-select" name="livre_id" required>
                                <option value="">Sélectionner un livre</option>
                                <?php foreach ($livres as $livre_item): ?>
                                <?php if ($livre_item['exemplaires_disponibles'] > 0): ?>
                                <option value="<?php echo $livre_item['id']; ?>">
                                    <?php echo htmlspecialchars($livre_item['titre']); ?> 
                                    (<?php echo $livre_item['exemplaires_disponibles']; ?> dispo)
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <p class="text-muted small">Durée d'emprunt : 14 jours</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="creer_emprunt" class="btn btn-primary">Créer l'emprunt</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL RETOUR -->
    <div class="modal fade" id="retourModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="">
                    <div class="modal-header">
                        <h5 class="modal-title">Enregistrer un retour</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Emprunt en cours *</label>
                            <select class="form-select" name="emprunt_id" required>
                                <option value="">Sélectionner un emprunt</option>
                                <?php foreach ($emprunts_actifs as $emp): ?>
                                <option value="<?php echo $emp['id']; ?>">
                                    <?php echo htmlspecialchars($emp['membre'] . ' - ' . $emp['titre']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="enregistrer_retour" class="btn btn-success">Enregistrer retour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        if (tab === 'retard') {
            const el = document.querySelector('a[href="#retard"]');
            if (el) new bootstrap.Tab(el).show();
        }
    </script>
</body>
</html>
