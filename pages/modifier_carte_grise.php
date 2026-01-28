<?php
session_start();
include("../config/bdd.php");
include("../includes/carte_grise_modele.php");

if (!isset($_SESSION["connected"]) or $_SESSION["connected"] == false){
    header("Location: formulaire_utilisateur.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])){
    $_SESSION['error_message'] = "pas d'id";
    header("Location: liste_carte_grise.php");
    exit();
}

$conn = sql_connect();
$carte = obtenir_carte_grise_par_id($conn, $_GET['id']);

if (!$carte){
    $_SESSION['error_message'] = 'carte grise introuvable';
    header("Location: liste_carte_grise.php");
    exit();
}

if (isset($_SESSION['error_message'])){
    echo $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['formulaire_data'])){
    $formulaire_data = $_SESSION['formulaire_data'];
    unset($_SESSION['formulaire_data']);
} else {
    $formulaire_data = $carte;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Carte Grise</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <h1>Modifier la Carte Grise n° <?= $carte['numero_carte_grise'] ?></h1>

    <form action="../process/update_carte_grise.php" method="post">
        <input type="hidden" name="id_carte_grise" value="<?= $carte['id_carte_grise'] ?>">

        <!-- ------------------------- PROPRIETAIRE (modifiable) ------------------------- -->
        <fieldset>
            <legend>Propriétaire</legend>

            <label for="nom">Nom</label> :
            <input type="text" name="nom" value="<?= htmlspecialchars($formulaire_data['nom'] ?? $carte['nom']) ?>"
                required />
            <br>

            <label for="prenom">Prénom</label> :
            <input type="text" name="prenom"
                value="<?= htmlspecialchars($formulaire_data['prenom'] ?? $carte['prenom']) ?>" required />
            <br>

            <label for="adresse">Adresse</label> :
            <input type="text" name="adresse"
                value="<?= htmlspecialchars($formulaire_data['adresse'] ?? $carte['adresse']) ?>" required />
            <br>
        </fieldset>

        <!-- ------------------------- VEHICULE (affichage seulement) ------------------------- -->
        <fieldset>
            <legend>Véhicule (non modifiable)</legend>

            <p>Fabricant: <?= htmlspecialchars($carte['fabricant']) ?></p>
            <p>Modèle: <?= htmlspecialchars($carte['modele']) ?></p>
            <p>Type: <?= htmlspecialchars($carte['type']) ?></p>
            <p>Catégorie: <?= htmlspecialchars($carte['categorie']) ?></p>
            <p>Classe environnementale: <?= htmlspecialchars($carte['classe_env']) ?></p>
            <p>Numéro de série: <?= htmlspecialchars($carte['numero_serie']) ?></p>
        </fieldset>

        <!-- ------------------------- CARTE GRISE INFOS ------------------------- -->
        <fieldset>
            <legend>Informations Carte Grise</legend>

            <p>Numéro carte grise: <?= $carte['numero_carte_grise'] ?> (non modifiable)</p>

            <label>Le conducteur propriétaire?</label> :
            <label>Oui</label><input type="radio" name="conducteur_proprietaire" value='1'
                <?= ($formulaire_data['conducteur_proprietaire'] ?? $carte['conducteur_proprietaire']) == '1' ? 'checked' : '' ?>>
            <label>Non</label><input type="radio" name="conducteur_proprietaire" value='0'
                <?= ($formulaire_data['conducteur_proprietaire'] ?? $carte['conducteur_proprietaire']) == '0' ? 'checked' : '' ?>>
            <br>

            <label for='date-delivrance'>Date délivrance</label> :
            <input type="date" name='date-delivrance'
                value="<?= $formulaire_data['date-delivrance'] ?? $carte['date_delivrance'] ?>" required>
            <br>

            <label for="date-fin">Date fin</label> :
            <input type="date" name='date-fin' value="<?= $formulaire_data['date-fin'] ?? $carte['date_fin'] ?>">
            <br>
        </fieldset>

        <button type="submit">Enregistrer les modifications</button>
        <a href="carte_grise.php?id=<?= $carte['id_carte_grise'] ?>">Annuler</a>
    </form>
</body>

</html>