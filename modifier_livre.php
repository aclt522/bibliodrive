<?php
session_start();
require_once('connexion.php');
require_once('securite_admin.php');

if (!isset($_GET['nolivre'])) {
    header('Location: gere_les_livres.php');
    exit();
}

$nolivre = (int)$_GET['nolivre'];
$message = "";

/* Récupération du livre */
$stmt = $connexion->prepare("
    SELECT livre.*, auteur.nom AS auteur_nom, auteur.prenom AS auteur_prenom
    FROM livre
    INNER JOIN auteur ON livre.noauteur = auteur.noauteur
    WHERE nolivre = :nolivre
");
$stmt->bindValue(':nolivre', $nolivre);
$stmt->execute();
$livre = $stmt->fetch(PDO::FETCH_OBJ);

if (!$livre) {
    header('Location: gere_les_livres.php');
    exit();
}

/* Tous les auteurs */
$authors = $connexion->query("SELECT * FROM auteur ORDER BY nom ASC")->fetchAll(PDO::FETCH_OBJ);

/* Traitement formulaire */
if (isset($_POST['modifier'])) {

    $titre = $_POST['titre'];
    $annee = $_POST['anneeparution'];
    $isbn = $_POST['isbn13'];
    $detail = $_POST['detail'];
    $noauteur = (int)$_POST['auteur'];

    $photo = $livre->photo;

    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = 'covers/livre_' . $nolivre . '.' . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], $filename);
        $photo = basename($filename);
    }

    $stmtUpdate = $connexion->prepare("
        UPDATE livre SET
            titre = :titre,
            anneeparution = :annee,
            isbn13 = :isbn,
            detail = :detail,
            noauteur = :noauteur,
            photo = :photo
        WHERE nolivre = :nolivre
    ");

    $stmtUpdate->execute([
        ':titre' => $titre,
        ':annee' => $annee,
        ':isbn' => $isbn,
        ':detail' => $detail,
        ':noauteur' => $noauteur,
        ':photo' => $photo,
        ':nolivre' => $nolivre
    ]);

    header('Location: gere_les_livres.php?msg=modifie');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier un livre</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/modifier_livre.css">
</head>
<body>

<div class="container mt-5">
    <h1>Modifier le livre</h1>

    <?= $message ?>

    <form method="POST" enctype="multipart/form-data" class="form-container">

        <!-- Image -->
        <div class="text-center mb-3">
            <img
                src="<?= (!empty($livre->photo) && file_exists('covers/'.$livre->photo)) ? 'covers/'.$livre->photo : 'covers/default.jpg' ?>"
                class="book-img-edit"
                alt="Livre">
        </div>

        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($livre->titre) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Auteur</label>
            <select name="auteur" class="form-select" required>
                <?php foreach ($authors as $a): ?>
                    <option value="<?= $a->noauteur ?>" <?= $a->noauteur == $livre->noauteur ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a->prenom . ' ' . $a->nom) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Année</label>
            <input type="number" name="anneeparution" class="form-control" value="<?= $livre->anneeparution ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn13" class="form-control" value="<?= htmlspecialchars($livre->isbn13) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Détails</label>
            <textarea name="detail" class="form-control" rows="4"><?= htmlspecialchars($livre->detail) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Changer l'image</label>
            <input type="file" name="photo" class="form-control">
        </div>

        <div class="d-flex justify-content-between">
            <a href="gere_les_livres.php" class="btn btn-secondary">← Retour</a>
            <button type="submit" name="modifier" class="btn btn-primary">Modifier</button>
        </div>

    </form>
</div>

</body>
</html>
