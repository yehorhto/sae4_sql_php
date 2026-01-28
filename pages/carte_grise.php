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
if (isset($_SESSION['message_success'])){
    echo $_SESSION['message_success'];
    unset($_SESSION['message_success']);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte grise</title>

</head>

<body>
    <a href="liste_carte_grise.php">Retouner a la liste</a>
    <h1>Carte grise n <?= $carte['numero_carte_grise'] ?></h1>

    <h2>PRoprietaire</h2>
    <p>Nom : <?= $carte['nom']?></p>
    <p>Prenom : <?= $carte['prenom'] ?></p>
    <p>Adresse : <?= $carte['adresse'] ?></p>

    <h2>Vehicule</h2>
    <p><?= $carte['fabricant']. " ". $carte['modele'] ?></p>
    <h4>Infos suplementaires</h4>
    <p>Type : <?= $carte['type'] ?></p>
    <p>Categorie : <?= $carte['categorie'] ?></p>
    <p>Classe environmentale : <?= $carte['classe_env'] ?></p>

    <a href="modifier_carte_grise.php?id=<?= $carte['id_carte_grise'] ?>"> Modifier</a>

    <form action="../process/delete_carte_grise.php" method="post"
        onsubmit="return confirm('vous voulez vraiment supprimer cette carte grise?')">
        <input hidden name="id" value="<?= $carte['id_carte_grise'] ?>">
        <button type="submit">Supprimer</button>
    </form>


</body>

</html>