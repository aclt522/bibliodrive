<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Formulaire d'inscription</title>
</head>
<body>
    <h2>Ajouter une personne</h2>

    <form action="formulaire.php" method="post">
        <label>Nom :</label><br>
        <input type="text" name="nom" required><br><br>

        <label>Prénom :</label><br>
        <input type="text" name="prenom" required><br><br>

        <label>Adresse :</label><br>
        <textarea name="adresse" required></textarea><br><br>

        <input name= "OK" type="submit">Enregistrer</button>
    </form>

</body>
</html>

<?php


 if(isset($_POST['OK'])) {

require_once('connexion.php');


    $stmt = $connexion->prepare("INSERT INTO utilisateur (nom, prenom, mel, mot_de_passe) VALUES (:nom, :prenom, :mel, :mot_de_passe)");
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $adresse = $_POST['adresse'];



    $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
    $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
    $stmt->bindValue(':adresse', $adresse, PDO::PARAM_STR);
   


    if ($connexion->connect_error) {
        die("Erreur de connexion : " . $connexion->connect_error);
    }

    
    $sql = "INSERT INTO personnes (nom, prenom, adresse) VALUES (?, ?, ?)";
    $stmt = $connexion->prepare($sql);
    $stmt->bind_param("sss", $nom, $prenom, $adresse);

   
    if ($stmt->execute()) {
        echo "Enregistrement réussi !";
    } else {
        echo "Erreur lors de l'enregistrement : " . $stmt->error;
    }

    $stmt->close();
    $connexion->close();
 }
    ?>