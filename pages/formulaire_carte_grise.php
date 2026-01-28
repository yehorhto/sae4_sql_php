<?php
session_start();
include("../config/bdd.php");

if (!isset($_SESSION["connected"]) or $_SESSION["connected"] == false){
    header("Location: formulaire_utilisateur.php");
    exit();
}

if (isset($_SESSION['error_message'])){
    echo $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['formulaire_data'])){
    $formulaire_data = $_SESSION['formulaire_data'];
    unset($_SESSION['formulaire_data']);
}else{
    $formulaire_data = [];
}

$conn = sql_connect();



$stmt = $conn->prepare("SELECT id_proprietaire, nom, prenom, adresse FROM proprietaire ORDER BY nom, prenom");
$stmt->execute();
$proprietaires = $stmt->fetchAll();


$stmt = $conn->prepare("SELECT id_vehicule, numero_serie from vehicule ORDER BY numero_serie");
$stmt->execute();
$vehicules = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT id_fabricant, nom FROM fabricant");
$stmt->execute();
$fabricants = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT id_type, libelle FROM type_vehicule");
$stmt->execute();
$types_vehicules = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT m.id_modele, m.nom as modele_nom, f.nom as fabricant_nom FROM modele m
                        JOIN fabricant f on m.id_fabricant = f.id_fabricant 
                        GROUP BY f.nom
                        ORDER BY m.nom DESC");
$stmt->execute();
$modeles = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT id_categorie, code FROM categorie");
$stmt->execute();
$categories = $stmt->fetchAll();

