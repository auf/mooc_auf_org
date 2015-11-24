<?php
include("inc_session.php") ;
require_once("inc_guillemets.php");
/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/
$_SESSION["filtres"]["formations"]["annee"] = $_POST["formation_annee"] ;
$_SESSION["filtres"]["formations"]["groupe"] = $_POST["formation_groupe"] ;
$_SESSION["filtres"]["formations"]["niveau"] = $_POST["formation_niveau"] ;
$_SESSION["filtres"]["formations"]["ref_institution"] = $_POST["formation_ref_institution"] ;
$_SESSION["filtres"]["formations"]["ref_discipline"] = $_POST["formation_ref_discipline"] ;
$_SESSION["filtres"]["formations"]["intitule"] = $_POST["formation_intitule"] ;
header("Location: index.php") ;
?>
