<?php
// Connexion au serveur
try {
  $dns = 'mysql:host=localhost;dbname=livres;charset=utf8'; 
  $utilisateur = 'root';
  $motDePasse = '';

  $connexion = new PDO($dns, $utilisateur, $motDePasse);
  $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (Exception $e) {
  echo "Connexion à MySQL impossible : ", $e->getMessage();
  die();
}
?>
