<?php
require_once("inc_session.php") ;
if ( intval($_SESSION["id"]) != 0 ) {
	header("Location: /bienvenue.php") ;
}

include("inc_html.php");
$entete_page_1 = $dtd1
	. "<title>Promotions : date d'examen</title>"
	. $htmlJquery . $htmlMakeSublist
	. $dtd2 ;
$entete_page_2 = $debut_chemin
	. "<a href='/bienvenue.php'>Accueil</a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/examens/'>Examens</a>"
	. " <span class='arr'>&rarr;</span> "
	. "Date d'examen"
	. $fin_chemin ;


require_once("inc_date.php");
require_once("inc_guillemets.php");
require_once("inc_promotions.php");
require_once("inc_examens.php");

require_once("inc_mysqli.php");
$cnx = connecter() ;

//
// Initialisation du formulaire formulaire_examen.php
//
$id_examen = $promotion_examen = "" ;
$jour_examen = $mois_examen = $annee_examen = "" ;
$am = $pm = "Non" ;
$commentaire = "" ;

//$action = $submit_libelle = "" ;


if ( ($_GET["action"] == "maj") AND isset($_GET["id_examen"]) )
{
	$req = "SELECT * FROM examens
		WHERE id_examen=".$_GET["id_examen"] ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;

	$id_examen        = $enr["id_examen"] ;
	$promotion_examen = $enr["ref_session"] ;
	$date_examen = explode("-", $enr["date_examen"]) ;
	$annee_examen     = $date_examen[0] ;
	$mois_examen      = $date_examen[1] ;
	$jour_examen      = $date_examen[2] ;
	$am               = $enr["am"] ;
	$pm               = $enr["pm"] ;
	$commentaire      = $enr["commentaire"] ;
	$submit_libelle = "Modifier" ;
	$action         = "update" ;

	echo $entete_page_1 ;
	include("inc_menu.php") ;
	echo $entete_page_2 ;
	include("formulaire_examen.php") ;
	// Le lien supprimer est dans formulaire_examen.php
}
//
// UPDATE
//
else if ( $_POST["action"] == "update" )
{
	$erreurs = verifier_examen($_POST) ;
	if ( $erreurs != "" )
	{
		echo $entete_page_1 ;
		include("inc_menu.php") ;
		echo $entete_page_2 ;
		echo $erreurs ;
		$id_examen        = $_POST["id_examen"] ;
		$promotion_examen = $_POST["promotion_examen"] ;
		$annee_examen     = $_POST["annee_examen"] ;
		$mois_examen      = $_POST["mois_examen"] ;
		$jour_examen      = $_POST["jour_examen"] ;
		$commentaire      = $_POST["commentaire"] ;
		$submit_libelle = "Modifier" ;
		$action         = "update" ;

		include("formulaire_examen.php") ;
	}
	else
	{
		include("update_examen.php") ;
		header("Location: /examens/") ;
		//header("Location: /promotions/promotions.php#p". $_POST["promotion_examen"]) ;
	}
}
//
// INSERT
//
else if ( $_POST["action"] == "insert" )
{
	$erreurs = verifier_examen($_POST) ;
	if ( $erreurs != "" )
	{
		echo $entete_page_1 ;
		include("inc_menu.php") ;
		echo $entete_page_2 ;
		echo $erreurs ;
		$promotion_examen = $_POST["promotion_examen"] ;
		$annee_examen     = $_POST["annee_examen"] ;
		$mois_examen      = $_POST["mois_examen"] ;
		$jour_examen      = $_POST["jour_examen"] ;
		$commentaire      = $_POST["commentaire"] ;
		$submit_libelle = "Ajouter" ;
		$action         = "insert" ;

		include("formulaire_examen.php") ;
	}
	else {
		include("insert_examen.php") ;
		header("Location: /examens/") ;
	}
}
//
// DELETE
//
else if ( ($_GET["action"] == "delete") AND isset($_GET["id_examen"]) )
{
	$req = "DELETE FROM examens
		WHERE id_examen=".$_GET["id_examen"] ;
	mysqli_query($cnx, $req) ;
	header("Location: /examens/") ;
}
//
// Ajout
//
else
{
	if ( intval($_GET["promotion"]) != 0  )
	{
		$promotion_examen = $_GET["promotion"] ;
		$redirect = "promotions" ;
	}
	$action = "insert" ;
	$submit_libelle = "Ajouter" ;
	echo $entete_page_1 ;
	include("inc_menu.php") ;
	echo $entete_page_2 ;
	include("formulaire_examen.php") ;
}

deconnecter($cnx) ;
echo $end ;

