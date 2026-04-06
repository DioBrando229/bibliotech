<?php
require_once '../../backend/init.php';
require_once '../../backend/config/database.php';
require_once '../../backend/models/Utilisateur.php';

protegerPageAdmin();
$user = getUtilisateurConnecte();

$database = new Database();
$db = $database->getConnection();

$utilisateur = new Utilisateur($db);

// ── Créer bibliothécaire ──────────────────────────────────────────────────────
if (isset($_POST['creer_bibliothecaire'])) {
    $utilisateur->nom       = nettoyer($_POST['nom']);
    $utilisateur->prenoms   = nettoyer($_POST['prenoms']);
    $utilisateur->email     = nettoyer($_POST['email']);
    $utilisateur->telephone = nettoyer($_POST['telephone']);
    $utilisateur->mot_de_passe = $_POST['mot_de_passe'];
    $utilisateur->role      = 'bibliothecaire';
    $utilisateur->statut    = 'actif';
    $utilisateur->adresse   = '';

    if ($utilisateur->emailExiste()) {
        setMessage('error', 'Cet email est déjà utilisé.');
    } elseif ($utilisateur->creer()) {
        setMessage('success', 'Bibliothécaire créé avec succès !');
    } else {
        setMessage('error', 'Erreur lors de la création.');
    }
    rediriger('admin/utilisateurs.php');
}

// ── Changer statut ────────────────────────────────────────────────────────────
if (isset($_POST['changer_statut'])) {
    $cible_id      = (int)$_POST['utilisateur_id'];
    $nouveau_statut = nettoyer($_POST['nouveau_statut']);

    if ($cible_id === (int)$user['id']) {
        setMessage('error', 'Vous ne pouvez pas modifier votre propre statut.');
    } elseif ($utilisateur->changerStatut($cible_id, $nouveau_statut)) {
        setMessage('success', 'Statut mis à jour.');
    } else {
        setMessage('error', 'Erreur lors de la mise à jour du statut.');
    }
    rediriger('admin/utilisateurs.php');
}

// ── Changer rôle ──────────────────────────────────────────────────────────────
if (isset($_POST['changer_role'])) {
    $cible_id    = (int)$_POST['utilisateur_id'];
    $nouveau_role = nettoyer($_POST['nouveau_role']);
    $roles_valides = ['membre', 'bibliothecaire', 'admin'];

    if ($cible_id === (int)$user['id']) {
        setMessage('error', 'Vous ne pouvez pas modifier votre propre rôle.');
    } elseif (!in_array($nouveau_role, $roles_valides)) {
        setMessage('error', 'Rôle invalide.');
    } else {
        $q = "UPDATE utilisateurs SET role = :role WHERE id = :id";
        $s = $db->prepare($q);
        $s->bindParam(':role', $nouveau_role);
        $s->bindParam(':id', $cible_id);
        if ($s->execute()) {
            setMessage('success', 'Rôle mis à jour.');
        } else {
            setMessage('error', 'Erreur lors de la mise à jour du rôle.');
        }
    }
    rediriger('admin/utilisateurs.php');
}

// ── Modifier informations ─────────────────────────────────────────────────────
if (isset($_POST['modifier_utilisateur'])) {
    $cible_id = (int)$_POST['utilisateur_id'];
    $q = "UPDATE utilisateurs SET nom=:nom, prenoms=:prenoms, telephone=:telephone
          WHERE id=:id";
    $s = $db->prepare($q);
    $nom      = nettoyer($_POST['nom']);
    $prenoms  = nettoyer($_POST['prenoms']);
    $telephone = nettoyer($_POST['telephone']);
    $s->bindParam(':nom', $nom);
    $s->bindParam(':prenoms', $prenoms);
    $s->bindParam(':telephone', $telephone);
    $s->bindParam(':id', $cible_id);
    if ($s->execute()) {
        setMessage('success', 'Informations mises à jour.');
    } else {
        setMessage('error', 'Erreur lors de la modification.');
    }
    rediriger('admin/utilisateurs.php');
}

// ── Réinitialiser mot de passe ────────────────────────────────────────────────
if (isset($_POST['reset_password'])) {
    $cible_id   = (int)$_POST['utilisateur_id'];
    $nouveau_mdp = $_POST['nouveau_mot_de_passe'];
    if (strlen($nouveau_mdp) < 6) {
        setMessage('error', 'Le mot de passe doit faire au moins 6 caractères.');
    } else {
        $hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
        $q = "UPDATE utilisateurs SET mot_de_passe=:mdp WHERE id=:id";
        $s = $db->prepare($q);
        $s->bindParam(':mdp', $hash);
        $s->bindParam(':id', $cible_id);
        if ($s->execute()) {
            setMessage('success', 'Mot de passe réinitialisé.');
        } else {
            setMessage('error', 'Erreur lors de la réinitialisation.');
        }
    }
    rediriger('admin/utilisateurs.php');
}

