<?php
session_start();
include("../config/bdd.php");
include("../includes/carte_grise_modele.php");
error_reporting(E_ALL);

if (!isset($_SESSION["connected"]) or $_SESSION["connected"] == false){
    header("Location: ../pages/formulaire_utilisateur.php");
    exit();
}

if (!isset($_POST['id_carte_grise']) || empty($_POST['id_carte_grise'])){
    $_SESSION['error_message'] = "ID de carte grise manquant";
    header("Location: ../pages/liste_carte_grise.php");
    exit();
}

$conn = sql_connect();
$_SESSION['formulaire_data'] = $_POST;

try {
    // prendre le carte d'avant pour comparer
    $carte_avant = obtenir_carte_grise_par_id($conn, $_POST['id_carte_grise']);
    
    if (!$carte_avant) {
        throw new Exception("Carte grise introuvable");
    }
    
    // verifier les champs
    if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['adresse'])) {
        throw new Exception('Nom, prénom et adresse sont obligatoires');
    }
    
    if (empty($_POST['conducteur_proprietaire']) || empty($_POST['date-delivrance'])) {
        throw new Exception('Tous les champs de la carte grise sont obligatoires');
    }
    
    // UPDATE des infos de proprietaire
    $stmt = $conn->prepare("UPDATE proprietaire SET 
        nom = :nom,
        prenom = :prenom,
        adresse = :adresse
        WHERE id_proprietaire = :id_proprietaire
    ");
    
    $stmt->execute([
        ':nom' => $_POST['nom'],
        ':prenom' => $_POST['prenom'],
        ':adresse' => $_POST['adresse'],
        ':id_proprietaire' => $carte_avant['id_proprietaire']
    ]);
    
    // UPDATE des infos de carte grise
    $stmt = $conn->prepare("UPDATE carte_grise SET
        conducteur_proprietaire = :conducteur_proprietaire,
        date_delivrance = :date_delivrance,
        date_fin = :date_fin
        WHERE id_carte_grise = :id_carte_grise
    ");
    
    $stmt->execute([
        ':conducteur_proprietaire' => $_POST['conducteur_proprietaire'],
        ':date_delivrance' => $_POST['date-delivrance'],
        ':date_fin' => $_POST['date-fin'],
        ':id_carte_grise' => $_POST['id_carte_grise']
    ]);
    
    // prendre la carte d'apres la modification pour comparer
    $carte_apres = obtenir_carte_grise_par_id($conn, $_POST['id_carte_grise']);
    
    // on stock les deux cartes dans session pour comparer 
    $_SESSION['modification_avant'] = $carte_avant;
    $_SESSION['modification_apres'] = $carte_apres;
    
    unset($_SESSION['formulaire_data']);
    $_SESSION['message_success'] = 'Carte grise modifiée avec succès';
    header('Location: ../pages/afficher_modifications_carte_grise.php?id=' . $_POST['id_carte_grise']);
    exit();
    
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header("Location: ../pages/modifier_carte_grise.php?id=" . $_POST['id_carte_grise']);
    exit();
}

?>