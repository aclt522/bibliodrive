<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('connexion.php');
?>

<nav class="navbar navbar-expand bg-light px-4 py-2">

    <!-- Image logo -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
    <img src="images/logo.jpg" alt="Logo" style="height:40px;">
</a>

    <!-- Liens à gauche -->
    <ul class="navbar-nav">
        <?php if (isset($_SESSION['profil']) && $_SESSION['profil'] === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="page_admin.php">Administration</a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="liste_des_livres.php">Liste des livres</a>
        </li>
    </ul>

    <!-- Partie droite : on sépare recherche et boutons -->
    <div class="ms-auto d-flex align-items-center gap-2">

        <!-- Barre de recherche -->
<div class="d-flex align-items-center">
    <?php
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    ?>
    <form method="GET" action="liste_des_livres.php" class="d-flex" role="search">
        <input 
            class="form-control me-2 rounded-pill" 
            type="search" 
            name="search" 
            placeholder="Rechercher des livres" 
            aria-label="Rechercher"
            value="<?= htmlspecialchars($search) ?>"
            style="width: 200px;"
        >
        <button class="btn btn-outline-primary rounded-pill" type="submit">
            🔍
        </button>
    </form>
</div>

<!-- Panier -->
<?php if (isset($_SESSION['mel'])): ?>
    <a class="btn btn-warning" href="panier.php">
        🛒 Panier
        <?php 
        $nbPanier = isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0;
        echo $nbPanier > 0 ? "($nbPanier)" : "";
        ?>
    </a>
<?php endif; ?>

<!-- Mes emprunts -->
<?php
if (isset($_SESSION['mel'])) {
    $stmt = $connexion->prepare("SELECT COUNT(*) FROM emprunter WHERE mel = :mel AND dateretour >= NOW()");
    $stmt->bindValue(':mel', $_SESSION['mel']);
    $stmt->execute();
    $empruntsEnCours = (int)$stmt->fetchColumn();

    if ($empruntsEnCours > 0): ?>
        <a class="btn btn-info" href="mes_emprunts.php">
            📚 Mes emprunts (<?= $empruntsEnCours ?>)
        </a>
    <?php endif;
}
?>

        <!-- Boutons connexion / profil -->
        <div class="d-flex align-items-center gap-2">
            <?php if (!isset($_SESSION['mel'])): ?>
                <a class="btn btn-success" href="login.php">Connexion</a>
            <?php else: ?>
                <a class="btn btn-danger" href="logout.php">Déconnexion</a>
                <?php include 'infos_utilisateur.php'; ?>
            <?php endif; ?>
        </div>

    </div>

    <!-- Image du château -->
<div class="ms-3 d-none d-lg-flex align-items-center">
    <img src="images/chateau_moulinsart.png"
         alt="Château de Moulinsart"
         style="height:45px; opacity:0.85;">
</div>

</nav>

<!-- Bande d'information -->
<div class="bg-dark text-white text-center py-2">
    La Bibliothèque de Moulinsart est fermée au public jusqu'à nouvel ordre.
    Mais il vous est possible d'emprunter nos livres via notre service Biblio Drive.
</div>