// ── Supprimer utilisateur ─────────────────────────────────────────────────────
if (isset($_POST['supprimer_utilisateur'])) {
    $cible_id = (int)$_POST['utilisateur_id'];
    if ($cible_id === (int)$user['id']) {
        setMessage('error', 'Vous ne pouvez pas supprimer votre propre compte.');
    } else {
        // Vérifier qu'il n'a pas d'emprunts actifs
        $q_check = "SELECT COUNT(*) as total FROM emprunts WHERE utilisateur_id=:id AND statut IN ('en_cours','en_retard')";
        $s_check = $db->prepare($q_check);
        $s_check->bindParam(':id', $cible_id);
        $s_check->execute();
        $nb = $s_check->fetch(PDO::FETCH_ASSOC)['total'];

        if ($nb > 0) {
            setMessage('error', 'Impossible de supprimer : cet utilisateur a des emprunts actifs.');
        } else {
            $q = "DELETE FROM utilisateurs WHERE id=:id";
            $s = $db->prepare($q);
            $s->bindParam(':id', $cible_id);
            if ($s->execute()) {
                setMessage('success', 'Utilisateur supprimé.');
            } else {
                setMessage('error', 'Erreur lors de la suppression.');
            }
        }
    }
    rediriger('admin/utilisateurs.php');
}

// ── Récupérer tous les utilisateurs ──────────────────────────────────────────
$filtre_role   = isset($_GET['role'])   ? $_GET['role']   : '';
$filtre_statut = isset($_GET['statut']) ? $_GET['statut'] : '';

$q_users = "SELECT id, nom, prenoms, email, telephone, role, statut, date_inscription 
            FROM utilisateurs 
            WHERE 1=1";
$params = [];
if ($filtre_role) {
    $q_users .= " AND role = :role";
    $params[':role'] = $filtre_role;
}
if ($filtre_statut) {
    $q_users .= " AND statut = :statut";
    $params[':statut'] = $filtre_statut;
}
$q_users .= " ORDER BY date_inscription DESC";
$s_users = $db->prepare($q_users);
foreach ($params as $k => $v) $s_users->bindValue($k, $v);
$s_users->execute();
$utilisateurs = $s_users->fetchAll(PDO::FETCH_ASSOC);

// Compteurs rapides
$q_counts = "SELECT
    SUM(role='membre' AND statut='actif') as membres_actifs,
    SUM(role='bibliothecaire') as bibliothecaires,
    SUM(statut='en_attente') as en_attente,
    SUM(statut='suspendu') as suspendus
    FROM utilisateurs";
