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
	$form .= "<th>Promotion(s)&nbsp;:</th>\n" ;
	$form .= "<td style='width: 50em;'>" ;

	if ( isset($tab["promotions"]) AND is_array($tab["promotions"]) AND (count($tab["promotions"]) > 0) ) {
		$liste_promos = "" ;
		foreach ($tab["promotions"] as $promo ) {
			$liste_promos .= $promo . ", " ;
			$form .= "<input type='hidden' name='promotions[]' value='$promo' />\n" ;
		}
		$liste_promos = substr($liste_promos, 0, -2) ;
		$req = "SELECT id_session, intitule, intit_ses FROM atelier, session
			WHERE atelier.id_atelier=session.id_atelier
			AND id_session IN (".$liste_promos.")" ;
		$res = mysqli_query($cnx, $req) ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
			$form .= "<div style='margin-bottom: 0.5em;'>" ;
			$form .= $enr["intitule"] ;
			$form .= " (". $enr["intit_ses"] . ")" ;
			$form .= "&nbsp;<input style='font-size: smaller; padding: 0; margin: 0;' type='submit' name='".$enr["id_session"]."' value='Supprimer' />\n" ;
			$form .= "</div>" ;
		}
	}

	$form .= "<div style='float: right; position: relative; top: -2px;'>\n" ;
	$form .= "<input class='b' type='submit' name='submitA' value='Ajouter' />\n" ;
	$form .= "</div>\n" ;
	/*
	$formPromo = chaine_liste_promotions("promotion_examen",
		$tab["promotion"], "", $cnx) ;
	$form .= $formPromo["form"] ;
	$form .= $formPromo["script"] ;
	*/
	$form .= liste_promotions("promotion_examen", $tab["promotion"], $cnx, TRUE) ;
	$form .= "</td>\n" ;
	$form .= "</tr>\n" ;
	$form .= "<tr>\n<th>Date(s) d'examen&nbsp;:</th>\n<td>" ;
	$form .= "<input type='text' size='70' id='dates' name='dates' " ;
	$form .= " value='".( isset($tab["dates"]) ? $tab["dates"] : "" )."' " ;
	$form .= " />" ;
	$form .= "</td>\n</tr>\n" ;
	$form .= "<tr>\n<th>Commentaire&nbsp;:</th>\n<td>" ;
	$form .= "<textarea name='commentaire' rows='3' cols='70'>" ;
	$form .= ( isset($tab["commentaire"]) ? $tab["commentaire"] : "" ) ;
	$form .= "</textarea>" ;
	$form .= "</td>\n</tr>\n" ;
//	$form .= "<tr><td colspan='2' class='invisible'>" ;
	$form .= "<tr><td colspan='2'>" ;
	$form .= "<p class='c'>" ;
	$form .= "<input class='b' type='submit' name='submitE' value='Enregistrer' />" ;
	if ( isset($tab["confirm"]) AND ( $tab["confirm"] == TRUE ) )
	{
		$form .= " ou " ;
		$form .= "<input class='b' type='submit' name='submitE' value='Confirmer' />" ;
		$form .= "<div class='c' style='font-size: smaller'>Confirmer permet d'enregistrer en passant outre l'avertissement relatif à l'agenda des CNF</div>" ;
	}
	$form .= "</p>\n" ;
	$form .= "</td>\n</tr>\n" ;
	$form .= "</table>\n" ;
	$form .= "</form>" ;
	return $form ;
}

