<?php
require_once "utils.php";

/*
Fonction pour generer un numero unique de carte grise
Format AAAALLNNNNN:
    - AAAA = annee (4 chiffres)
    - LL = lettres (2 caracteres, commence par AA)
    - NNNNN = nombres (5 chiffres, 00000 - 99999)
Pour les arguments on prend la connection a la base des donnees, et l'annee 
*/
function generer_numero_carte_grise($conn, $annee){
    $stmt = $conn->prepare("SELECT numero_carte_grise FROM carte_grise 
                            WHERE numero_carte_grise LIKE :annee
                            ORDER BY numero_carte_grise DESC LIMIT 1");
    
    // changement de :annee par la variable $annee dans la requete, et addition % pour trouver toutes les numeros de cartes grises qui commencnt par l'annee donnee
    $stmt->execute([':annee' => $annee."%"]);
    $dernier = $stmt->fetchColumn();

    // si aucun numero existant n'est trouvee, renvoyer le premier numero pour cette annee
    if (!$dernier) return $annee."AA00000";

    // extraire la partie lettre (4-6 position, 2 caracteres)
    $lettres = substr($dernier, 4, 6);

    // extraire les 5 dernieres caracteres de la derniere carte gris (5 dernieres chiffres)
    $last_num = (int)substr($dernier, -5);

    // imcrementer le numero
    $new_num = $last_num + 1;

    // si le numero depasse 99999, reinitialiser $numbers a 00000 et incrementer les letters 
    if ($new_num > 99999){
        $numbers = "00000";
        increment_lettres($lettres);
    }else{
        $numbers = $new_num;
    }
    
    // renvoyer le numero de carte grise complet
    return $annee.$lettres.$numbers;
}

/*
Fonction pour generer un numero d'immatriculation unique
Format LLNNNLL :
    -LL (gauche) = paire des lettres gauche (AA-ZZ)
    -NNN = numero (10-999)
    -LL (droit) = paire des lettres droit (AA-ZZ)

le numero d'immatriculation s'increment d'abord par les lettres de droite, puis par les chiffres en enfin par les lettres de gauche

Pour les arguments on prend la connection a la base des donnees
*/
function generer_numero_immatriculation($conn){
    // requete pour obtenir le dernier numero d'immatriculation
    $stmt = $conn->prepare("SELECT numero_immatriculation FROM immatriculation ORDER BY numero_immatriculation DESC LIMIT 1");
    $stmt->execute();
    $dernier = $stmt->fetchColumn();

    // si aucun numero existant, renvoyer le premier numero
    if (!$dernier) return "AA10AA";

    // extraire le lettres de gauche (2 premieres caracteres)
    $gauche = substr($dernier,0,2);

    // extraire la partie numerique central (les chiffres peuvent etres de 10 a 999, et pour extrait le bon partie,
    // on soustrait 4 de la taille numero d'immatriculation)
    $num = (int) substr($dernier, 2, strlen($dernier)-4);

    // extraire le lettres de droite (2 dernieres caracteres)
    $droit = substr($dernier, -2);
    
    // incrementer les lettres de droite
    $droit = increment_lettres($droit);

    // si les lettres de droite sont revenues a 'AA', incrementer le numero
    if ($droit == 'AA'){
        $num++;
        // si le numero depasse 999, reinitaliser a 10 et incrementer les lettres de gauche
        if ($num > 999){
            $num = 10;
            $gauche = increment_lettres($gauche);
        }
    }
    
    // renvoyer le numero d'immatriculation complet
    return $gauche.$num.$droit;

    
}

function generer_numero_serie($conn, $fabricant_code, $annee, $mois)
{
    $stmt = $conn->prepare("SELECT numero_serie FROM vehicule WHERE numero_serie LIKE :pattern ORDER BY numero_serie DESC LIMIT 1");
    if ($mois < 10){
        $mois = "0".$mois;        
    }
    $pattern = $fabricant_code . $annee . $mois . '%';
    $stmt->execute([':pattern' => $pattern]);
    $derniere = $stmt->fetchColumn();
    
    if (!$derniere){
        $num = '000000';
    } else {
        $last_num = (int)substr($derniere, -6);
        $next_num = $last_num + 1;
        $num = sprintf("%06d", $next_num);
    }
    return $fabricant_code . $annee . $mois . $num;
}

function nouveau_code_fabricant($nom)
{
    // on convert le nom de fabricant en majuscules et coupe les mots
    $nom = strtoupper($nom);
    // \s signifie l'espace ou \n(new line) ou tabulation
    $mots = preg_split('/\s+/', $nom);

    // si le nom de fabricant est une seul mot on renvoi 3 premieres lettres
    if (count($mots) === 1) {
       return substr($mots[0], 0, 3);
   }

    // si on a plus que 1 mot on prend le premier lettre de chaque      
    $code = '';
    foreach ($mots as $mot) {
        $code .= $mot[0];
    }

    // 3 lettres maximum
    return substr($code, 0, 3);

}
?>