<?php
function increment_lettres($lettres){
    return $lettres == "ZZ" ? "AA" : ++$lettres;
}
?>