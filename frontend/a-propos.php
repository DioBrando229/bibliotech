<?php
require_once '../backend/init.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="bg-primary text-white py-4">
        <div class="container">
            <h1 class="mb-2">À propos de BiblioTech</h1>
            <p class="mb-0">Découvrez notre bibliothèque communautaire</p>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-5">
                        <h2 class="mb-4">Notre Mission</h2>
                        <p class="lead">
                            BiblioTech est une bibliothèque communautaire moderne qui facilite l'accès à la lecture 
                            et au savoir pour tous au Bénin.
                        </p>
                        <p>
                            Dans un contexte où l'accès à l'information reste un défi pour de nombreuses communautés, 
                            BiblioTech joue un rôle crucial dans la promotion de l'éducation et de la culture. 
                            Notre plateforme digitale vous permet de gérer vos emprunts en toute simplicité.
                        </p>

                        <hr class="my-4">

                        <h3 class="mb-3">Nos Services</h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-primary rounded-circle p-3" style="font-size: 1.5rem;">📚</span>
                                    </div>
                                    <div>
                                        <h5>Catalogue Riche</h5>
                                        <p class="text-muted">Plus de 5000 ouvrages dans diverses catégories</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-primary rounded-circle p-3" style="font-size: 1.5rem;">💻</span>
                                    </div>
                                    <div>
                                        <h5>Gestion Digitale</h5>
                                        <p class="text-muted">Empruntez et réservez vos livres en ligne 24/7</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-primary rounded-circle p-3" style="font-size: 1.5rem;">🔒</span>
                                    </div>
                                    <div>
                                        <h5>Sécurisé</h5>
                                        <p class="text-muted">Vos données sont protégées et sécurisées</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="badge bg-primary rounded-circle p-3" style="font-size: 1.5rem;">🆓</span>
                                    </div>
                                    <div>
                                        <h5>Accès Gratuit</h5>
                                        <p class="text-muted">Inscription gratuite pour tous les membres</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h3 class="mb-3">Horaires d'Ouverture</h3>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td><strong>Lundi - Vendredi</strong></td>
                                    <td>8h00 - 18h00</td>
                                </tr>
                                <tr>
                                    <td><strong>Samedi</strong></td>
                                    <td>9h00 - 13h00</td>
                                </tr>
                                <tr>
                                    <td><strong>Dimanche</strong></td>
                                    <td>Fermé</td>
                                </tr>
                            </tbody>
                        </table>

                        <hr class="my-4">

                        <h3 class="mb-3">Localisation</h3>
                        <p>
                            <strong>📍 Adresse :</strong> Abomey-Calavi, Bénin<br>
                            <strong>📧 Email :</strong> contact@bibliotech.bj<br>
                            <strong>📱 Téléphone :</strong> +229 XX XX XX XX
                        </p>

                        <div class="mt-4 text-center">
                            <a href="inscription.php" class="btn btn-primary btn-lg">Devenir membre</a>
                            <a href="catalogue.php" class="btn btn-outline-primary btn-lg">Voir le catalogue</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
