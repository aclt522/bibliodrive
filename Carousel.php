<?php
require_once('connexion.php');
$stmt = $connexion->prepare("SELECT * FROM livre ORDER BY dateajout DESC LIMIT 3");
$stmt->setFetchMode(PDO::FETCH_OBJ);
$stmt->execute();
$livres = $stmt->fetchAll();
?>

<div class="container my-4">
  <h3 class="text-center couleur1 mb-4">Derniers livres acquis :</h3>

  <div id="demo" class="carousel slide carousel-fade carousel-dark" data-bs-ride="carousel">

    <!-- Indicators -->
    <div class="carousel-indicators">
      <?php for ($i = 0; $i < count($livres); $i++): ?>
        <button type="button" data-bs-target="#demo" data-bs-slide-to="<?= $i ?>" class="<?= $i == 0 ? 'active' : '' ?>"></button>
      <?php endfor; ?>
    </div>

    <!-- Carousel items -->
    <div class="carousel-inner text-center">
      <?php foreach ($livres as $id => $livre): ?>
        <div class="carousel-item <?= $id == 0 ? 'active' : '' ?>">
          <img src="./covers/<?= $livre->photo ?>" 
               class="d-block mx-auto img-fluid" 
               alt="<?= htmlspecialchars($livre->titre) ?>" 
               style="max-height:400px; object-fit:contain;">
          <div class="carousel-caption d-none d-md-block">
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#demo" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#demo" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>

  </div>
</div>
