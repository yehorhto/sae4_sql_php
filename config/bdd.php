<?php
function sql_connect()
{
    $dbhost = "";
    $dbname = "";
    $dbuser = "";
    $passwd = "";
    

    try {
        $sch = "mysql:host=".$dbhost.";dbname=" . $dbname;
        $bdd = new PDO($sch, $dbuser, $passwd);
    } catch (Exception $e) {
        die("Error : " . $e->getMessage());
    }
    return $bdd;

}



?>