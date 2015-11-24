<?php
include_once("inc_session.php") ;
$_SESSION["filtres"]["recherche_precedente"] = $_SESSION["filtres"]["recherche"] ;
unset($_SESSION["filtres"]["recherche"]) ;
header("Location: /recherche/") ;
?>
