<?php
include("inc_session.php") ;
require_once("inc_guillemets.php");
/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/
$_SESSION["filtres"]["messagerieCNF"]["annee"] = $_POST["annee"] ;
$_SESSION["filtres"]["messagerieCNF"]["groupe"] = $_POST["groupe"] ;
header("Location: /messagerie_cnf/index.php") ;
?>
