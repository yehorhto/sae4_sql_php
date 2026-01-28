<?php

session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);


include("../config/bdd.php");
$conn = sql_connect();
if (!isset($_POST['action'])) { // Cas d'erreur, si l'utilisateur a accede cette page
    header("Location: ../pages/formulaire_utilisateur.php");
    exit();
}


$username = $_POST['username']; // identifiant
$passwd = $_POST['passwd']; // mot de passe

if ($username == '' or $passwd == '') {
    $_SESSION["flash_message"] = "Le formulaire est requis.";
    header("Location: ../pages/formulaire_utilisateur.php");
    exit();
}

if ($_POST['action'] == 'register') {



    $stmt = $conn->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->execute([":username" => $username]);
    $user = $stmt->fetchAll();

    if (count($user) > 0) {
        $_SESSION["flash_message"] = $username . " : L'utilisateur existe déjà";
        header("Location: ../pages/formulaire_utilisateur.php");
        exit();
    }
    $hash = password_hash($passwd, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (:username, :passwd, 'A')");
    $stmt->execute([":username" => $username, ":passwd" => $hash]);

    $_SESSION["flash_message"] = "L'utilisateur " . $username . " est enregistré, vous pouvez maintenant vous connecter.";
    header("Location: ../pages/formulaire_utilisateur.php");
    exit();
} elseif ($_POST['action'] == 'login') {

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username;");
    $stmt->execute([":username" => $username]);
    $user = $stmt->fetchAll();
    if (count($user) == 0) {
        $_SESSION["flash_message"] = $username . " : L'utilisateur n'existe pas.";
        header("Location: ../pages/formulaire_utilisateur.php");
        exit();
    } else {
        if (password_verify($passwd, $user[0]["password"])) {
            unset($_SESSION["flash_message"]);
            $_SESSION["connected"] = true;
            $_SESSION["username"] = $username;
            header("Location: ../pages/index.php");
            exit();
        } else {
            $_SESSION["flash_message"] = "wrong passwd";
            header("Location: ../pages/formulaire_utilisateur.php");
            exit();
        }
    }
}

?>