$stmt = $conn->prepare("SELECT id_classe_env, classe FROM classe_env");
$stmt->execute();
$classes_env = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Carte Grise </title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <a href="index.php">retourner a la page d'acceuil</a>
    <br>
    <a href="profile.php">annuler</a>

    <form action="../process/inserer_carte_grise.php" method="post">
        <!-- ------------------------- PROPRIETAIRE ------------------------- -->
        <fieldset>

            <legend>Proprietaire</legend>

            <div class="proprietaire-toggle">


                <!-- ------------------------- MODE PROPRIETAIRE ------------------------- -->
                <!-- mode existant -->
                <label>Existant</label> :
                <input type="radio" name="mode-proprietaire" value="existant" <?= 
                    (isset($formulaire_data['mode-proprietaire']) and 
                    $formulaire_data['mode-proprietaire']=='existant') 
                    ? 'checked' : '' ?>>

                <!-- mode nouveau -->
                <label>Nouveau</label> :
                <input type="radio" name="mode-proprietaire" value="nouveau" <?= 
                    (!isset($formulaire_data['mode-proprietaire']) or 
                    $formulaire_data['mode-proprietaire']=='nouveau') 
                    ? 'checked' : '' ?>>


                <!-- ------------------------- PROPRIETAIRE EXISTANT ------------------------- -->
                <div id="proprietaire-existant">

                    <label>Choisissez le proprietaire</label><br>

                    <select name="id_proprietaire">
                        <option value="null">Choisissez le proprietaire</option>
                        <?php foreach ($proprietaires as $p): ?>
                        <option value="<?= $p['id_proprietaire'] ?>" <?= 
                            (isset($formulaire_data["id_proprietaire"]) and 
                            $formulaire_data['id_proprietaire'] == $p['id_proprietaire']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nom'].' '.$p['prenom'].' — '.$p['adresse']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <!-- ------------------------- PROPRIETAIRE NOUVEAU ------------------------- -->
                <div id='proprietaire-nouveau'>

                    <label>Nouveau Proprietaire</label><br>

                    <label for="nom">Nom</label> :
                    <input type="text" name="nom" value="<?=
                        isset($formulaire_data['nom']) ? $formulaire_data['nom'] : '' ?>" />
                    <br>

                    <label for="prenom">Prenom</label> :
                    <input type="text" name="prenom" value="<?= 
                        isset($formulaire_data['prenom']) ? $formulaire_data['prenom'] : '' ?>" />
                    <br>

                    <label for="adresse">Adresse</label> :
                    <input type="text" name="adresse" value="<?= 
                        isset($formulaire_data['adresse']) ? $formulaire_data['adresse'] : '' ?>" />
                    <br>

                </div>
            </div>

        </fieldset>


        <!-- ------------------------- VEHICULE ------------------------- -->
        <fieldset>

            <legend>Vehicule</legend>

            <div class="vehicule-toggle">

                <!-- mode existant -->
                <label>Existant</label> :
                <input type="radio" name="mode-vehicule" value="existant" <?= 
                    (isset($formulaire_data['mode-vehicule']) and 
                    $formulaire_data['mode-vehicule']=='existant') 
                    ? 'checked' : '' ?>>

                <!-- mode nouveau -->
                <label>Nouveau</label> :
                <input type="radio" name="mode-vehicule" value="nouveau" <?= 
                    (!isset($formulaire_data['mode-vehicule']) or 
                    $formulaire_data['mode-vehicule']=='nouveau') 
                    ? 'checked' : '' ?>>


                <!-- ------------------------- VEHICULE EXISTANT ------------------------- -->

                <div id="vehicule-existant">
                    <label>Choisissez le vehicule</label>

                    <label for="id_vehicule">Numero serie</label> :
                    <select name="id_vehicule">
                        <option value="">Choisissez le vehicule</option>
                        <?php foreach ($vehicules as $v): ?>
                        <option value="<?= $v["id_vehicule"] ?>" <?= 
                            (isset($formulaire_data["numero_serie"]) and
                            $formulaire_data['id_vehicule'] == $v['id_vehicule']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($v['numero_serie']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                </div>


                <!-- ------------------------- VEHICULE NOUVEAU ------------------------- -->

                <div id='vehicule-nouveau'>
                    <label>Nouveau vehicule</label>

                    <label for="fabricant_nom">Fabricant</label> :
                    <input list="liste_fabricants" name="fabricant_nom"
                        value="<?= htmlspecialchars($formulaire_data['fabricant_nom'] ?? '') ?>">
                    <datalist id="liste_fabricants">
                        <?php foreach ($fabricants as $fabr): ?>
                        <option value="<?= htmlspecialchars($fabr['nom']) ?>">
                            <?php endforeach; ?>
                    </datalist>
                    <br>

                    <label for="modele_nom">Modele</label> :
                    <input list="liste_modeles" name="modele_nom"
                        value="<?= htmlspecialchars($formulaire_data['modele_nom'] ?? '') ?>">
                    <datalist id="liste_modeles">
                        <?php foreach ($modeles as $m): ?>
                        <option value="<?= htmlspecialchars($m['modele_nom'])?>">
                            <?php endforeach; ?>
                    </datalist>
                    <br>

                    <label for="type">Type</label> :
                    <select name="type"><br>
                        <option value="null">Choisissez le type de vehicule</option>
                        <?php foreach ($types_vehicules as $type): ?>
                        <option value="<?= $type["id_type"] ?>" <?= 
                            (isset($formulaire_data["type"]) and
                            $formulaire_data['type'] == $type['id_type']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['libelle']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <br>

                    <label for="categorie">categorie</label> :
                    <select name="categorie"><br>
                        <option value="null">Choisissez le categorie</option>
                        <?php foreach ($categories as $categorie): ?>
                        <option value="<?= $categorie["id_categorie"] ?>" <?= 
                            (isset($formulaire_data["categorie"]) and
                            $formulaire_data['categorie'] == $categorie['id_categorie']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categorie['code']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <br>

                    <label for="classe_env">classe_env</label> :
                    <select name="classe_env"><br>
                        <option value="null">Choisissez le classe environmentale</option>
                        <?php foreach ($classes_env as $classe): ?>
                        <option value="<?= $classe["id_classe_env"] ?>" <?= 
                            (isset($formulaire_data["classe_env"]) and
                            $formulaire_data['classe_env'] == $classe['id_classe_env']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($classe['classe']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <br>


                    <label for='date-premiere-attribution'>date premiere attribution</label> :
                    <input type="date" name='date-premiere-attribution' value="<?= date("Y-m-d") ?>">
                    <br>


                    <label for="annee_fabrication">annee_fabrication</label> :
                    <input type="number" id="annee_fabrication" name="annee_fabrication"
                        value="<?= 
                        isset($formulaire_data['annee_fabrication']) ? $formulaire_data['annee_fabrication'] : '' ?>" />
                    <br>

                    <label for="mois_fabrication">mois_fabrication</label> :
                    <input type="number" id="mois_fabrication" name="mois_fabrication" min="1" max="12" value="<?= 
                        isset($formulaire_data['mois_fabrication']) ? $formulaire_data['mois_fabrication'] : '' ?>" />
                    <br>

                    <label for="poids">poids</label> :
                    <input type="number" id="poids" name="poids" min="0" value="<?= 
                        isset($formulaire_data['poids']) ? $formulaire_data['poids'] : '' ?>" />
                    <br>

                    <label for="poids_maximale">poids_maximale</label> :
                    <input type="number" id="poids_maximale" name="poids_maximale" min="0" value="<?= 
                        isset($formulaire_data['poids_maximale']) ? $formulaire_data['poids_maximale'] : '' ?>" />
                    <br>

                    <label for="cylindree_du_motour">cylindree_du_motour</label> :
                    <input type="number" id="cylindree_du_motour" name="cylindree_du_motour" min="0"
                        value="<?= 
                        isset($formulaire_data['cylindree_du_motour']) ? $formulaire_data['cylindree_du_motour'] : '' ?>" />
                    <br>

                    <label for="puissance_chevaux">puissance_chevaux</label> :
                    <input type="number" id="puissance_chevaux" name="puissance_chevaux" min="0"
                        value="<?= 
                        isset($formulaire_data['puissance_chevaux']) ? $formulaire_data['puissance_chevaux'] : '' ?>" />
                    <br>

                    <label for="puissance_cv">puissance_cv</label> :
                    <input type="number" id="puissance_cv" name="puissance_cv" min="0" value="<?= 
                        isset($formulaire_data['puissance_cv']) ? $formulaire_data['puissance_cv'] : '' ?>" />
                    <br>

                    <label for="nb_places_assises">nb_places_assises</label> :
                    <input type="number" id="nb_places_assises" name="nb_places_assises" min="0"
                        value="<?= 
                        isset($formulaire_data['nb_places_assises']) ? $formulaire_data['nb_places_assises'] : '' ?>" />
                    <br>

                    <label for="nb_places_debout">nb_places_debout</label> :
                    <input type="number" id="nb_places_debout" name="nb_places_debout" min="0" value="<?= 
                        isset($formulaire_data['nb_places_debout']) ? $formulaire_data['nb_places_debout'] : '' ?>" />
                    <br>

                    <label for="son">son</label> :
                    <input type="number" id="son" name="son" min="0" value="<?= 
                        isset($formulaire_data['son']) ? $formulaire_data['son'] : '' ?>" />
                    <br>

                    <label for="vitesse">vitesse</label> :
                    <input type="number" id="vitesse" name="vitesse" min="0" value="<?= 
                        isset($formulaire_data['vitesse']) ? $formulaire_data['vitesse'] : '' ?>" />
                    <br>

                    <label for="emission_co2">emission_co2</label> :
                    <input type="number" id="emission_co2" name="emission_co2" min="0" value="<?= 
                        isset($formulaire_data['emission_co2']) ? $formulaire_data['emission_co2'] : '' ?>" />
                    <br>

                </div>
            </div>


        </fieldset>


        <!-- ------------------------- CARTE CRISE INFOS ------------------------- -->

        <fieldset>
            <legend>Carte Grise</legend>

            <label>Le conducteur propritaire?</label> :
            <label>Oui</label><input type="radio" name="conducteur_proprietaire" value='1' <?= 
                    (!isset($formulaire_data['conducteur_proprietaire']) or 
                    $formulaire_data['conducteur_proprietaire']=='1') 
                    ? 'checked' : '' ?>>
            <label>Non</label><input type="radio" name="conducteur_proprietaire" value='0' <?= 
                    (isset($formulaire_data['conducteur_proprietaire']) and 
                    $formulaire_data['conducteur_proprietaire']=='0') 
                    ? 'checked' : '' ?>>
            <br>


            <label for='date-delivrance'>date delivrance</label> :
            <input type="date" name='date-delivrance'
                value="<?= 
                        isset($formulaire_data['date-delivrance']) ? $formulaire_data['date-delivrance'] : date("Y-m-d") ?>">
            <br>

            <label for="date-fin">date fin</label> :
            <input type="date" name='date-fin' value="<?= 
                        isset($formulaire_data['date-fin']) ? $formulaire_data['date-fin'] : '' ?>">
            <br>


        </fieldset>

        <button type="submit">submit</button>

    </form>
</body>

</html>