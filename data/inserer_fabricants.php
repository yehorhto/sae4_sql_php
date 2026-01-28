<?php

include("../config/bdd.php");
require_once "../includes/utils.php";

$conn = sql_connect();

$file = fopen('manufacturers.csv', 'r');

while (($data = fgetcsv($file,1000, ',', "\\",'\\')) !== false){

$nom = $data[1];
if ($nom == '') continue;

$code = nouveau_code_fabricant($nom);

$stmt = $conn->prepare("INSERT INTO fabricant (code_fabricant, nom) VALUES (:code_fabricant, :nom)");
$stmt->execute([":code_fabricant" => $code, ":nom" => $nom]);
}

?>