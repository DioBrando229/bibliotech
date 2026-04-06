<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Utilisateur.php';

protegerPageBibliothecaire();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

// Traitement activation/désactivation
if (isset($_POST['changer_statut'])) {
    $membre_id = (int)$_POST['membre_id'];
    $nouveau_statut = $_POST['statut'];
    
    $utilisateur = new Utilisateur($db);
    if ($utilisateur->changerStatut($membre_id, $nouveau_statut)) {
        setMessage('success', 'Statut modifié avec succès !');
    } else {
        setMessage('error', 'Erreur lors de la modification.');
    }
    rediriger('bibliothecaire/membres.php');
}

$utilisateur = new Utilisateur($db);
$membres_actifs = $utilisateur->getTousMembres('actif');
$membres_attente = $utilisateur->getTousMembres('en_attente');
$membres_inactifs = $utilisateur->getTousMembres('inactif');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion membres - <?php echo SITE_NAME; ?></title>
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
                        <a class="nav-link" href="reservations.php">🔖 Gestion réservations</a>
                        <a class="nav-link active" href="membres.php">👥 Gestion membres</a>
                        <a class="nav-link" href="../catalogue.php">📖 Voir catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <h2 class="mb-4">Gestion des membres</h2>

                <?php echo afficherMessage(); ?>

                <!-- ONGLETS -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#actifs">
                            Actifs (<?php echo count($membres_actifs); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#attente">
                            En attente (<?php echo count($membres_attente); ?>)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#inactifs">
                            Inactifs (<?php echo count($membres_inactifs); ?>)
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- MEMBRES ACTIFS -->
                    <div class="tab-pane fade show active" id="actifs">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nom complet</th>
                                                <th>Email</th>
                                                <th>Téléphone</th>
                                                <th>Date inscription</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($membres_actifs as $membre): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($membre['prenoms'] . ' ' . $membre['nom']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($membre['email']); ?></td>
                                                <td><?php echo htmlspecialchars($membre['telephone']); ?></td>
                                                <td><?php echo formaterDate($membre['date_inscription']); ?></td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="membre_id" value="<?php echo $membre['id']; ?>">
                                                        <input type="hidden" name="statut" value="inactif">
                                                        <button type="submit" name="changer_statut" class="btn btn-sm btn-warning" 
                                                                onclick="return confirm('Désactiver ce membre ?')">
                                                            Désactiver
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MEMBRES EN ATTENTE -->
                    <div class="tab-pane fade" id="attente">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($membres_attente)): ?>
                                <p class="text-muted">Aucun membre en attente de validation.</p>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nom complet</th>
                                                <th>Email</th>
                                                <th>Téléphone</th>
                                                <th>Date inscription</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($membres_attente as $membre): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($membre['prenoms'] . ' ' . $membre['nom']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($membre['email']); ?></td>
                                                <td><?php echo htmlspecialchars($membre['telephone']); ?></td>
                                                <td><?php echo formaterDate($membre['date_inscription']); ?></td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="membre_id" value="<?php echo $membre['id']; ?>">
                                                        <input type="hidden" name="statut" value="actif">
                                                        <button type="submit" name="changer_statut" class="btn btn-sm btn-success">
                                                            ✓ Activer
                                                        </button>
                                                    </form>
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

                    <!-- MEMBRES INACTIFS -->
                    <div class="tab-pane fade" id="inactifs">
                        <div class="card">
                            <div class="card-body">
                                <?php if (empty($membres_inactifs)): ?>
                                <p class="text-muted">Aucun membre inactif.</p>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nom complet</th>
                                                <th>Email</th>
                                                <th>Téléphone</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($membres_inactifs as $membre): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($membre['prenoms'] . ' ' . $membre['nom']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($membre['email']); ?></td>
                                                <td><?php echo htmlspecialchars($membre['telephone']); ?></td>
                                                <td>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="membre_id" value="<?php echo $membre['id']; ?>">
                                                        <input type="hidden" name="statut" value="actif">
                                                        <button type="submit" name="changer_statut" class="btn btn-sm btn-success">
                                                            Réactiver
                                                        </button>
                                                    </form>
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
