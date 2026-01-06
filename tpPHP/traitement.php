 <!DOCTYPE html>

<html lang="fr">

  <head>
      <title>test php</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    
    <table border="2">
    <tr>
        <th>Multiplication</th>
        
    </tr>
    
    <?php
    $n=$_GET["nombre"];
    for ($x = 0; $x <= 10; $x++) {
        
        echo "<tr><td>" . $x . "x" . $n . "=" . ($n*$x) . "</td></tr>";
        
    }
    ?>
</table>

<?php
if (!isset($_POST["btnEnvoyer"])) {
    
    echo '
        <form action="" method="post">
            Nom : <input type="text" name="txtNom"><br>
            Prénom : <input type="text" name="txtPrenom"><br>
            Adresse : <input type="text" name="txtAdresse"><br>
            <input type="submit" name="btnEnvoyer" value="Envoyer">
        </form>
    ';
} 
