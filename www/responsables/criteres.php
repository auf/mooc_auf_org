<?php
include("inc_session.php") ;
require_once("inc_guillemets.php");
/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/
$_SESSION["filtres"]["responsables"]["nombre"] = $_POST["responsable_nombre"] ;
$_SESSION["filtres"]["responsables"]["ref_institution"] = $_POST["responsable_ref_institution"] ;
$_SESSION["filtres"]["responsables"]["nom"] = $_POST["responsable_nom"] ;
$_SESSION["filtres"]["responsables"]["login"] = $_POST["responsable_login"] ;
$_SESSION["filtres"]["responsables"]["email"] = $_POST["responsable_email"] ;
$_SESSION["filtres"]["responsables"]["tri"] = $_POST["responsable_tri"] ;
header("Location: index.php") ;
?>
