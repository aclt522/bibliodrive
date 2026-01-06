<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connexion.php';

$stmt = $connexion->prepare(
    "SELECT nom, prenom, ville, codepostal, profil, adresse
     FROM utilisateur
     WHERE mel = :mel"
);
$stmt->execute([':mel' => $_SESSION['mel']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) return;
?>

<div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">
        Profil
    </button>
    <ul class="dropdown-menu dropdown-menu-end p-3" style="min-width: 260px;">
        <li><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></li>
        <li><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></li>
        <li><strong>Ville :</strong> <?= htmlspecialchars($user['ville']) ?></li>
        <li><strong>Code postal :</strong> <?= htmlspecialchars($user['codepostal']) ?></li>
        <li><strong>Adresse :</strong> <?= htmlspecialchars($user['adresse']) ?></li>
        <li>
            <strong>Profil :</strong>
            <span class="badge <?= $user['profil']==='admin'?'bg-danger':'bg-primary'?>">
                <?= htmlspecialchars($user['profil']) ?>
            </span>
        </li>
    </ul>
</div>