$counts = $db->query($q_counts)->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - <?php echo SITE_NAME; ?></title>
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
                        <a class="nav-link" href="catalogue.php">📚 Gestion catalogue</a>
                        <a class="nav-link active" href="utilisateurs.php">👥 Gestion utilisateurs</a>
                        <a class="nav-link" href="../catalogue.php">📖 Voir catalogue</a>
                        <hr>
                        <a class="nav-link text-danger" href="../deconnexion.php">🚪 Déconnexion</a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gestion des utilisateurs</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creerBiblioModal">
                        ➕ Créer un bibliothécaire
                    </button>
                </div>

                <?php echo afficherMessage(); ?>

                <!-- COMPTEURS RAPIDES -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <a href="utilisateurs.php?role=membre&statut=actif" class="text-decoration-none">
                            <div class="card text-center p-3 h-100">
                                <div class="fw-bold fs-4 text-primary"><?php echo $counts['membres_actifs']; ?></div>
                                <small class="text-muted">Membres actifs</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="utilisateurs.php?role=bibliothecaire" class="text-decoration-none">
                            <div class="card text-center p-3 h-100">
                                <div class="fw-bold fs-4 text-primary"><?php echo $counts['bibliothecaires']; ?></div>
                                <small class="text-muted">Bibliothécaires</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="utilisateurs.php?statut=en_attente" class="text-decoration-none">
                            <div class="card text-center p-3 h-100">
                                <div class="fw-bold fs-4 text-warning"><?php echo $counts['en_attente']; ?></div>
                                <small class="text-muted">En attente</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="utilisateurs.php?statut=suspendu" class="text-decoration-none">
                            <div class="card text-center p-3 h-100">
                                <div class="fw-bold fs-4 text-danger"><?php echo $counts['suspendus']; ?></div>
                                <small class="text-muted">Suspendus</small>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- FILTRES -->
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                            <select name="role" class="form-select form-select-sm" style="width:auto">
                                <option value="">Tous les rôles</option>
                                <option value="membre"        <?php echo $filtre_role==='membre'        ? 'selected':'' ?>>Membre</option>
                                <option value="bibliothecaire" <?php echo $filtre_role==='bibliothecaire' ? 'selected':'' ?>>Bibliothécaire</option>
                                <option value="admin"         <?php echo $filtre_role==='admin'         ? 'selected':'' ?>>Admin</option>
                            </select>
                            <select name="statut" class="form-select form-select-sm" style="width:auto">
                                <option value="">Tous les statuts</option>
                                <option value="actif"      <?php echo $filtre_statut==='actif'      ? 'selected':'' ?>>Actif</option>
                                <option value="en_attente" <?php echo $filtre_statut==='en_attente' ? 'selected':'' ?>>En attente</option>
                                <option value="suspendu"   <?php echo $filtre_statut==='suspendu'   ? 'selected':'' ?>>Suspendu</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Filtrer</button>
                            <?php if ($filtre_role || $filtre_statut): ?>
                            <a href="utilisateurs.php" class="btn btn-sm btn-link text-muted">Réinitialiser</a>
                            <?php endif; ?>
                            <span class="ms-auto text-muted small"><?php echo count($utilisateurs); ?> utilisateur(s)</span>
                        </form>
                    </div>
                </div>

                <!-- TABLEAU -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Contact</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                        <th>Inscrit le</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($utilisateurs as $u): ?>
                                    <tr class="<?php echo $u['id']==$user['id'] ? 'table-light' : ''; ?>">
                                        <td>
                                            <strong><?php echo htmlspecialchars($u['prenoms'].' '.$u['nom']); ?></strong>
                                            <?php if ($u['id']==$user['id']): ?>
                                            <span class="badge bg-secondary ms-1">Vous</span>
                                            <?php endif; ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($u['email']); ?></small>
                                        </td>
                                        <td><small><?php echo htmlspecialchars($u['telephone']); ?></small></td>
                                        <td>
                                            <span class="badge bg-<?php echo $u['role']==='admin' ? 'danger' : ($u['role']==='bibliothecaire' ? 'primary' : 'secondary'); ?>">
                                                <?php echo ucfirst($u['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $u['statut']==='actif' ? 'success' : ($u['statut']==='en_attente' ? 'warning' : 'danger'); ?>">
                                                <?php echo ucfirst(str_replace('_',' ',$u['statut'])); ?>
                                            </span>
                                        </td>
                                        <td><small><?php echo formaterDate($u['date_inscription']); ?></small></td>
                                        <td class="text-end">
                                            <?php if ($u['id'] != $user['id']): ?>
                                            <div class="btn-group btn-group-sm">
                                                <!-- Modifier -->
                                                <button class="btn btn-outline-primary" title="Modifier"
                                                    data-bs-toggle="modal" data-bs-target="#modifierModal"
                                                    data-id="<?php echo $u['id']; ?>"
                                                    data-nom="<?php echo htmlspecialchars($u['nom']); ?>"
                                                    data-prenoms="<?php echo htmlspecialchars($u['prenoms']); ?>"
                                                    data-telephone="<?php echo htmlspecialchars($u['telephone']); ?>">
                                                    ✏️
                                                </button>
                                                <!-- Changer rôle -->
                                                <button class="btn btn-outline-secondary" title="Changer rôle"
                                                    data-bs-toggle="modal" data-bs-target="#roleModal"
                                                    data-id="<?php echo $u['id']; ?>"
                                                    data-nom="<?php echo htmlspecialchars($u['prenoms'].' '.$u['nom']); ?>"
                                                    data-role="<?php echo $u['role']; ?>">
                                                    🔑
                                                </button>
                                                <!-- Statut -->
                                                <?php if ($u['statut'] === 'actif'): ?>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Suspendre cet utilisateur ?')">
                                                    <input type="hidden" name="utilisateur_id" value="<?php echo $u['id']; ?>">
                                                    <input type="hidden" name="nouveau_statut" value="suspendu">
                                                    <button type="submit" name="changer_statut" class="btn btn-outline-warning" title="Suspendre">🔒</button>
                                                </form>
                                                <?php elseif ($u['statut'] === 'en_attente'): ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="utilisateur_id" value="<?php echo $u['id']; ?>">
                                                    <input type="hidden" name="nouveau_statut" value="actif">
                                                    <button type="submit" name="changer_statut" class="btn btn-outline-success" title="Activer">✅</button>
                                                </form>
                                                <?php else: ?>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="utilisateur_id" value="<?php echo $u['id']; ?>">
                                                    <input type="hidden" name="nouveau_statut" value="actif">
                                                    <button type="submit" name="changer_statut" class="btn btn-outline-success" title="Réactiver">🔓</button>
                                                </form>
                                                <?php endif; ?>
                                                <!-- Reset mdp -->
                                                <button class="btn btn-outline-info" title="Réinitialiser mot de passe"
                                                    data-bs-toggle="modal" data-bs-target="#resetMdpModal"
                                                    data-id="<?php echo $u['id']; ?>"
                                                    data-nom="<?php echo htmlspecialchars($u['prenoms'].' '.$u['nom']); ?>">
                                                    🔐
                                                </button>
                                                <!-- Supprimer -->
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement cet utilisateur ?')">
                                                    <input type="hidden" name="utilisateur_id" value="<?php echo $u['id']; ?>">
                                                    <button type="submit" name="supprimer_utilisateur" class="btn btn-outline-danger" title="Supprimer">🗑️</button>
                                                </form>
                                            </div>
                                            <?php else: ?>
                                            <small class="text-muted">—</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($utilisateurs)): ?>
                                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL : Créer bibliothécaire -->
    <div class="modal fade" id="creerBiblioModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">➕ Créer un bibliothécaire</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Nom *</label>
                                <input type="text" class="form-control" name="nom" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Prénoms *</label>
                                <input type="text" class="form-control" name="prenoms" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control" name="telephone" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Mot de passe *</label>
                                <input type="password" class="form-control" name="mot_de_passe" minlength="6" required>
                                <div class="form-text">Minimum 6 caractères</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="creer_bibliothecaire" class="btn btn-primary">Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL : Modifier informations -->
    <div class="modal fade" id="modifierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="utilisateur_id" id="modifier_id">
                    <div class="modal-header">
                        <h5 class="modal-title">✏️ Modifier l'utilisateur</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Nom *</label>
                                <input type="text" class="form-control" name="nom" id="modifier_nom" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Prénoms *</label>
                                <input type="text" class="form-control" name="prenoms" id="modifier_prenoms" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" name="telephone" id="modifier_telephone">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="modifier_utilisateur" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL : Changer rôle -->
    <div class="modal fade" id="roleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="utilisateur_id" id="role_id">
                    <div class="modal-header">
                        <h5 class="modal-title">🔑 Changer le rôle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Utilisateur : <strong id="role_nom"></strong></p>
                        <label class="form-label">Nouveau rôle *</label>
                        <select class="form-select" name="nouveau_role" id="role_select" required>
                            <option value="membre">Membre</option>
                            <option value="bibliothecaire">Bibliothécaire</option>
                            <option value="admin">Administrateur</option>
                        </select>
                        <div class="alert alert-warning mt-3 small mb-0">
                            ⚠️ Changer le rôle modifie les accès de l'utilisateur immédiatement.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="changer_role" class="btn btn-warning">Changer le rôle</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL : Réinitialiser mot de passe -->
    <div class="modal fade" id="resetMdpModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="utilisateur_id" id="reset_id">
                    <div class="modal-header">
                        <h5 class="modal-title">🔐 Réinitialiser le mot de passe</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Utilisateur : <strong id="reset_nom"></strong></p>
                        <label class="form-label">Nouveau mot de passe *</label>
                        <input type="password" class="form-control" name="nouveau_mot_de_passe" minlength="6" required>
                        <div class="form-text">Minimum 6 caractères</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="reset_password" class="btn btn-info text-white">Réinitialiser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Pré-remplir modal Modifier
        document.getElementById('modifierModal').addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('modifier_id').value       = btn.dataset.id;
            document.getElementById('modifier_nom').value      = btn.dataset.nom;
            document.getElementById('modifier_prenoms').value  = btn.dataset.prenoms;
            document.getElementById('modifier_telephone').value = btn.dataset.telephone;
        });

        // Pré-remplir modal Rôle
        document.getElementById('roleModal').addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('role_id').value     = btn.dataset.id;
            document.getElementById('role_nom').textContent = btn.dataset.nom;
            document.getElementById('role_select').value = btn.dataset.role;
        });

        // Pré-remplir modal Reset mdp
        document.getElementById('resetMdpModal').addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('reset_id').value       = btn.dataset.id;
            document.getElementById('reset_nom').textContent = btn.dataset.nom;
        });
    </script>
</body>
</html>
