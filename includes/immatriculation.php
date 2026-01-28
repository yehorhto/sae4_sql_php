<?php
require_once 'generateurs.php';

function creer_immatriculation($conn) {
    $numero_immatriculation = generer_numero_immatriculation($conn);
    
    $stmt = $conn->prepare("INSERT INTO immatriculation (numero_immatriculation, date_attribution) VALUES (:numero_immatriculation, :date_attribution)");
    $stmt->execute([
        ':numero_immatriculation' => $numero_immatriculation,
        ':date_attribution' => date("Y/m/d")
    ]);
    
    return $conn->lastInsertId();
}

?>