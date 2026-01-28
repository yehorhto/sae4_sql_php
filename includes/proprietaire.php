<?php
require_once "../config/bdd.php";

function gerer_proprietaire($conn, $post){
    if ($post['mode-proprietaire'] == 'existant') {
        return trouver_proprietaire_existant($conn, $post['id_proprietaire']);
    } elseif ($post['mode-proprietaire'] == 'nouveau') {
        return creer_nouveau_proprietaire($conn, $post);
    }
    throw new Exception("mode proprietaire invalide");
}

function trouver_proprietaire_existant($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM proprietaire WHERE id_proprietaire = :id");
    $stmt->execute([":id" => $id]);
    $proprietaire = $stmt->fetchAll();
    
    if (count($proprietaire) == 0) {
        throw new Exception("proprietaire n'existe pas");
    }
    
    return $proprietaire[0]["id_proprietaire"];
}

function creer_nouveau_proprietaire($conn, $post) {
    valider_proprietaire_champs($post);
    verifier_proprietaire_duplicate($conn, $post);
    
    $stmt = $conn->prepare("INSERT INTO proprietaire (nom, prenom, adresse) VALUES (:nom, :prenom, :adresse)");
    $stmt->execute([
        ':nom' => $post["nom"],
        ':prenom' => $post["prenom"],
        ':adresse' => $post["adresse"]
    ]);
    
    return $conn->lastInsertId();
}

function valider_proprietaire_champs($post) {
    if (empty($post['nom']) || empty($post['prenom']) || empty($post['adresse'])) {
        throw new Exception('toutes les champs sont obligatoires');
    }
}

function verifier_proprietaire_duplicate($conn, $post) {
    $stmt = $conn->prepare("SELECT * FROM proprietaire WHERE nom = :nom AND prenom = :prenom AND adresse = :adresse");
    $stmt->execute([
        ":nom" => $post["nom"],
        ":prenom" => $post["prenom"],
        ":adresse" => $post["adresse"]
    ]);
    
    if (count($stmt->fetchAll()) > 0) {
        throw new Exception("utilisateur existe deja");
    }
}