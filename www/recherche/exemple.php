<?php
include_once("inc_session.php") ;

$_SESSION["filtres"]["recherche_precedente"] = $_SESSION["filtres"]["recherche"] ;
unset($_SESSION["rechercher"]) ;








$_SESSION["filtres"]["recherche"]["nom"] = rawurldecode($_GET["nom"]) ;

$_SESSION["filtres"]["recherche"]["tri1"] = "nom" ;
//$_SESSION["filtres"]["recherche"]["tri2"] = "" ;
//$_SESSION["filtres"]["recherche"]["tri3"] = "" ;

$_SESSION["filtres"]["recherche"]["ok"] = "ok" ;

header("Location: /recherche/") ;
?>
