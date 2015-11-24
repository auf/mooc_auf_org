<?php
include_once("inc_session.php") ;
$_SESSION["filtres"]["institutions"]["pays"] = $_POST["institutions_pays"] ;
$_SESSION["filtres"]["institutions"]["qualite"] = $_POST["institutions_qualite"] ;
$_SESSION["filtres"]["institutions"]["statut"] = $_POST["institutions_statut"] ;
$_SESSION["filtres"]["institutions"]["recherche"] = $_POST["institutions_recherche"] ;
$_SESSION["filtres"]["institutions"]["tri"] = $_POST["institutions_tri"] ;
//diagnostic() ;
header("Location: /institutions/") ;
?>
