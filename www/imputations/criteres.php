<?php
include("inc_session.php") ;
/*
while (list($key, $val) = each($_POST)) {
   echo "$key => $val<br />";
}
*/
$_SESSION["filtres"]["imputations"]["annee"] = $_POST["i_annee"] ;
$_SESSION["filtres"]["imputations"]["promotion"] = $_POST["i_promotion"] ;
$_SESSION["filtres"]["imputations"]["lieu"]  = $_POST["i_lieu"] ;
$_SESSION["filtres"]["imputations"]["etat"]  = $_POST["i_etat"] ;
$_SESSION["filtres"]["imputations"]["tri"]  = $_POST["i_tri"] ;
$_SESSION["filtres"]["imputations"]["latin1"] = $_POST["latin1"] ;
if ( $_POST["action"] == "Exporter" ) {
	$_SESSION["filtres"]["imputations"]["action"] = $_POST["action"] ;
}
header("Location: " . $_POST["redirect"]) ;
?>
