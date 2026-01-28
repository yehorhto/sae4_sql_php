<?php

// include("../config/bdd.php");
require_once "../includes/utils.php";

// $conn = sql_connect();

// $file = fopen('manufacturers.csv', 'r');
$adresses = fopen('adresses.csv', 'r');
$names = fopen('first-names.csv', 'r');

while (($data = fgetcsv($adresses,count(file('adresses.csv')), ',', "\\",'\\')) !== false){

$adresse = $data[1];
$i = 1;
$ran = mt_rand(0,count(file("first-names.csv")));
while ( !feof($names)){
    $content = fgets($names,4096);
    if ($ran == $i){
    
        $name = $content;
    }
    $i++;
}

echo $name.'-'.$adresse."\n";

// $code = nouveau_code_fabricant($nom);

// $stmt = $conn->prepare("INSERT INTO fabricant (code_fabricant, nom) VALUES (:code_fabricant, :nom)");
// $stmt->execute([":code_fabricant" => $code, ":nom" => $nom]);
}
// echo $file2[0][0];

// echo count(file("adresses.csv"))

?>