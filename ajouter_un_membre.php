<?php
require_once('connexion.php');
require_once('securite_admin.php');

$message = "";

// =============================
// TRAITEMENT DU FORMULAIRE
// =============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mel = $_POST['mel'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $codepostal = $_POST['codepostal'] ?? '';
    $profil = $_POST['profil'] ?? 'client';

    // Vérifier si l'email existe déjà
    $stmt = $connexion->prepare("SELECT mel FROM utilisateur WHERE mel = :mel");
    $stmt->bindValue(':mel', $mel);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $message = "<div class='alert alert-danger'>❌ Cet email est déjà utilisé.</div>";
    } else {
        $motdepasse_hash = password_hash($motdepasse, PASSWORD_DEFAULT);

        $stmt = $connexion->prepare("
            INSERT INTO utilisateur (mel, motdepasse, nom, prenom, adresse, ville, codepostal, profil)
            VALUES (:mel, :motdepasse, :nom, :prenom, :adresse, :ville, :codepostal, :profil)
        ");

        $stmt->bindValue(':mel', $mel);
        $stmt->bindValue(':motdepasse', $motdepasse_hash);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenom', $prenom);
        $stmt->bindValue(':adresse', $adresse);
        $stmt->bindValue(':ville', $ville);
        $stmt->bindValue(':codepostal', $codepostal);
        $stmt->bindValue(':profil', $profil);

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Membre ajouté avec succès !</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ Erreur lors de l'ajout du membre.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ajouter un membre</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- CSS global -->
<link rel="stylesheet" href="css/ajouter_un_membre.css">
</head>
<body>

<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Ajouter un membre</h1>

    <div class="mb-3">
        <a href="page_admin.php" class="btn btn-secondary">← Retour à l'administration</a>
    </div>

    <?= $message ?>

    <form method="POST" class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Email *</label>
            <input type="email" class="form-control" name="mel" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Mot de passe *</label>
            <input type="password" class="form-control" name="motdepasse" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Nom *</label>
            <input type="text" class="form-control" name="nom" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Prénom *</label>
            <input type="text" class="form-control" name="prenom" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Adresse *</label>
            <input type="text" class="form-control" name="adresse" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ville *</label>
            <input type="text" class="form-control" name="ville" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Code postal *</label>
            <input type="text" class="form-control" name="codepostal" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Profil *</label>
            <select class="form-select" name="profil" required>
                <option value="client">Client</option>
                <option value="admin">Administrateur</option>
            </select>
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100 py-2">Ajouter le membre</button>
        </div>

    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
