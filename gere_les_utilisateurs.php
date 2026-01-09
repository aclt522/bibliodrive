<?php
session_start();
require_once('connexion.php');

require_once('securite_admin.php');

// =======================
// Changement de profil
// =======================
if(isset($_GET['changer_profil']) && isset($_GET['mel'])) {
    $mel = $_GET['mel'];
    $nouveauProfil = $_GET['profil'] === 'admin' ? 'admin' : 'client';

    $stmt = $connexion->prepare("UPDATE utilisateur SET profil=:profil WHERE mel=:mel");
    $stmt->bindValue(':profil', $nouveauProfil);
    $stmt->bindValue(':mel', $mel);
    $stmt->execute();

    header("Location: gere_les_utilisateurs.php");
    exit();
}

// =======================
// Suppression utilisateur
// =======================
if(isset($_GET['supprimer']) && isset($_GET['mel'])) {
    $mel = $_GET['mel'];

    // Vérifie s'il a des emprunts non retournés
    $stmt = $connexion->prepare("SELECT COUNT(*) FROM emprunter WHERE mel=:mel AND dateretour >= CURDATE()");
    $stmt->bindValue(':mel', $mel);
    $stmt->execute();
    $emprunts = (int)$stmt->fetchColumn();

    if($emprunts === 0) {
        $stmt = $connexion->prepare("DELETE FROM utilisateur WHERE mel=:mel");
        $stmt->bindValue(':mel', $mel);
        $stmt->execute();
    } else {
        $message = "<div class='alert alert-danger text-center mt-3'>⚠️ Impossible de supprimer un utilisateur avec des emprunts en cours.</div>";
    }

    header("Location: gere_utilisateurs.php");
    exit();
}

// =======================
// Recherche utilisateur
// =======================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if($search !== '') {
    $stmt = $connexion->prepare("
        SELECT * FROM utilisateur
        WHERE nom LIKE :search OR prenom LIKE :search OR mel LIKE :search
        ORDER BY nom, prenom
    ");
    $stmt->bindValue(':search', "%$search%");
} else {
    $stmt = $connexion->prepare("SELECT * FROM utilisateur ORDER BY nom, prenom");
}
$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gérer les utilisateurs</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">

<h1 class="mb-4 text-center">Gestion des utilisateurs</h1>

<!-- Bouton Ajouter utilisateur + retour à l'accueil + barre de recherche -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <!-- Boutons gauche -->
    <div class="d-flex gap-2">
        <a href="page_admin.php" class="btn btn-secondary">
            ← Retour à l'administration
        </a>

        <a href="ajouter_un_membre.php" class="btn btn-success">
            ➕ Ajouter un utilisateur
        </a>
    </div>

    <!-- Recherche droite -->
    <form method="GET" class="d-flex" role="search" style="max-width: 400px;">
        <input type="search" name="search" class="form-control me-2 rounded-pill"
               placeholder="Rechercher par nom, prénom ou email"
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-primary rounded-pill">
        🔍
    </button>
    </form>

</div>

<table class="table table-striped text-center align-middle">
<thead class="table-dark">
<tr>
<th>Email</th>
<th>Nom</th>
<th>Prénom</th>
<th>Profil</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach($utilisateurs as $u): ?>
<tr>
<td><?= htmlspecialchars($u->mel) ?></td>
<td><?= htmlspecialchars($u->nom) ?></td>
<td><?= htmlspecialchars($u->prenom) ?></td>
<td>
    <div class="dropdown">
        <button class="btn btn-sm dropdown-toggle 
            <?= $u->profil === 'admin' ? 'btn-success' : 'btn-secondary' ?>"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">
            <?= htmlspecialchars($u->profil) ?>
        </button>

        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item <?= $u->profil === 'admin' ? 'disabled' : '' ?>"
                   href="gere_les_utilisateurs.php?changer_profil=1&mel=<?= $u->mel ?>&profil=admin">
                    👑 Admin
                </a>
            </li>
            <li>
                <a class="dropdown-item <?= $u->profil === 'client' ? 'disabled' : '' ?>"
                   href="gere_les_utilisateurs.php?changer_profil=1&mel=<?= $u->mel ?>&profil=client">
                    👤 Client
                </a>
            </li>
        </ul>
    </div>
</td>

<td>
    <?php
    // Vérifie si l'utilisateur a des emprunts
    $stmt2 = $connexion->prepare("SELECT COUNT(*) FROM emprunter WHERE mel=:mel AND dateretour >= CURDATE()");
    $stmt2->bindValue(':mel', $u->mel);
    $stmt2->execute();
    $emprunts = (int)$stmt2->fetchColumn();
    ?>
    <?php if($emprunts === 0): ?>
        <a href="gere_les_utilisateurs.php?supprimer=1&mel=<?= $u->mel ?>" class="btn btn-danger btn-sm" onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')">Supprimer</a>
    <?php else: ?>
        <button class="btn btn-dark btn-sm" disabled>Limite emprunts</button>
    <?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
