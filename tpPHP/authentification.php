 <!DOCTYPE html>

<html>

<body>

<?php
if (!isset($_POST['btnSeConnecter'])) {
    echo '

    <form action="authentification.php" method = "post" ">

        nom: <input name="nom" type="text" size ="30"">

        code: <input name="code" type="text" size ="30"">

        <input type="submit" name="btnSeConnecter"  value="Se connecter">

    </form>';

} else

{
    require_once 'connexion.php';
    $nom = $_POST['nom'];
    $code = $_POST['code'];
    $stmt = $connexion->prepare("SELECT * FROM agent where nom=:nom AND code=:code");

    $stmt->bindValue(":nom", $nom); 
    $stmt->bindValue(":code", $code);

    $stmt->setFetchMode(PDO::FETCH_OBJ);

    $stmt->execute();

    $enregistrement = $stmt->fetch(); 

    if ($enregistrement) {
        echo '<h1>Connexion réussie !</h1>';
        echo '<h1>bienvenue ' . $enregistrement->prenom;

    } else { 
        echo "Echec à la connexion.";

    }

}
?>
</body>
</html>