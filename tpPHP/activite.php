<!DOCTYPE html>
<html lang="fr">
<head>
  <title></title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

 <h2>Formulaire HTML</h2>

<form method="post" action="activite.php">

numéro : <input type="text" name="numero"><br><br>

activité : <input type="text" name="activite"><br><br>

<input type="submit" name="ok" value="Envoyer">

</form>






<?php
 if(isset($_POST['ok'])){
$numero = $_POST['numero'];
$activite = $_POST['activite'];

require_once('connexion.php');
 $stmt = $connexion->prepare("INSERT INTO activite (numero, libelle) VALUES (:numero, :activite)");

 

$stmt->bindValue(':numero', $numero, PDO::PARAM_INT);

$stmt->bindValue(':activite', $activite, PDO::PARAM_STR);



 

$nb_ligne_affectees = $stmt->execute();



echo $nb_ligne_affectees." ligne() insérée(s).<BR>";

 


} 
?>
</body>

</html>   