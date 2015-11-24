<?php
include_once("inc_session.php") ;
require_once("inc_guillemets.php") ;
$_SESSION["filtres"]["individus"] = array() ;
if ( $_POST["individus_cnf"] != "" ) {
	$_SESSION["filtres"]["individus"]["cnf"] = $_POST["individus_cnf"] ;
}
else if ( $_POST["individus_pays"] != "" ) {
	$_SESSION["filtres"]["individus"]["pays"] = $_POST["individus_pays"] ;
}
else if ( $_POST["individus_region"] != "" ) {
	$_SESSION["filtres"]["individus"]["region"] = $_POST["individus_region"] ;
}
$_SESSION["filtres"]["individus"]["actif"] = $_POST["individus_actif"] ;
$_SESSION["filtres"]["individus"]["tri"] = $_POST["individus_tri"] ;
//diagnostic() ;
header("Location: /individus/") ;
?>
