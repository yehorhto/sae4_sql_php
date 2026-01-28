<?php
session_start();
include("../config/bdd.php");
include("../includes/carte_grise_modele.php");

if (!isset($_SESSION["connected"]) or $_SESSION["connected"] == false){
    header("Location: formulaire_utilisateur.php");
    exit();
}

if (!isset($_POST['id']) || empty($_POST['id'])){
    $_SESSION['error_message'] = "ID manquant";
    header("Location: liste_carte_grise.php");
    exit();
}

$conn = sql_connect();
$supprimer = supprimer_carte_grise($conn, $_POST['id']);

if ($supprimer){
    $_SESSION['message_success'] = 'carte grise supprime avec succes';
} else{
    $_SESSION['error_message'] = 'error pendat la suppression de la carte grise';
}
header('Location: ../pages/liste_carte_grise.php');
exit();