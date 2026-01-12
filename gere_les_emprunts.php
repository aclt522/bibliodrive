<?php
session_start();
require_once('connexion.php');
require_once('securite_admin.php');

// =============================
// RÉVOCATION D’UN EMPRUNT
// =============================
if (isset($_GET['revoquer']) && isset($_GET['mel'])) {
    $idLivre = (int)$_GET['revoquer'];
    $melUser = $_GET['mel'];

    $stmt = $connexion->prepare("
        DELETE FROM emprunter 
        WHERE mel = :mel AND nolivre = :nolivre
    ");
    $stmt->bindValue(':mel', $melUser);
    $stmt->bindValue(':nolivre', $idLivre);
    $stmt->execute();

    header("Location: gere_les_emprunts.php");
    exit();
}

// =============================
// RECHERCHE
// =============================
$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT e.mel, e.nolivre, e.dateemprunt,
           l.titre,
           a.prenom, a.nom
    FROM emprunter e
    INNER JOIN livre l ON e.nolivre = l.nolivre
    INNER JOIN auteur a ON l.noauteur = a.noauteur
";

if ($search !== '') {
    $sql .= " 
        WHERE e.mel LIKE :search 
        OR l.titre LIKE :search
        OR CONCAT(a.prenom,' ',a.nom) LIKE :search
    ";
}

$sql .= " ORDER BY e.dateemprunt DESC";

$stmt = $connexion->prepare($sql);

if ($search !== '') {
    $stmt->bindValue(':search', "%$search%");
}

$stmt->execute();
$emprunts = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gérer les emprunts</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/gere_les_emprunts.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">
    <h1 class="text-center mb-4">📚 Gestion des emprunts</h1>

    <!-- Barre de recherche -->
    <form method="GET" class="d-flex align-items-center justify-content-center mb-4" role="search">
        <input
            type="search"
            name="search"
            class="form-control me-3 rounded-pill search-form"
            placeholder="Utilisateur, livre ou auteur..."
            value="<?= htmlspecialchars($search) ?>"
        >
        <button type="submit" class="btn btn-outline-primary rounded-pill btn-search">
            🔍
        </button>
    </form>

    <!-- Tableau des emprunts -->
    <table class="table table-striped text-center align-middle">
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Livre</th>
                <th>Auteur</th>
                <th>Date emprunt</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprunts as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e->mel) ?></td>
                <td><?= htmlspecialchars($e->titre) ?></td>
                <td><?= htmlspecialchars($e->prenom.' '.$e->nom) ?></td>
                <td><?= date('d/m/Y', strtotime($e->dateemprunt)) ?></td>
                <td>
                    <a href="gere_les_emprunts.php?revoquer=<?= $e->nolivre ?>&mel=<?= urlencode($e->mel) ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Révoquer cet emprunt ?');">
                        Révoquer
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-center mt-3">
        <a href="page_admin.php" class="btn btn-secondary btn-return">← Retour admin</a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
