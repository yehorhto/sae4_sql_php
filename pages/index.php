<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Carte Grise</title>
</head>

<body>
    <?php
    if (!isset($_SESSION["connected"]) || $_SESSION["connected"] !== true) {
        echo "u'll have to <li><a href='formulaire_utilisateur.php'>login</a></li> first";
    } else {
        echo "<li><a href='profile.php'>Profile</a></li>";
    }

    ?>
    <h1>Gestion et Stockage des cartes grises</h1>
    <p>Ce site vous permet de créer, stocker et gérer facilement vos cartes grises, documents administratifs et
        informations relatives à vos véhicules. Grâce à une interface simple et sécurisée, vous pouvez avoir un accès
        rapide et centralisé à toutes vos informations importantes, tout en assurant leur conservation à long terme.</p>

    <p>Que vous soyez particulier ou professionnel, ce site est conçu pour simplifier la gestion de vos documents et
        vous offrir un moyen pratique et fiable de suivre l'état administratif de vos véhicules.</p>



</body>

</html>