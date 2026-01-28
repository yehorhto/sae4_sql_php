<?php
require_once '../config/bdd.php';

function obtenir_carte_grise_par_id($conn, $id){
    $stmt = $conn->prepare("SELECT cg.*, 
                   p.nom, p.prenom, p.adresse,
                   v.id_type as vehicule_id_type,
                   v.id_categorie as vehicule_id_categorie,
                   v.id_classe_env as vehicule_classe_env,
                   v.*,
                   f.nom as fabricant,
                   m.nom as modele,
                   t.libelle as type,
                   cat.code as categorie,
                   ce.classe as classe_env
            FROM carte_grise cg
            JOIN proprietaire p ON cg.id_proprietaire = p.id_proprietaire
            JOIN vehicule v ON cg.id_vehicule = v.id_vehicule
            JOIN fabricant f ON v.id_fabricant = f.id_fabricant
            JOIN modele m ON v.id_modele = m.id_modele
            JOIN type_vehicule t ON v.id_type = t.id_type
            JOIN categorie cat ON v.id_categorie = cat.id_categorie
            JOIN classe_env ce ON v.id_classe_env = ce.id_classe_env
            WHERE cg.id_carte_grise = :id");
    
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

function supprimer_carte_grise($conn,$id){
    $stmt = $conn->prepare("DELETE FROM carte_grise WHERE id_carte_grise = :id");
    return $stmt->execute([':id' => $id]);
}
?>