<?php
session_start();

require_once('securite_admin.php');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Admin - Bibliodrive</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<?php include('navbar.php'); ?>

<!-- Contenu admin -->
<div class="container mt-5">

    <h1 class="mb-4 text-center">Administration</h1>

    <div class="row justify-content-center g-4">

        <div class="col-md-4 col-12">
            <a href="ajouter_un_livre.php" class="btn btn-primary btn-lg w-100">
                ➕ Ajouter un livre
            </a>
        </div>

        <div class="col-md-4 col-12">
            <a href="ajouter_un_membre.php" class="btn btn-success btn-lg w-100">
                👤 Ajouter un membre
            </a>
        </div>

        <div class="col-md-4 col-12">
            <a href="gere_les_emprunts.php" class="btn btn-warning btn-lg w-100">
                📚 Gérer les emprunts
            </a>
        </div>

        <div class="col-md-4 col-12">
            <a href="gere_les_utilisateurs.php" class="btn btn-dark btn-lg w-100">
                👥 Gérer les utilisateurs
            </a>
        </div>

        <div class="col-md-4 col-12">
            <a href="gere_les_livres.php" class="btn btn-info btn-lg w-100">
                📝 Gérer les livres
            </a>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
