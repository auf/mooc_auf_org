<?php
require_once("inc_session.php") ;
if ( intval($_SESSION["id"]) != 0 ) {
	header("Location: /bienvenue.php") ;
}

include("inc_html.php");
$entete_page_1 = $dtd1
	. "<title>Ajout d'un examen</title>\n"
	. $htmlJquery
	. $htmlMakeSublist
	. $htmlDatePick
	. "<script type='text/javascript'>
$(function() {
$('#dates').datepick({multiSelect: 999, showOn: 'both'});
});
</script>\n"
	. $dtd2 ;
$entete_page_2 = $debut_chemin
	. "<a href='/bienvenue.php'>Accueil</a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/examens/'>Examens</a>"
	. " <span class='arr'>&rarr;</span> "
	. "Ajout"
	. $fin_chemin ;

require_once("inc_mysqli.php");
$cnx = connecter() ;

require_once("inc_promotions.php");
require_once("inc_date.php");
require_once("inc_guillemets.php");

function formulaire_ajout_examen($tab, $cnx)
{
	$form  = "<form method='post' action='ajout.php'>\n" ;
	$form .= "<table class='formulaire'>\n" ;
	$form .= "<tr>\n" ;
	$form .= "<th>Promotion&nbsp;:</th>\n" ;
	$form .= "<td style='width: 50em;'>" ;
	$formPromo = chaine_liste_promotions("promotion_examen",
		$tab["promotion"], "", $cnx) ;
	$form .= $formPromo["form"] ;
	$form .= $formPromo["script"] ;
	$form .= "</td>\n" ;
	$form .= "</tr>\n" ;
	$form .= "<tr>\n<th>Date(s) d'examen</th>\n<td>" ;
	$form .= "<input type='text' size='70' id='dates' name='dates' " ;
	$form .= " value='".$tab["dates"]."' " ;
	$form .= " />" ;
	$form .= "</td>\n</tr>\n" ;
	$form .= "<tr>\n<th>Commentaire&nbsp;:</th>\n<td>" ;
	$form .= "<textarea name='commentaire' rows='3' cols='70'>" ;
	$form .= $tab["commentaire"] ;
	$form .= "</textarea>" ;
	$form .= "</td>\n</tr>\n" ;
//	$form .= "<tr><td colspan='2' class='invisible'>" ;
	$form .= "<tr><td colspan='2'>" ;
	$form .= "<p class='c'>" ;
	$form .= "<input class='b' type='submit' value='Ajouter' />" ;
	$form .= "</p>\n" ;
	$form .= "</td>\n</tr>\n" ;
	$form .= "</table>\n" ;
	$form .= "</form>" ;
	return $form ;
}

//
// Formulaire soumis
//
if ( isset($_POST["promotion_examen"]) )
{
	// Verification
	$erreurs = "" ;
	if ( $_POST["promotion_examen"] == 0 ) {
		$erreurs .= "<p>Le choix d'au moins une promotion est obligatoire.</p>" ;
	}
	if ( $_POST["dates"] == "" ) {
		$erreurs .= "<p>Le choix d'au moins une date est obligatoire.</p>" ;
	}
	else {
		$tab_dates = explode(",", $_POST["dates"]) ;
		foreach($tab_dates as $date) {
			if ( strlen($date) != 10 ) {
				$erreurs .= "<p>$date n'est pas une date valide.</p>" ;
			}
		}
		reset($tab_dates) ;
	}

	if ( $erreurs )
	{
		echo $entete_page_1 ;
		include("inc_menu.php") ;
		echo $entete_page_2 ;
		echo "<div class='erreur c'>$erreurs</div>" ;
		echo formulaire_ajout_examen(array(
			"promotion" => $_POST["promotion_examen"],
			"dates" => $_POST["dates"],
			"commentaire" => $_POST["commentaire"]
			), $cnx) ;
		echo $end ;
	}
	else
	{
		foreach($tab_dates as $date)
		{
			$date = date2mysql($date) ;
			$req = "INSERT INTO examens
				(ref_session, date_examen, commentaire)
				VALUES(".$_POST["promotion_examen"].",
				'$date',
				'".mysqli_real_escape_string($cnx, $_POST["commentaire"])."')" ;
			mysqli_query($cnx, $req) ;
		}
		header("Location: /examens/") ;
	}
}
//
// Arrivee dans le formulaire
//
else
{
	echo $entete_page_1 ;
	include("inc_menu.php") ;
	echo $entete_page_2 ;
	echo formulaire_ajout_examen(
		array(
			"promotion" => $_GET["promotion"]
		), $cnx
	) ;
	echo $end ;
}

deconnecter($cnx) ;

