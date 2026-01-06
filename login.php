<?php
/*************************
 * DÉMARRAGE SESSION
 *************************/
session_start();

/*************************
 * CONNEXION BDD
 *************************/
include('connexion.php'); // Inclusion de la base de données

/*************************
 * SI DÉJÀ CONNECTÉ → REDIRECTION
 *************************/
if (isset($_SESSION['mel'])) {
    if ($_SESSION['profil'] === 'admin') {
        header('Location: page_admin.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

/*************************
 * MESSAGE ERREUR
 *************************/
$message = "Veuillez saisir vos identifiants pour vous connecter.";

/*************************
 * TRAITEMENT FORMULAIRE
 *************************/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mel = $_POST['mel'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';

    // Récupération de l'utilisateur par email
    $stmt = $connexion->prepare("SELECT * FROM utilisateur WHERE mel = :mel");
    $stmt->bindValue(':mel', $mel);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$utilisateur) {
        $message = "Email inconnu";
    } elseif (!password_verify($motdepasse, $utilisateur->motdepasse)) {
        // Vérification sécurisée du mot de passe
        $message = "Mot de passe incorrect";
    } else {
        // Connexion réussie
        $_SESSION['mel'] = $utilisateur->mel;
        $_SESSION['profil'] = $utilisateur->profil;

        // Redirection selon le profil
        if ($utilisateur->profil === 'admin') {
            header('Location: page_admin.php');
        } else {
            header('Location: index.php');
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container mt-5" style="max-width: 400px;">

    <h2 class="text-center mb-4">Connexion</h2>

    <?php if ($message): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="mel" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="motdepasse" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Se connecter
        </button>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
