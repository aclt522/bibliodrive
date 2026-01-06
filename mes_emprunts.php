<?php
session_start();
require_once('connexion.php');

if (!isset($_SESSION['mel'])) {
    header('Location: login.php');
    exit();
}

$mel = $_SESSION['mel'];

// Récupérer les emprunts de l’utilisateur
$stmt = $connexion->prepare("
    SELECT e.nolivre, e.dateemprunt, e.dateretour,
           l.titre, l.anneeparution, l.photo,
           a.prenom, a.nom
    FROM emprunter e
    INNER JOIN livre l ON e.nolivre = l.nolivre
    INNER JOIN auteur a ON l.noauteur = a.noauteur
    WHERE e.mel = :mel
    ORDER BY e.dateemprunt DESC
");
$stmt->bindValue(':mel', $mel);
$stmt->execute();
$emprunts = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mes emprunts</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.book-img { width: 100px; height: 150px; object-fit: cover; }
</style>
</head>
<body>
<div class="container mt-4">

    <h1 class="mb-3 text-center">Mes emprunts</h1>

    <!-- Bouton retour à l'accueil -->
    <div class="d-flex justify-content-center mb-4">
        <a href="index.php" class="btn btn-secondary">← Retour à l'accueil</a>
    </div>

    <?php if (!empty($emprunts)): ?>
    <table class="table table-striped text-center align-middle">
        <thead>
            <tr>
                <th>Livre</th>
                <th>Auteur</th>
                <th>Année</th>
                <th>Date d'emprunt</th>
                <th>Date de retour</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprunts as $e): ?>
            <tr>
                <td>
                    <img src="<?= (!empty($e->photo) && file_exists('covers/'.$e->photo)) ? 'covers/'.$e->photo : 'covers/default.jpg' ?>" class="book-img mb-1" alt="<?= htmlspecialchars($e->titre) ?>"><br>
                    <?= htmlspecialchars($e->titre) ?>
                </td>
                <td><?= htmlspecialchars($e->prenom . ' ' . $e->nom) ?></td>
                <td><?= htmlspecialchars($e->anneeparution) ?></td>
                <td><?= date('d/m/Y', strtotime($e->dateemprunt)) ?></td>
                <td><?= date('d/m/Y', strtotime($e->dateretour)) ?></td>
                <td>
                    <?php if (strtotime($e->dateretour) >= time()): ?>
                        <span class="text-success">En cours</span>
                    <?php else: ?>
                        <span class="text-danger">Retourné / Expiré</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php else: ?>
    <p class="text-center">Vous n'avez aucun emprunt pour le moment.</p>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
