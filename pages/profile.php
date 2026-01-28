<?php
session_start();
if (!isset($_SESSION["connected"]) or $_SESSION["connected"] == false){
    header("Location: formulaire_utilisateur.php");
    exit();
}
echo "<h1>Bonjour ".$_SESSION["username"]."</h1>";


echo "<a href='./formulaire_carte_grise.php'>Ajouter nouveau carte grise</a>";
echo "<br>";
echo "<a href='./liste_carte_grise.php'>Lister cartes grises</a>";

?>