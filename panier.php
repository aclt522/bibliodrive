<?php
session_start();
require_once('connexion.php');

if (!isset($_SESSION['mel'])) {
    header('Location: login.php');
    exit();
}

$mel = $_SESSION['mel'];
$panier = $_SESSION['panier'] ?? [];
$message = "";

// =============================
// Compter les emprunts en cours
// =============================
$stmt = $connexion->prepare("
    SELECT COUNT(*) 
    FROM emprunter 
    WHERE mel = :mel 
      AND dateretour >= CURDATE()
");
$stmt->bindValue(':mel', $mel);
$stmt->execute();
$empruntsEncours = (int)$stmt->fetchColumn();

$limiteAtteinte = ($empruntsEncours >= 5);
$hasEmprunts = ($empruntsEncours > 0);

// =============================
// Annuler un livre du panier
// =============================
if (isset($_GET['annuler'])) {
    $id = (int)$_GET['annuler'];
    if (($key = array_search($id, $panier)) !== false) {
        unset($_SESSION['panier'][$key]);
    }
    header("Location: panier.php");
    exit();
}

// =============================
// Valider le panier
// =============================
if (isset($_GET['valider']) && !$limiteAtteinte && !empty($panier)) {

    if ($empruntsEncours + count($panier) <= 5) {

        foreach ($panier as $idLivre) {

            // Vérifier si déjà emprunté
            $check = $connexion->prepare("
                SELECT COUNT(*) 
                FROM emprunter 
                WHERE nolivre = :nolivre 
                  AND dateretour >= CURDATE()
            ");
            $check->bindValue(':nolivre', $idLivre);
            $check->execute();

            if ((int)$check->fetchColumn() === 0) {
                $insert = $connexion->prepare("
                    INSERT INTO emprunter (mel, nolivre, dateemprunt, dateretour)
                    VALUES (:mel, :nolivre, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY))
                ");
                $insert->bindValue(':mel', $mel);
                $insert->bindValue(':nolivre', $idLivre);
                $insert->execute();
            }
        }

        $_SESSION['panier'] = [];
        $message = "<div class='alert alert-success text-center'>✅ Emprunt validé (retour sous 14 jours)</div>";

    } else {
        $message = "<div class='alert alert-danger text-center'>❌ Limite de 5 emprunts atteinte</div>";
    }
}

// =============================
// Récupérer les livres du panier
// =============================
$livresPanier = [];

if (!empty($panier)) {
    $in = implode(',', array_map('intval', $panier));
    $stmt = $connexion->query("
        SELECT l.*, a.prenom, a.nom
        FROM livre l
        INNER JOIN auteur a ON l.noauteur = a.noauteur
        WHERE l.nolivre IN ($in)
    ");
    $livresPanier = $stmt->fetchAll(PDO::FETCH_OBJ);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Panier</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h1 class="text-center mb-4">🛒 Votre panier</h1>

<?= $message ?>

<?php if (!empty($livresPanier)): ?>

<table class="table table-striped text-center">
<thead>
<tr>
    <th>Titre</th>
    <th>Auteur</th>
    <th>Année</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach ($livresPanier as $livre): ?>
<tr>
    <td><?= htmlspecialchars($livre->titre) ?></td>
    <td><?= htmlspecialchars($livre->prenom . ' ' . $livre->nom) ?></td>
    <td><?= htmlspecialchars($livre->anneeparution) ?></td>
    <td>
        <a href="panier.php?annuler=<?= $livre->nolivre ?>" class="btn btn-danger btn-sm">
            Annuler
        </a>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<div class="d-flex justify-content-between">
    <a href="index.php" class="btn btn-secondary">← Accueil</a>

    <?php if ($limiteAtteinte): ?>
        <button class="btn btn-danger" disabled>
            ❌ Limite d’emprunts atteinte
        </button>
    <?php else: ?>
        <a href="panier.php?valider=1" class="btn btn-success">
            ✅ Valider le panier
        </a>
    <?php endif; ?>
</div>

<?php else: ?>

<p class="text-center">Votre panier est vide.</p>

<div class="d-flex justify-content-center gap-2">
    <a href="index.php" class="btn btn-secondary">← Accueil</a>

    <?php if ($limiteAtteinte): ?>
        <button class="btn btn-danger" disabled>
            ❌ Limite d’emprunts atteinte
        </button>
    <?php else: ?>
        <a href="liste_des_livres.php" class="btn btn-primary">
            📚 Emprunter des livres
        </a>
    <?php endif; ?>
</div>

<?php endif; ?>

<!-- Bouton TOUJOURS accessible -->
<?php if ($hasEmprunts): ?>
<div class="text-center mt-4">
    <a href="mes_emprunts.php" class="btn btn-info">
        📖 Voir mes emprunts
    </a>
</div>
<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
