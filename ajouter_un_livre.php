<?php
require_once('connexion.php');

require_once('securite_admin.php');

$message = "";

// =============================
// TRAITEMENT DU FORMULAIRE
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? null;
    $isbn13 = $_POST['isbn13'] ?? null;
    $anneeparution = $_POST['anneeparution'] ?? null;
    $resume = $_POST['resume'] ?? null;
    $auteur_id = $_POST['auteur'] ?? null;
    $nouveau_auteur = !empty($_POST['nouveau_auteur']) ? trim($_POST['nouveau_auteur']) : null;
    $photo = null;

    if (!$titre || !$isbn13 || !$anneeparution || !$resume) {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs obligatoires.</div>";
    } else {
        // Ajout du nouvel auteur si fourni
        if ($nouveau_auteur) {
            $parts = explode(' ', $nouveau_auteur, 2);
            $prenom_auteur = $parts[0];
            $nom_auteur = $parts[1] ?? '';
            $stmt = $connexion->prepare("INSERT INTO auteur (prenom, nom) VALUES (:prenom, :nom)");
            $stmt->bindValue(':prenom', $prenom_auteur);
            $stmt->bindValue(':nom', $nom_auteur);
            $stmt->execute();
            $auteur_id = $connexion->lastInsertId();
        }

        // Gestion de l'image
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "covers/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $fileName = uniqid() . "_" . basename($_FILES['photo']['name']);
            $targetFile = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            if (in_array($fileType, ['jpg', 'jpeg', 'png'])) {
                move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
                $photo = $fileName;
            }
        }

        // Insertion dans la BDD
        $dateajout = date('Y-m-d');
        $stmt = $connexion->prepare("
            INSERT INTO livre (titre, isbn13, anneeparution, detail, photo, noauteur, dateajout)
            VALUES (:titre, :isbn13, :anneeparution, :resume, :photo, :auteur, :dateajout)
        ");
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':isbn13', $isbn13);
        $stmt->bindValue(':anneeparution', $anneeparution);
        $stmt->bindValue(':resume', $resume);
        $stmt->bindValue(':photo', $photo);
        $stmt->bindValue(':auteur', $auteur_id);
        $stmt->bindValue(':dateajout', $dateajout);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>📚 Livre ajouté avec succès !</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Erreur lors de l'ajout du livre.</div>";
        }
    }
}

// =============================
// Récupération des auteurs
// =============================
$auteursStmt = $connexion->query("SELECT * FROM auteur ORDER BY nom");
$auteurs = $auteursStmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un livre</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Ajouter un livre</h1>

   <!-- Bouton retour -->
    <div class="mb-3">
        <a href="page_admin.php" class="btn btn-secondary">
            ← Retour à l'administration
        </a>
    </div>

    <?= $message ?>

    <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Titre *</label>
            <input type="text" class="form-control" name="titre" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">ISBN13 *</label>
            <input type="text" class="form-control" name="isbn13" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Année de parution *</label>
            <input type="number" class="form-control" name="anneeparution" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Auteur existant</label>
            <select class="form-select" name="auteur">
                <option value="">-- Choisir un auteur --</option>
                <?php foreach ($auteurs as $auteur): ?>
                    <option value="<?= $auteur->noauteur ?>">
                        <?= htmlspecialchars($auteur->prenom . ' ' . $auteur->nom) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Ou ajouter un nouvel auteur</label>
            <input type="text" class="form-control" name="nouveau_auteur" placeholder="Prénom Nom">
        </div>
        <div class="col-12">
            <label class="form-label">Résumé *</label>
            <textarea class="form-control" name="resume" rows="5" required></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">Image de couverture</label>
            <input type="file" class="form-control" name="photo" accept="image/jpeg, image/png">
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100 py-2">Ajouter le livre</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
