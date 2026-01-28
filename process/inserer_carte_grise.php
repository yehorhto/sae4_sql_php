<?php
session_start();
include("../config/bdd.php");
include("../includes/creer_carte_grise.php");
include("../includes/immatriculation.php");
include("../includes/proprietaire.php");
include("../includes/vehicule.php");
error_reporting(E_ALL);

$conn = sql_connect();
$_SESSION['formulaire_data'] = $_POST;

try {
    $proprietaire_id = gerer_proprietaire($conn, $_POST);
    $vehicule_id = gerer_vehicule($conn, $_POST);
    $immatriculation_id = creer_immatriculation($conn);
    $carte_grise_id = creer_carte_grise($conn, $_POST, $proprietaire_id, $vehicule_id, $immatriculation_id);
    
    unset($_SESSION['formulaire_data']);
    $_SESSION['message_success'] = 'Carte grise a été créée avec succès';
    header('Location: ../pages/carte_grise.php?id=' . $carte_grise_id);
    exit();
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ../pages/formulaire_carte_grise.php");
    exit();
}

?>