<?php
require_once "../config/bdd.php";

session_start();
if (!isset($_SESSION["connected"]) or $_SESSION["connected"] == false){
    header("Location: formulaire_utilisateur.php");
    exit();
}

if (isset($_SESSION['error_message'])){
    echo $_SESSION['error_message'];
    unset($_SESSION['error_message']);      
}

if (isset($_SESSION['message_success'])){
    echo $_SESSION['message_success'];
    unset($_SESSION['message_success']);
}

$conn = sql_connect();
$action = $_GET['action'] ?? null;
$zero_resultats = "pas des resultats";
?>
<a href="index.php">retourner a la page d'acceuil</a>
<br>
<a href="profile.php">annuler</a>

<h1>Liste</h1>

<form method="get">
    <button name='action' value="">Toutes cartes grises</button>
    <button name='action' value="a">Carte grise par temps</button>
    <button name='action' value="b">Carte grise par proprietaire</button>
    <button name='action' value="c">Carte grise par plaque</button>
    <button name='action' value="d">par marques les plus immatricules</button>
    <button name='action' value="e">vehicules > X and et emmision > Y</button>
</form>

<?php if ($action == null): ?>
<h4>toutes cartes grises</h4>
<?php
$stmt=$conn->prepare("SELECT c.id_carte_grise, c.numero_carte_grise, i.numero_immatriculation, p.nom, p.prenom FROM carte_grise c
                      JOIN proprietaire p ON p.id_proprietaire = c.id_proprietaire
                      JOIN immatriculation i ON i.id_immatriculation = c.id_immatriculation
                      ORDER BY c.id_carte_grise DESC");
$stmt->execute();
$resultats = $stmt->fetchAll();

foreach ($resultats as $carte){
    echo "<p>
        <a href='../pages/carte_grise.php?id=".$carte['id_carte_grise']."'>".
        $carte['numero_carte_grise']."</a> – ".$carte['numero_immatriculation']." – ".
        $carte['nom']." ".$carte['prenom']."</p>";
}

?>
<?php endif; ?>

<?php if ($action == 'a'): ?>
<h4>cartes par laps des tempss</h4>
<p>(a. Lister les cartes grises par laps de temps.)</p>

<form method="get">
    <input value="a" name="action" hidden>
    <label>Du</label> :
    <input type="date" name="debut" required>
    <label>Au</label> :
    <input type="date" name='fin' required>
    <button type="submit">rechercher</button>
</form>
<?php
    if (isset($_GET['debut']) and isset($_GET['fin'])){
        $stmt = $conn->prepare('SELECT c.id_carte_grise, c.numero_carte_grise, c.date_delivrance, p.nom, p.prenom
                                FROM carte_grise c 
                                JOIN proprietaire p ON c.id_proprietaire = p.id_proprietaire
                                WHERE c.date_delivrance > :debut AND c.date_delivrance < :fin');
        $stmt->execute([":debut" => $_GET['debut'], ":fin" => $_GET['fin']]);
        $resultats = $stmt->fetchAll();

        if (count($resultats) > 0){
            foreach ($resultats as $carte){
                echo "<p><a href='../pages/carte_grise.php?id=".
                $carte['id_carte_grise']."'>".$carte['numero_carte_grise']."</a> ".
                $carte['nom']." ".$carte['prenom']." - ".
                $carte['date_delivrance']."</p>";
            }
        } else echo $zero_resultats;
    }

?>
<?php endif; ?>

<?php if ($action == 'b'): ?>
<h4>recherche par proprietaire</h4>
<p> (b. Lister les cartes grises par nom, par prénom (par ordre alphabétique par nom et ensuite par
    prénom), ou encore par une séquence de caractères d’un nom.)</p>

<form method="get">
    <input hidden name="action" value="b">
    <input type="text" name='nom'>
    <button type="submit">rechercher</button>
</form>

<?php
if (!empty($_GET['nom'])){
    $stmt = $conn->prepare("SELECT c.id_carte_grise, c.numero_carte_grise, p.nom, p.prenom
                            FROM carte_grise c
                            JOIN proprietaire p ON c.id_proprietaire = p.id_proprietaire
                            WHERE nom LIKE :nom
                            ORDER BY p.nom, p.prenom");
    $stmt->execute([':nom' => '%'.$_GET['nom'].'%']);
    $resultats = $stmt->fetchAll();

    if (count($resultats) > 0){
        foreach ($resultats as $carte){
            echo "<p><a href='../pages/carte_grise.php?id=".
                $carte['id_carte_grise']."'>".$carte['numero_carte_grise']."</a> ".
                $carte['nom']." ".$carte['prenom']."</p>";
        }
    } else echo $zero_resultats;

}
?>
<?php endif; ?>

<?php if ($action == 'c'): ?>
<h4>recherche par plaque</h4>
<p>(c. Lister par numéro de plaque (ex. plaque commençant par BE ou plaque se terminant par AC ou
    plaque dont les chiffres varient entre 20 et 30 ou par la combinaison de ces valeurs))</p>

<form method="get">
    <input hidden name="action" value="c">
    <input type="text" name="plaque">
    <button>Rechercher</button>
</form>

<?php
if (!empty($_GET['plaque'])){
    $stmt = $conn->prepare("SELECT i.numero_immatriculation, c.numero_carte_grise, c.id_carte_grise
                            FROM carte_grise c
                            JOIN immatriculation i ON c.id_immatriculation = i.id_immatriculation
                            WHERE i.numero_immatriculation LIKE :p
                            ORDER BY i.numero_immatriculation DESC
    ");
    $stmt->execute([':p' => '%'.$_GET['plaque'].'%']);
    $resultats = $stmt->fetchAll();

    if (count($resultats) > 0){
        foreach ($resultats as $carte){
            echo "<p><a href='../pages/carte_grise.php?id=".
                $carte['id_carte_grise']."'>".$carte['numero_carte_grise']."</a> ".
                $carte['numero_immatriculation']."</p>";
        }
    }
}
?>
<?php endif; ?>

<?php if ($action == 'd'): ?>
<h4>Marques les plus immatricules</h4>
<p>(d. Lister par ordre décroissant les marques qui ont le plus de véhicules immatriculés.)</p>
<?php
$stmt = $conn->prepare("SELECT f.nom as marque, COUNT(*) as nombre
                            FROM carte_grise c 
                            JOIN vehicule v ON c.id_vehicule = v.id_vehicule
                            JOIN fabricant f ON f.id_fabricant = v.id_fabricant
                            GROUP BY f.id_fabricant, marque
                            ORDER BY nombre DESC
    "); 
$stmt->execute();
$resultats = $stmt->fetchAll();

if (count($resultats)>0){
    foreach($resultats as $r){
        echo "<p>".$r['marque']." - ".$r['nombre']." vehicules</p>";
    }
} else{
    echo "aucun vehicule";
}
?>
<?php endif; ?>

<?php if ($action == 'e'): ?>
<h4>vehicules anciens et pollutans</h4>
<p>(e. Lister le nombre de véhicules qui ont plus de X années et les combiner avec ceux qui ont un
    critère d’émission V1 > à Y)</p>

<form method="get">
    <input hidden name="action" value="e">
    <input type="number" name='x' placeholder="age (annees)">
    <input type="number" name='y' placeholder="co2 minimuim">
    <button type="submit">rechercher</button>
</form>

<?php
if (isset($_GET['x']) and isset($_GET['y'])){
    $stmt = $conn->prepare("SELECT COUNT(*) FROM vehicule
                            WHERE (YEAR(CURDATE()) - annee_fabrication) > :x
                            AND emission_co2 > :y");
    $stmt->execute(['x' => $_GET['x'], 'y' => $_GET['y']]);
    $resultats = $stmt->fetch();
    echo $resultats[0];
}
?>

<?php endif; ?>