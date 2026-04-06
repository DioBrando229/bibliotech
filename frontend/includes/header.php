<?php
// S'assurer que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = getUtilisateurConnecte();

// Déterminer le chemin de base
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$base_path = ($current_dir === 'frontend') ? '' : '../';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $base_path; ?>index.php">
            <span class="text-primary">Biblio</span><span class="text-info">Tech</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>catalogue.php">Catalogue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>a-propos.php">À propos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>contact.php">Contact</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if ($user): ?>
                    <!-- Utilisateur connecté -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="badge bg-primary rounded-circle me-2" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;">
                                <?php echo strtoupper(substr($user['prenoms'], 0, 1) . substr($user['nom'], 0, 1)); ?>
                            </span>
                            <?php echo htmlspecialchars($user['prenoms'] . ' ' . $user['nom']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($user['role'] === 'membre'): ?>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>membre/dashboard.php">📊 Tableau de bord</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>membre/profil.php">👤 Mon profil</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>membre/emprunts.php">📚 Mes emprunts</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>membre/reservations.php">🔖 Mes réservations</a></li>
                            <?php elseif ($user['role'] === 'bibliothecaire'): ?>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>bibliothecaire/dashboard.php">📊 Tableau de bord</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>bibliothecaire/emprunts.php">📚 Gestion emprunts</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>bibliothecaire/membres.php">👥 Gestion membres</a></li>
                            <?php elseif ($user['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>admin/dashboard.php">📊 Tableau de bord</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>admin/catalogue.php">📚 Gestion catalogue</a></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>admin/utilisateurs.php">👥 Gestion utilisateurs</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?php echo $base_path; ?>deconnexion.php">🚪 Déconnexion</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Utilisateur non connecté -->
                    <li class="nav-item">
                        <a class="nav-link" href="connexion.php">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="inscription.php">Inscription</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
