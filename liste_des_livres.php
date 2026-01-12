<?php
session_start();
require_once('connexion.php');

// Initialiser le panier si inexistant
if(!isset($_SESSION['panier'])) $_SESSION['panier'] = [];

// =============================
// AJOUT AU PANIER
// =============================
$messagePanier = '';
if(isset($_GET['ajouter']) && isset($_SESSION['mel'])) {
    $idAjouter = (int)$_GET['ajouter'];

    // Compter les emprunts en cours
    $stmt = $connexion->prepare("SELECT COUNT(*) FROM emprunter WHERE mel=:mel AND dateretour >= CURDATE()");
    $stmt->bindValue(':mel', $_SESSION['mel']);
    $stmt->execute();
    $empruntsEncours = (int)$stmt->fetchColumn();

    // Limite max 5
    if($empruntsEncours + count($_SESSION['panier']) < 5) {
        if(!in_array($idAjouter, $_SESSION['panier'])) {
            $_SESSION['panier'][] = $idAjouter;
            $messagePanier = "<div class='alert alert-success'>✅ Livre ajouté au panier avec succès !</div>";
        } else {
            $messagePanier = "<div class='alert alert-warning'>⚠️ Ce livre est déjà dans votre panier.</div>";
        }
    } else {
        $messagePanier = "<div class='alert alert-danger'>❌ Vous ne pouvez pas emprunter plus de 5 livres à la fois.</div>";
    }
}

// =============================
// RECHERCHE DES LIVRES
// =============================
$search = $_GET['search'] ?? '';

if ($search) {
    $stmt = $connexion->prepare("
        SELECT livre.nolivre, livre.titre, livre.anneeparution, livre.photo, livre.detail, livre.isbn13, auteur.nom, auteur.prenom
        FROM livre
        INNER JOIN auteur ON livre.noauteur = auteur.noauteur
        WHERE livre.titre LIKE :search 
           OR livre.isbn13 LIKE :search
           OR livre.anneeparution LIKE :search
           OR CONCAT(auteur.prenom, ' ', auteur.nom) LIKE :search
        ORDER BY livre.anneeparution DESC
    ");
    $stmt->bindValue(':search', "%$search%");
    $stmt->execute();
} else {
    $stmt = $connexion->prepare("
        SELECT livre.nolivre, livre.titre, livre.anneeparution, livre.photo, livre.detail, livre.isbn13, auteur.nom, auteur.prenom
        FROM livre
        INNER JOIN auteur ON livre.noauteur = auteur.noauteur
        ORDER BY livre.anneeparution DESC
    ");
    $stmt->execute();
}

$livres = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Liste des livres</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/liste_des_livres.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-4">

    <h1>Liste des livres</h1>

    <!-- Message d'ajout au panier -->
    <?= $messagePanier ?>

    <!-- Bouton retour -->
    <div class="d-flex justify-content-center mb-3">
        <a href="index.php" class="btn btn-secondary btn-return">← Retour à l'accueil</a>
    </div>

    <!-- Barre de recherche -->
    <form method="GET" class="d-flex align-items-center justify-content-center mb-4" role="search">
        <input
            type="search"
            name="search"
            class="form-control me-3 rounded-pill search-form"
            placeholder="Rechercher un livre..."
            value="<?= htmlspecialchars($search) ?>"
        >
        <button type="submit" class="btn btn-outline-primary rounded-pill btn-search">
            🔍
        </button>
    </form>

    <!-- Liste des livres -->
    <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
        <?php foreach($livres as $livre): ?>
            <?php 
                $imgPath = 'covers/' . ($livre->photo ?? '');

                // Vérifier si déjà emprunté
                $stmt2 = $connexion->prepare("SELECT COUNT(*) FROM emprunter WHERE nolivre = :nolivre AND dateretour >= CURDATE()");
                $stmt2->bindValue(':nolivre', $livre->nolivre);
                $stmt2->execute();
                $dejaEmprunte = ($stmt2->fetchColumn() > 0);

                // Vérifier si dans le panier
                $dansPanier = in_array($livre->nolivre, $_SESSION['panier'] ?? []);

                // Vérifier limite de 5
                $stmt3 = $connexion->prepare("SELECT COUNT(*) FROM emprunter WHERE mel=:mel AND dateretour >= CURDATE()");
                $stmt3->bindValue(':mel', $_SESSION['mel'] ?? '');
                $stmt3->execute();
                $empruntsEncours = (int)$stmt3->fetchColumn();
                $limiteAtteinte = isset($_SESSION['mel']) && ($empruntsEncours + count($_SESSION['panier']) >= 5);
            ?>
            <div class="col">
                <div class="card h-100">
                    <img src="<?= (!empty($livre->photo) && file_exists($imgPath)) ? $imgPath : 'covers/default.jpg' ?>" class="card-img-top book-img" alt="<?= htmlspecialchars($livre->titre) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($livre->titre) ?></h5>
                        <p class="card-text mb-2">
                            Auteur: <?= htmlspecialchars($livre->prenom . ' ' . $livre->nom) ?><br>
                            Année: <?= htmlspecialchars($livre->anneeparution) ?><br>
                            ISBN: <?= htmlspecialchars($livre->isbn13) ?><br>
                            <strong>Status:</strong> 
                            <?php if (!$dejaEmprunte): ?>
                                <span class="text-success">Disponible</span>
                            <?php else: ?>
                                <span class="text-danger">Indisponible</span>
                            <?php endif; ?>
                        </p>

                        <?php if(isset($_SESSION['mel'])): ?>
                            <?php if($dejaEmprunte): ?>
                                <button class="btn btn-secondary mt-auto" disabled>Déjà emprunté</button>
                            <?php elseif($dansPanier): ?>
                                <button class="btn btn-warning mt-auto" disabled>Déjà dans le panier</button>
                            <?php elseif($limiteAtteinte): ?>
                                <button class="btn btn-danger mt-auto" disabled>Limite atteinte</button>
                            <?php else: ?>
                                <a href="liste_des_livres.php?ajouter=<?= $livre->nolivre ?>" class="btn btn-primary mt-auto">Emprunter</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <small class="text-muted">Connectez-vous pour emprunter</small>
                        <?php endif; ?>

                        <button type="button" class="btn btn-secondary mt-2" data-bs-toggle="modal" data-bs-target="#detailModal<?= $livre->nolivre ?>">
                            Voir les détails
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal détails -->
            <div class="modal fade" id="detailModal<?= $livre->nolivre ?>" tabindex="-1" aria-labelledby="detailModalLabel<?= $livre->nolivre ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="detailModalLabel<?= $livre->nolivre ?>"><?= htmlspecialchars($livre->titre) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="<?= (!empty($livre->photo) && file_exists($imgPath)) ? $imgPath : 'covers/default.jpg' ?>" class="img-fluid" alt="<?= htmlspecialchars($livre->titre) ?>">
                                </div>
                                <div class="col-md-8">
                                    <p><strong>Auteur :</strong> <?= htmlspecialchars($livre->prenom . ' ' . $livre->nom) ?></p>
                                    <p><strong>Année :</strong> <?= htmlspecialchars($livre->anneeparution) ?></p>
                                    <p><strong>ISBN :</strong> <?= htmlspecialchars($livre->isbn13) ?></p>
                                    <p><strong>Détails :</strong><br><?= nl2br(htmlspecialchars($livre->detail)) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