//
// Formulaire soumis
//
// Ne peut pas être vide (0 ou entier), donc bonne condition
if ( isset($_POST["promotion_examen"]) )
{
	// Formulaire soumis par bouton submit pour enregistrer ou ajouter une promotion
	if ( isset($_POST["submitA"]) OR isset($_POST["submitE"]) )
	{
		// Par bouton submit pour ajouter une promotion
		if ( isset($_POST["submitA"]) )
		{
			$erreurs = "" ;
			if ( $_POST["promotion_examen"] == 0 ) {
				$erreurs .= "<p>Il faut choisir une promotion pour pouvoir en ajouter une autre.</p>" ;
			}
			if ( isset($_POST["promotions"]) AND is_array($_POST["promotions"]) AND in_array($_POST["promotion_examen"], $_POST["promotions"]) ) {
				$erreurs .= "<p>Cette promotion était déjà sélectionnée.</p>" ;
			}
			if ( $erreurs )
			{
				echo $entete_page_1 ;
				include("inc_menu.php") ;
				echo $entete_page_2 ;
				echo "<div class='erreur c'>$erreurs</div>" ;
				$promotions = $_POST["promotions"] ;
				echo formulaire_ajout_examen(array(
						"promotions" => $promotions,
						"promotion" => 0,
						"dates" => $_POST["dates"],
						"commentaire" => $_POST["commentaire"]
					), $cnx) ;
				echo $end ;
			}
			else
			{
				echo $entete_page_1 ;
				include("inc_menu.php") ;
				echo $entete_page_2 ;
				if ( isset($_POST["promotions"]) ) {
					$promotions = $_POST["promotions"] ;
				}
				else {
					$promotions = array() ;
				}
				$promotions[] = $_POST["promotion_examen"] ;
				echo formulaire_ajout_examen(array(
						"promotions" => $promotions,
						"promotion" => 0,
						"dates" => $_POST["dates"],
						"commentaire" => $_POST["commentaire"]
					), $cnx) ;
				echo $end ;
			}
		}
		// Par bouton submit pour enregistrer
		else if ( isset($_POST["submitE"]) )
		{
			// Verification
			$erreurs = "" ;
			$erreursAgenda = "" ;
			if ( ($_POST["promotion_examen"] == 0) AND ( !is_array($_POST["promotions"]) ) ) {
				$erreurs .= "<p>Le choix d'au moins une promotion est obligatoire.</p>" ;
			}
			if ( $_POST["dates"] == "" ) {
				$erreurs .= "<p>Le choix d'au moins une date est obligatoire.</p>" ;
			}
			else {
				$tab_dates = explode(",", $_POST["dates"]) ;
				$liste_dates = "" ;
				foreach($tab_dates as $date) {
					if ( strlen($date) != 10 ) {
						$erreurs .= "<p>$date n'est pas une date valide (espaces ?).</p>" ;
					}
					$dateSQL = date2mysql($date) ;
					$liste_dates .= "'" . $dateSQL . "'" . ", " ;
				}
				$liste_dates = substr($liste_dates, 0, -2) ;
				reset($tab_dates) ;

				$req = "SELECT indispos.*,
					GROUP_CONCAT(cnf ORDER BY cnf SEPARATOR ', ') AS liste
					FROM indispos LEFT JOIN indispos_cnf ON id_indispo=ref_indispo
					WHERE date_indispo IN ($liste_dates)
					GROUP BY id_indispo
					ORDER BY date_indispo, id_indispo" ;
				$res = mysqli_query($cnx, $req) ;
				if ( mysqli_num_rows($res) != 0 )
				{
					$erreursAgenda .= "<p>Tous les CNF ne sont pas disponibles :" ;
					while ( $row = mysqli_fetch_assoc($res) ) {
						$erreursAgenda .= "<br />" ;
						$erreursAgenda .= mysql2date($row["date_indispo"]) ;
						$erreursAgenda .= " : " ;
						$erreursAgenda .= $row["commentaire"] ;
						$erreursAgenda .= " " ;
						$erreursAgenda .= "(<em>" . $row["liste"] . "</em>)" ;
					}
					$erreursAgenda .= "</p>" ;
				}
			}
		
			if ( $erreurs )
			{
				echo $entete_page_1 ;
				include("inc_menu.php") ;
				echo $entete_page_2 ;
				echo "<div class='erreur c'>$erreurs</div>" ;
				if ( $erreursAgenda != "" )
				{
					echo "<div class='erreur c'>$erreursAgenda</div>" ;
					echo formulaire_ajout_examen(array(
						"promotions" => $_POST["promotions"],
						"promotion" => $_POST["promotion_examen"],
						"dates" => $_POST["dates"],
						"commentaire" => $_POST["commentaire"],
						"confirm" => TRUE
						), $cnx) ;
				}
				else
				{
					echo formulaire_ajout_examen(array(
						"promotions" => $_POST["promotions"],
						"promotion" => $_POST["promotion_examen"],
						"dates" => $_POST["dates"],
						"commentaire" => $_POST["commentaire"]
						), $cnx) ;
				}
				echo $end ;
			}
			else if ( $erreursAgenda AND ( $_POST["submitE"] == "Enregistrer" ) )
			{
				echo $entete_page_1 ;
				include("inc_menu.php") ;
				echo $entete_page_2 ;
				echo "<div class='erreur c'>$erreursAgenda</div>" ;
				echo formulaire_ajout_examen(array(
					"promotions" => $_POST["promotions"],
					"promotion" => $_POST["promotion_examen"],
					"dates" => $_POST["dates"],
					"commentaire" => $_POST["commentaire"],
					"confirm" => TRUE
					), $cnx) ;
				echo $end ;
			}
			else
			{
				if ( is_array($_POST["promotions"]) ) {
					$promos = $_POST["promotions"] ;
				}
				else {
					$promos = array() ;
				}
				if ( $_POST["promotion_examen"] != 0 ) {
					$promos[] = $_POST["promotion_examen"] ;
				}

				foreach ($promos as $promo)
				{
					foreach($tab_dates as $date)
					{
						$date = date2mysql($date) ;
						$req = "INSERT INTO examens
							(ref_session, date_examen, commentaire)
							VALUES(".$promo.",
							'$date',
							'".mysqli_real_escape_string($cnx, $_POST["commentaire"])."')" ;
						mysqli_query($cnx, $req) ;
					}
				}
				header("Location: /examens/") ;
			}
		}
	}
	// Par un bouton Supprimer
	else if ( $cle = array_search("Supprimer", array_values($_POST)) )
	{
		$cles = array_keys($_POST) ;
		$cle = $cles[$cle] ;

		$promotions = array() ;
		foreach ( $_POST["promotions"] as $promo ) {
			if ( $promo != $cle ) {
				$promotions[] = $promo ;
			}
		}

		echo $entete_page_1 ;
		include("inc_menu.php") ;
		echo $entete_page_2 ;
		echo formulaire_ajout_examen(array(
				"promotions" => $promotions,
				"promotion" => 0,
				"dates" => $_POST["dates"],
				"commentaire" => $_POST["commentaire"]
			), $cnx) ;
		echo $end ;
	}
	// Cas non prevu thoriquement impossible
	else
	{
		echo "Erreur" ;
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
			"promotion" => ( isset($_GET["promotion"]) ? $_GET["promotion"] : "" )
		), $cnx
	) ;
	echo $end ;
}

deconnecter($cnx) ;

//diagnostic() ;

?>
