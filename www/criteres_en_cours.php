<?php
include("inc_session.php") ;
require_once("inc_guillemets.php");
/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/
$_SESSION["e_groupe"] = magic_strip($_POST["groupe"]) ;
header("Location: /en_cours.php") ;
?>
