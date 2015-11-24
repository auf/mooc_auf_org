<?php
require_once("inc_session.php") ;
if ( intval($_SESSION["id"]) != 0 ) {
	header("Location: /bienvenue.php") ;
}
require_once("inc_html.php");
require_once("inc_indispos.php");
$titre = "Date" ;
$entete_page_1 = $dtd1
	. "<title>" . $titre . "</title>"
	. $htmlJquery
	. $htmlDatePick
    . "<script type='text/javascript'>
$(function() {
$('#date_indispo').datepick({showOn:'both', showBigPrevNext:true, firstDay: 0});
});
</script>\n"
	. scriptCheckBoxes()
	. '<link href="/css/calendar.css" rel="stylesheet" type="text/css" media="screen" />'
	. $dtd2 ;
$entete_page_2 = $debut_chemin
	. "<a href='/bienvenue.php'>Accueil</a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/agendaCNF/'>" . $titreIndispos . "</a>"
	. " <span class='arr'>&rarr;</span> "
	. $titre
	. $fin_chemin ;

require_once("inc_date.php");
require_once("inc_guillemets.php");

require_once("inc_mysqli_foad.php");
$cnx = connecter_foad() ;

//
// Initialisation du formulaire formulaire_indispo.php
//
$id_indispo = "" ;
$jour_indispo = $mois_indispo = $annee_indispo = "" ;
$commentaire = "" ;

if ( isset($_GET["id_indispo"]) )
{
	$req = "SELECT indispos.*, GROUP_CONCAT(cnf ORDER BY cnf SEPARATOR ', ') AS liste
	    FROM indispos LEFT JOIN indispos_cnf ON id_indispo=ref_indispo
		WHERE id_indispo=".$_GET["id_indispo"]."
	    GROUP BY id_indispo" ;
	$res = mysqli_query($cnx, $req) ;

	if ( mysqli_num_rows($res) == 0 )
	{
		deconnecter_foad($cnx) ;
		header("Location: /agendaCNF/") ;
		exit() ;
	}
	else
	{
		$enr = mysqli_fetch_assoc($res) ;
		$liste = $enr["liste"] ;
		$lieux = explode(", ", $liste) ;
		$agenda = array(
			"id_indispo"    => $enr["id_indispo"],
			"date_indispo"  => $enr["date_indispo"],
			"commentaire"   => $enr["commentaire"],
			"lieux"         => $lieux
		) ;

		//
		// DELETE
		//
		if ( $_GET["action"] == "delete" )
		{
			$req = "DELETE FROM indispos
				WHERE id_indispo=".$_GET["id_indispo"] ;
			mysqli_query($cnx, $req) ;
			$req = "DELETE FROM indispos_cnf
				WHERE ref_indispo=".$_GET["id_indispo"] ;
			mysqli_query($cnx, $req) ;
			header("Location: /agendaCNF/") ;
		}
		else
		{
			echo $entete_page_1 ;
			include("inc_menu.php") ;
			echo $entete_page_2 ;
			echo formulaireIndispo($agenda, "update", "Modifier") ;
		}
	}
}
//
// UPDATE
//
else if ( isset($_POST["action"]) AND ($_POST["action"] == "update") )
{
	$erreurs = verifier_indispo($_POST) ;
	$lieux = $_POST["lieux"] ;
//	sort($lieux) ;
	if ( $erreurs != "" )
	{
		$agenda = array(
			"id_indispo"    => $_POST["id_indispo"],
			"date_indispo"  => date2mysql($_POST["date_indispo"]),
			"commentaire"   => $_POST["commentaire"],
			"lieux"         => $lieux
		) ;
		echo $entete_page_1 ;
		include("inc_menu.php") ;
		echo $entete_page_2 ;
		echo $erreurs ;
		echo formulaireIndispo($agenda, "update", "Modifier") ;
	}
	else
	{
		$req = "UPDATE indispos SET
			date_indispo='".date2mysql($_POST["date_indispo"])."',
			commentaire='".mysqli_real_escape_string($cnx, $_POST["commentaire"])."'
			WHERE id_indispo=".$_POST["id_indispo"] ;
		mysqli_query($cnx, $req) ;

		$req = "DELETE FROM indispos_cnf
			WHERE ref_indispo=".$_POST["id_indispo"] ;
		mysqli_query($cnx, $req) ;

		foreach($lieux as $lieu) {
			$req = "INSERT INTO indispos_cnf(ref_indispo, cnf) VALUES(
				".$_POST["id_indispo"].",
				'".mysqli_real_escape_string($cnx, $lieu)."'
			)" ;
			mysqli_query($cnx, $req) ;
		}

		header("Location: /agendaCNF/") ;
	}
}
//
// INSERT
//
else if ( isset($_POST["action"]) AND ($_POST["action"] == "insert") )
{
	if ( is_array($_POST["lieux"]) ) {
		$lieux = $_POST["lieux"] ;
//		sort($lieux) ;
	}
	else {
		$lieux = array() ;
	}

	$erreurs = verifier_indispo($_POST) ;
	if ( $erreurs != "" )
	{
		$agenda = array(
			"id_indispo"    => 0,
			"date_indispo"  => date2mysql($_POST["date_indispo"]),
			"commentaire"   => $_POST["commentaire"],
			"lieux"         => $lieux
		) ;
		echo $entete_page_1 ;
		include("inc_menu.php") ;
		echo $entete_page_2 ;
		echo $erreurs ;
		echo formulaireIndispo($agenda, "insert", "Ajouter") ;
	}
	else {
		$req = "INSERT INTO indispos(date_indispo, commentaire) VALUES(
			'".date2mysql($_POST["date_indispo"])."',
			'".mysqli_real_escape_string($cnx, $_POST["commentaire"])."'
			)" ;
		mysqli_query($cnx, $req) ;
		$id_indispo = mysqli_insert_id($cnx) ;

		foreach($_POST["lieux"] as $lieu) {
			$req = "INSERT INTO indispos_cnf(ref_indispo, cnf) VALUES(
				".$id_indispo.",
				'".mysqli_real_escape_string($cnx, $lieu)."'
			)" ;
			mysqli_query($cnx, $req) ;
		}

		header("Location: /agendaCNF/") ;
	}
}
//
// Ajout
//
else
{
	$agenda = array(
		"id_indispo"    => 0,
		"date_indispo"  => "",
		"commentaire"   => "",
		"liste"         => "",
		"cnf"           => array()
	) ;
	echo $entete_page_1 ;
	include("inc_menu.php") ;
	echo $entete_page_2 ;
	echo formulaireIndispo($agenda, "insert", "Ajouter") ;
}

/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/

deconnecter_foad($cnx) ;
echo $end ;
?>
