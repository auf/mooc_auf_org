<?php
include("inc_session.php") ;
require_once("inc_guillemets.php");
/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/
$_SESSION["filtres"]["promotions"]["annee"] = $_POST["p_annee"] ;
$_SESSION["filtres"]["promotions"]["groupe"] = $_POST["groupe"] ;
if ( isset($_POST["p_exam"]) ) {
	$_SESSION["filtres"]["promotions"]["exam"] = $_POST["p_exam"] ;
}
else {
	unset($_SESSION["filtres"]["promotions"]["exam"]) ;
}
header("Location: index.php") ;
?>
