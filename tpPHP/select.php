 <!DOCTYPE html>

<html lang="fr">

<head>

  <title>Titre de la page</title>

  <meta charset="utf-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body>

  <?php

require_once('connexion.php');

$stmt = $connexion->prepare("SELECT * FROM agent");

$stmt->setFetchMode(PDO::FETCH_OBJ);

$stmt->execute();

while($enregistrement = $stmt->fetch())

{
  echo '<h1>', $enregistrement->civilite, ' ', $enregistrement->nom,' ', $enregistrement->prenom, ' ' $enregistrement->ville,'</h1>';
}
?>

</body>

</html>   