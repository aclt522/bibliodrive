 <?php

require_once('connexion.php');

$stmt = $connexion->prepare("SELECT * FROMutilisateur where nom=:nom AND prenom=:prenom");

$nom = "Martin";

$prenom = "Jacques";

 

$stmt->bindValue(":nom", $nom); // pas de troisième paramètre STR par défaut

$stmt->bindValue(":prenom", $prenom); // idem

$stmt->setFetchMode(PDO::FETCH_OBJ);

// Les résultats retournés par la requête seront traités en 'mode' objet

 

$stmt->execute();

$enregistrement = $stmt->fetch(); // boucle while inutile

echo '<h1>', $enregistrement->nom, ' ', $enregistrement->prenom,' ', $enregistrement->mot_de_passe,'</h1>';

?>