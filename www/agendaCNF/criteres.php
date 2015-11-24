<?php
include("inc_session.php") ;
if ( isset($_GET["mois"]) AND isset($_GET["annee"]) ) {
	$_SESSION["filtres"]["agendaCNF"]["mois"] = $_GET["mois"] ;
	$_SESSION["filtres"]["agendaCNF"]["annee"] = $_GET["annee"] ;
}
else {
	$_SESSION["filtres"]["agendaCNF"]["mois"] = $_POST["mois"] ;
	$_SESSION["filtres"]["agendaCNF"]["annee"] = $_POST["annee"] ;
	$_SESSION["filtres"]["agendaCNF"]["lieu"] = $_POST["lieu"] ;
}
//print_r($_POST) ;
if ( isset($_GET["mois"]) AND isset($_GET["annee"]) ) {
	header("Location: /agendaCNF/index.php#courant") ;
}
else {
	header("Location: /agendaCNF/") ;
}
?>
