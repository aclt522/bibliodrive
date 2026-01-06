<?php
session_start();
require_once('connexion.php');

require_once('securite_admin.php');

// =======================
// Suppression d'un livre
// =======================
if(isset($_GET['supprimer']) && isset($_GET['nolivre'])) {
    $nolivre = (int)$_GET['nolivre'];

    // Vérifie si le livre est emprunté
    $stmtCheck = $connexion->prepare("SELECT COUNT(*) FROM emprunter WHERE nolivre=:nolivre AND dateretour >= CURDATE()");
    $stmtCheck->bindValue(':nolivre', $nolivre);
    $stmtCheck->execute();
    $emprunts = (int)$stmtCheck->fetchColumn();

    if($emprunts === 0) {
        $stmtDel = $connexion->prepare("DELETE FROM livre WHERE nolivre=:nolivre");
        $stmtDel->bindValue(':nolivre', $nolivre);
        $stmtDel->execute();
        $message = "<div class='alert alert-success text-center mt-3'>Livre supprimé avec succès.</div>";
    } else {
        $message = "<div class='alert alert-danger text-center mt-3'>⚠️ Impossible de supprimer un livre actuellement emprunté.</div>";
    }

    header("Location: gere_les_livres.php");
    exit();
}

// =======================
// Recherche de livres
// =======================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if($search !== '') {
    $stmt = $connexion->prepare("
        SELECT l.*, a.prenom, a.nom 
        FROM livre l 
        INNER JOIN auteur a ON l.noauteur = a.noauteur
        WHERE l.titre LIKE :search 
           OR l.isbn13 LIKE :search 
           OR CONCAT(a.prenom, ' ', a.nom) LIKE :search
        ORDER BY l.titre
    ");
    $stmt->bindValue(':search', "%$search%");
} else {
    $stmt = $connexion->prepare("
        SELECT l.*, a.prenom, a.nom 
        FROM livre l 
        INNER JOIN auteur a ON l.noauteur = a.noauteur
        ORDER BY l.titre
    ");
}
$stmt->execute();
$livres = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gérer les livres</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.book-img { width: 80px; height: 120px; object-fit: cover; }
</style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
<h1 class="mb-4 text-center">Gestion des livres</h1>

<?= $message ?? '' ?>

<!-- Barre de recherche -->
<div class="d-flex justify-content-center my-3">
    <form method="GET" class="d-flex align-items-center" role="search">
        <input
            type="search"
            name="search"
            class="form-control me-3 rounded-pill"
            placeholder="Rechercher par titre, auteur ou ISBN"
            value="<?= htmlspecialchars($search) ?>"
            style="width: 400px; height: 38px; padding: 0 15px;"
        >
        <button type="submit" class="btn btn-outline-primary rounded-pill px-4" style="height: 38px;">
            🔍
        </button>
    </form>
</div>



<div class="mb-3 text-center">
    <a href="page_admin.php" class="btn btn-secondary">← Retour à l'administration</a>
    <a href="ajouter_un_livre.php" class="btn btn-success">➕ Ajouter un nouveau livre</a>
</div>

<table class="table table-striped text-center align-middle">
<thead class="table-dark">
<tr>
<th>Photo</th>
<th>Titre</th>
<th>Auteur</th>
<th>Année</th>
<th>ISBN</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach($livres as $l): ?>
<tr>
<td>
    <img src="<?= (!empty($l->photo) && file_exists('covers/'.$l->photo)) ? 'covers/'.$l->photo : 'covers/default.jpg' ?>" class="book-img" alt="<?= htmlspecialchars($l->titre) ?>">
</td>
<td><?= htmlspecialchars($l->titre) ?></td>
<td><?= htmlspecialchars($l->prenom . ' ' . $l->nom) ?></td>
<td><?= htmlspecialchars($l->anneeparution) ?></td>
<td><?= htmlspecialchars($l->isbn13) ?></td>
<td>
    <a href="modifier_livre.php?nolivre=<?= $l->nolivre ?>" class="btn btn-warning btn-sm">Modifier</a>

    <?php
    // Vérifie si livre emprunté
    $stmt2 = $connexion->prepare("SELECT COUNT(*) FROM emprunter WHERE nolivre=:nolivre AND dateretour >= CURDATE()");
    $stmt2->bindValue(':nolivre', $l->nolivre);
    $stmt2->execute();
    $emprunts = (int)$stmt2->fetchColumn();
    ?>
    <?php if($emprunts === 0): ?>
        <a href="gere_les_livres.php?supprimer=1&nolivre=<?= $l->nolivre ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer ce livre ?')">Supprimer</a>
    <?php else: ?>
        <button class="btn btn-dark btn-sm" disabled>Emprunt en cours</button>
    <?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
