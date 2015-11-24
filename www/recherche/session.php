<?php
include_once("inc_session.php") ;

$_SESSION["filtres"]["recherche_precedente"] = $_SESSION["filtres"]["recherche"] ;
unset($_SESSION["filtres"]["recherche"]) ;

if ( $_POST["rechercher_annee"] != "" ) {
	$_SESSION["filtres"]["recherche"]["annee"] = $_POST["rechercher_annee"] ;
}
if ( $_POST["rechercher_ref_institution"] != "" ) {
	$_SESSION["filtres"]["recherche"]["ref_institution"] = $_POST["rechercher_ref_institution"] ;
}
if ( $_POST["rechercher_formation"] != "" ) {
	$_SESSION["filtres"]["recherche"]["formation"] = $_POST["rechercher_formation"] ;
}
if ( $_POST["rechercher_promo"] != "" ) {
	$_SESSION["filtres"]["recherche"]["promo"] = $_POST["rechercher_promo"] ;
}
if ( $_POST["rechercher_etat"] != "" ) {
	$_SESSION["filtres"]["recherche"]["etat"] = $_POST["rechercher_etat"] ;
}
if ( $_POST["rechercher_lieu_paiement"] != "" ) {
	$_SESSION["filtres"]["recherche"]["lieu_paiement"] = $_POST["rechercher_lieu_paiement"] ;
}
if ( $_POST["rechercher_lieu_examen"] != "" ) {
	$_SESSION["filtres"]["recherche"]["lieu_examen"] = $_POST["rechercher_lieu_examen"] ;
}
if ( $_POST["rechercher_etat_dossier"] != "" ) {
	$_SESSION["filtres"]["recherche"]["etat_dossier"] = $_POST["rechercher_etat_dossier"] ;
}
//
if ( $_POST["rechercher_genre"] != "" ) {
	$_SESSION["filtres"]["recherche"]["genre"] = $_POST["rechercher_genre"] ;
}
if ( trim($_POST["rechercher_id_mooc"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["id_mooc"] = $_POST["rechercher_id_mooc"] ;
}
if ( trim($_POST["rechercher_nom"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["nom"] = $_POST["rechercher_nom"] ;
}
if ( trim($_POST["rechercher_prenom"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["prenom"] = $_POST["rechercher_prenom"] ;
}
if ( trim($_POST["rechercher_email"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["email"] = trim($_POST["rechercher_email"]) ;
}
if ( trim($_POST["rechercher_pays"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["pays"] = trim($_POST["rechercher_pays"]) ;
}
if ( trim($_POST["rechercher_region"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["region"] = trim($_POST["rechercher_region"]) ;
}
//
if ( trim($_POST["rechercher_tri1"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["tri1"] = trim($_POST["rechercher_tri1"]) ;
}
if ( trim($_POST["rechercher_tri2"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["tri2"] = trim($_POST["rechercher_tri2"]) ;
}
if ( trim($_POST["rechercher_tri3"]) != "" ) {
	$_SESSION["filtres"]["recherche"]["tri3"] = trim($_POST["rechercher_tri3"]) ;
}

if ( isset($_POST["latin1"]) AND ($_POST["latin1"]=="latin1") ) {
	$_SESSION["filtres"]["recherche"]["latin1"] = "latin1" ;
}

if ( isset($_POST["Rechercher"]) AND ($_POST["Rechercher"]=="Rechercher") ) {
	$_SESSION["filtres"]["recherche"]["submit"] = "Rechercher" ;
}
if ( isset($_POST["Exporter"]) AND ($_POST["Exporter"]=="Exporter") ) {
	$_SESSION["filtres"]["recherche"]["submit"] = "Exporter" ;
}

$_SESSION["filtres"]["recherche"]["ok"] = "ok" ;

header("Location: /recherche/#resul") ;
?>
