<?php
require_once 'generateurs.php';

function creer_carte_grise($conn, $post, $proprietaire_id, $vehicule_id, $immatriculation_id) {
    valider_carte_grise_champs($post);
    
    $annee_delivrance = substr($post['date-delivrance'], 0, 4);
    $numero_carte_grise = generer_numero_carte_grise($conn, $annee_delivrance);
    
    $stmt = $conn->prepare("INSERT INTO carte_grise (
        id_proprietaire, id_vehicule, id_immatriculation, numero_carte_grise,
        conducteur_proprietaire, date_delivrance, date_fin
    ) VALUES (
        :id_proprietaire, :id_vehicule, :id_immatriculation, :numero_carte_grise,
        :conducteur_proprietaire, :date_delivrance, :date_fin
    )");
    
    $stmt->execute([
        ':id_proprietaire' => $proprietaire_id,
        ':id_vehicule' => $vehicule_id,
        ':id_immatriculation' => $immatriculation_id,
        ':numero_carte_grise' => $numero_carte_grise,
        ':conducteur_proprietaire' => $post['conducteur_proprietaire'],
        ':date_delivrance' => $post['date-delivrance'],
        ':date_fin' => $post['date-fin']
    ]);
    
    return $conn->lastInsertId();
}

function modifier_carte_grise($conn, $post, $carte_grise_id, $proprietaire_id, $vehicule_id, $immatriculation_id) {
    valider_carte_grise_champs($post);
    
    $stmt = $conn->prepare("UPDATE carte_grise SET
        id_proprietaire = :id_proprietaire,
        id_vehicule = :id_vehicule,
        id_immatriculation = :id_immatriculation,
        conducteur_proprietaire = :conducteur_proprietaire,
        date_delivrance = :date_delivrance,
        date_fin = :date_fin
        WHERE id_carte_grise = :carte_grise_id
    ");
    
    $stmt->execute([
        ':id_proprietaire' => $proprietaire_id,
        ':id_vehicule' => $vehicule_id,
        ':id_immatriculation' => $immatriculation_id,
        ':conducteur_proprietaire' => $post['conducteur_proprietaire'],
        ':date_delivrance' => $post['date-delivrance'],
        ':date_fin' => $post['date-fin'],
        ':carte_grise_id' => $carte_grise_id
    ]);
    
    return $carte_grise_id;
}



function valider_carte_grise_champs($post) {
    if (empty($post['conducteur_proprietaire']) || 
        empty($post['date-delivrance']) || 
        empty($post['date-fin'])) {
        throw new Exception('Tous les champs sont obligatoires');
    }
}
?>