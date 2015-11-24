<?php
include("inc_session.php") ;
require_once("inc_guillemets.php");
/*
while (list($key, $val) = each($_POST)) {
   echo "$key => $val<br />";
}
*/
if ( isset($_GET["id_session"]) ) 
{
	$_SESSION["filtres"]["inscrits"]["annee"] = $_POST["c_annee"] ;
	$_SESSION["filtres"]["inscrits"]["groupe"] = $_POST["groupe"] ;
	$_SESSION["filtres"]["inscrits"]["pays"] = $_POST["c_pays"] ;
	$_SESSION["filtres"]["inscrits"]["etat"] = $_POST["c_etat"] ;
	$_SESSION["filtres"]["inscrits"]["lieu_paiement"] = $_POST["c_lieu_paiement"] ;
	$_SESSION["filtres"]["inscrits"]["lieu_examen"] = $_POST["c_lieu_examen"] ;
	$_SESSION["filtres"]["inscrits"]["etat_dossier"] = $_POST["c_etat_dossier"] ;
	$_SESSION["filtres"]["inscrits"]["nom"]  = $_POST["c_nom"] ;
	$_SESSION["filtres"]["inscrits"]["id_mooc"]  = $_POST["c_id_mooc"] ;
	$_SESSION["filtres"]["inscrits"]["max"]  = $_POST["c_max"] ;
	$_SESSION["filtres"]["inscrits"]["tri"]  = $_POST["c_tri"] ;

	header("Location: inscrits.php?id_session=".$_GET["id_session"]) ;
}
else {
	$_SESSION["filtres"]["inscrits"]["pays"] = $_POST["c_pays"] ;
	$_SESSION["filtres"]["inscrits"]["annee"] = $_POST["c_annee"] ;
	$_SESSION["filtres"]["inscrits"]["groupe"] = $_POST["groupe"] ;
	header("Location: index.php") ;
}
?>
