<?php
session_start();
include("../config/bdd.php");

if (!isset($_SESSION["connected"]) or $_SESSION["connected"] == false){
    header("Location: formulaire_utilisateur.php");
    exit();
}

if (!isset($_SESSION['modification_avant']) || !isset($_SESSION['modification_apres'])){
    $_SESSION['error_message'] = "Données de modification non disponibles";
    header("Location: liste_carte_grise.php");
    exit();
}

$avant = $_SESSION['modification_avant'];
$apres = $_SESSION['modification_apres'];
$carte_id = $_GET['id'] ?? null;

unset($_SESSION['modification_avant']);
unset($_SESSION['modification_apres']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifications</title>
</head>

<body>
    <h1>Modifications effectuées - Carte Grise n° <?= $avant['numero_carte_grise'] ?></h1>

    <?php if (isset($_SESSION['message_success'])): ?>
    <div>
        <?php 
        echo $_SESSION['message_success'];
        unset($_SESSION['message_success']); 
        ?>
    </div>
    <?php endif; ?>

    <h2>AVANT et APRES modifications</h2>

    <div>
        <div>
            <h2>AVANT</h2>

            <h3>Propriétaire</h3>
            <p>Nom : <?= htmlspecialchars($avant['nom']) ?></p>
            <p>Prénom : <?= htmlspecialchars($avant['prenom']) ?></p>
            <p>Adresse : <?= htmlspecialchars($avant['adresse']) ?></p>

            <h3>Carte Grise</h3>
            <p>Numéro : <?= $avant['numero_carte_grise'] ?></p>
            <p>Conducteur propriétaire : <?= $avant['conducteur_proprietaire'] ? 'Oui' : 'Non' ?></p>
            <p>Date délivrance : <?= $avant['date_delivrance'] ?></p>
            <p>Date fin : <?= $avant['date_fin'] ?: 'Non définie' ?></p>
        </div>

        <div>
            <h2>APRÈS</h2>

            <h3>Propriétaire</h3>
            <p>
                Nom : <?= htmlspecialchars($apres['nom']) ?>
            </p>
            <p>
                Prénom : <?= htmlspecialchars($apres['prenom']) ?>
            </p>
            <p>
                Adresse : <?= htmlspecialchars($apres['adresse']) ?>
            </p>

            <h3>Carte Grise</h3>
            <p>Numéro : <?= $apres['numero_carte_grise'] ?></p>
            <p>
                Conducteur propriétaire : <?= $apres['conducteur_proprietaire'] ? 'Oui' : 'Non' ?>
            </p>
            <p>
                Date délivrance : <?= $apres['date_delivrance'] ?>
            </p>
            <p>
                Date fin : <?= $apres['date_fin'] ?: 'Non définie' ?>
            </p>
        </div>
    </div>

    <h3>Véhicule (non modifiable)</h3>
    <p>Fabricant : <?= htmlspecialchars($apres['fabricant']) ?></p>
    <p>Modèle : <?= htmlspecialchars($apres['modele']) ?></p>
    <p>Type : <?= htmlspecialchars($apres['type']) ?></p>

    <a href="carte_grise.php?id=<?= $carte_id ?>">Retour</a> |
    <a href="liste_carte_grise.php">Liste des cartes grises</a>

</body>

</html>