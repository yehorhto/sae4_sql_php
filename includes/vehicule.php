<?php
require_once 'generateurs.php';

function gerer_vehicule($conn, $post) {
    if ($post['mode-vehicule'] == 'existant') {
        return trouver_vehicule_existant($conn, $post['id_vehicule']);
    } elseif ($post['mode-vehicule'] == 'nouveau') {
        return creer_nouveau_vehicule($conn, $post);
    }
    throw new Exception("mode vehicule invalide");
}

function trouver_vehicule_existant($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM vehicule WHERE id_vehicule = :id");
    $stmt->execute([":id" => $id]);
    $vehicule = $stmt->fetchAll();
    
    if (count($vehicule) == 0) {
        throw new Exception("vehicule n'existe pas");
    }
    
    return $vehicule[0]["id_vehicule"];
}

function creer_nouveau_vehicule($conn, $post) {
    valider_vehicule_champs($post);
    valider_vehicule_champs_numeriques($post);
    
    $fabricant_data = gerer_fabricant($conn, $post['fabricant_nom']);
    $modele_id = gerer_modele($conn, $post['modele_nom'], $fabricant_data['id']);
    $numero_serie = generer_numero_serie($conn, $fabricant_data['code'], $post['annee_fabrication'], $post['mois_fabrication']);
    
    return inserer_vehicule($conn, $post, $fabricant_data['id'], $modele_id, $numero_serie);
}

function valider_vehicule_champs($post) {
    $required = ['fabricant_nom', 'modele_nom', 'type', 'categorie', 'classe_env', 'date-premiere-attribution'];
    
    foreach ($required as $field) {
        if (!isset($post[$field]) || $post[$field] == 'null') {
            throw new Exception('Tous les champs sont obligatoires');
        }
    }
}

function valider_vehicule_champs_numeriques($post) {
    $valeurs_numeriques = [
        'annee_fabrication' => 'année de fabrication',
        'mois_fabrication' => 'mois de fabrication',
        'poids' => 'poids',
        'poids_maximale' => 'poids maximal',
        'cylindree_du_motour' => 'cylindre du moteur',
        'puissance_chevaux' => 'puissance en chevaux',
        'puissance_cv' => 'puissance CV',
        'nb_places_assises' => 'nombre de places assises',
        'nb_places_debout' => 'nombre de places debout',
        'son' => 'son',
        'vitesse' => 'vitesse',
        'emission_co2' => 'emission CO2'
    ];

    foreach ($valeurs_numeriques as $champ => $libelle) {
        if (!isset($post[$champ]) || $post[$champ] === '') {
            throw new Exception("Le champ '$libelle' est obligatoire");
        }
        
        if (!ctype_digit($post[$champ])) {
            throw new Exception("Le champ '$libelle' doit être un nombre entier");
        }
        
        if ($post[$champ] < 0) {
            throw new Exception("Le champ '$libelle' ne peut pas être négatif");
        }
    }
}

function gerer_fabricant($conn, $fabricant_nom) {
    $stmt = $conn->prepare("SELECT id_fabricant, code_fabricant FROM fabricant WHERE nom = :nom");
    $stmt->execute([':nom' => $fabricant_nom]);
    $fabricant = $stmt->fetchAll();
    
    if (count($fabricant) > 0) {
        return [
            'id' => $fabricant[0]['id_fabricant'],
            'code' => $fabricant[0]['code_fabricant']
        ];
    }
    
    $code = nouveau_code_fabricant($fabricant_nom);
    $stmt = $conn->prepare("INSERT INTO fabricant (nom, code_fabricant) VALUES (:nom, :code_fabricant)");
    $stmt->execute([":nom" => $fabricant_nom, ":code_fabricant" => $code]);
    
    return [
        'id' => $conn->lastInsertId(),
        'code' => $code
    ];
}

function gerer_modele($conn, $modele_nom, $fabricant_id) {
    $stmt = $conn->prepare("SELECT id_modele FROM modele WHERE nom = :nom");
    $stmt->execute([':nom' => $modele_nom]);
    $modele = $stmt->fetchAll();

    if (count($modele) > 0) {
        return $modele[0]['id_modele'];
    }
    
    $stmt = $conn->prepare("INSERT INTO modele (nom, id_fabricant) VALUES (:nom, :id_fabricant)");
    $stmt->execute([":nom" => $modele_nom, ':id_fabricant' => $fabricant_id]);
    
    return $conn->lastInsertId();
}

function inserer_vehicule($conn, $post, $fabricant_id, $modele_id, $numero_serie) {
    $stmt = $conn->prepare("INSERT INTO vehicule (
        numero_serie, id_fabricant, id_modele, id_type, id_categorie, id_classe_env,
        annee_fabrication, mois_fabrication, poids, poids_maximale, cylindree_du_motour,
        puissance_chevaux, puissance_cv, nb_places_assises, nb_places_debout,
        son, vitesse, emission_co2
    ) VALUES (
        :numero_serie, :id_fabricant, :id_modele, :id_type, :id_categorie, :id_classe_env,
        :annee_fabrication, :mois_fabrication, :poids, :poids_maximale, :cylindree_du_motour,
        :puissance_chevaux, :puissance_cv, :nb_places_assises, :nb_places_debout,
        :son, :vitesse, :emission_co2
    )");
    
    $stmt->execute([
        ':numero_serie' => $numero_serie,
        ':id_fabricant' => $fabricant_id,
        ':id_modele' => $modele_id,
        ':id_type' => $post['type'],
        ':id_categorie' => $post['categorie'],
        ':id_classe_env' => $post['classe_env'],
        ':annee_fabrication' => $post['annee_fabrication'],
        ':mois_fabrication' => $post['mois_fabrication'],
        ':poids' => $post['poids'],
        ':poids_maximale' => $post['poids_maximale'],
        ':cylindree_du_motour' => $post['cylindree_du_motour'],
        ':puissance_chevaux' => $post['puissance_chevaux'],
        ':puissance_cv' => $post['puissance_cv'],
        ':nb_places_assises' => $post['nb_places_assises'],
        ':nb_places_debout' => $post['nb_places_debout'],
        ':son' => $post['son'],
        ':vitesse' => $post['vitesse'],
        ':emission_co2' => $post['emission_co2']
    ]);
    
    return $conn->lastInsertId();
}

?>