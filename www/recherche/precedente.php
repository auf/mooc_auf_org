<?php
include_once("inc_session.php") ;

$intermediaire = $_SESSION["filtres"]["recherche"] ;
$_SESSION["filtres"]["recherche"] = $_SESSION["filtres"]["recherche_precedente"] ;
$_SESSION["filtres"]["recherche_precedente"] = $intermediaire ;

$_SESSION["filtres"]["recherche"]["ok"] = "ok" ;

header("Location: /recherche/") ;
?>
