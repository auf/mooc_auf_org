<?php
include_once("inc_session.php") ;
if ( intval($_SESSION["id"]) != 0 ) {
	header("Location: /bienvenue.php") ;
}
if ( !isset($_GET["id_dossier"]) ) {
	header("Location: /recherche/") ;
}

$titre = "Ajouter un diplôme, et éventuellement un ancien" ;
require_once("inc_html.php");
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
require_once("inc_menu.php");
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/recherche/'>Recherche</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
//echo "&rArr; ".$titre ;
echo $fin_chemin ;

require_once("inc_mysqli.php");
$cnx = connecter() ;

$req = "SELECT dossier.*, candidat.*,
	intitule, universite, atelier.id_atelier, nb_annees,
	intit_ses, annee,
	(SELECT id_imputation FROM imputations WHERE ref_dossier=id_dossier AND annee_relative=1)
	AS id_imputation1,
	(SELECT id_imputation FROM imputations WHERE ref_dossier=id_dossier AND annee_relative=2)
	AS id_imputation2,
	(SELECT etat FROM imputations WHERE ref_dossier=id_dossier AND annee_relative=2)
	AS etat2
	FROM candidat, atelier, session, dossier
	WHERE dossier.id_dossier=".$_GET["id_dossier"]."
	AND dossier.id_candidat=candidat.id_candidat
	AND atelier.id_atelier=session.id_atelier
	AND session.id_session=dossier.id_session" ;
$res = mysqli_query($cnx, $req) ;
$T = mysqli_fetch_assoc($res) ;

//
// Le candidat est déjà diplômé pour ce dossier
//
if ( $T["ref_ancien"] != 0 ) {
	echo "<p>Déjà diplômé.</p>" ;
}
else
{
	require_once("inc_anciens.php") ;

	$refs_ancien = rechercheCandidat($T, $cnx) ;
//	print_r($refs_ancien) ;
//	echo count($refs_ancien) ;
	$ids_ancien = rechercheAnciens($T, $cnx) ;
//	print_r($ids_ancien) ;
//	echo count($ids_ancien) ;
	$anciens = array_merge($refs_ancien, $ids_ancien) ;
	$anciens = array_unique($anciens) ;
	$nbAnciens = count($anciens) ;

	if ( $nbAnciens != count($ids_ancien) ) {
		echo "<p class='erreur'>Incohérence entre la table dossier et les anciens. Merci de signaler cette erreur, en précisant qu'elle concerne le numéro de dossier suivant : " . $_GET["id_dossier"] ."</p>\n" ;
	}

	// Une nouvelle recherche dans les anciens sur l'email seulement
	// Pour ne pas proposer l'ajout d'un nouvel ancien si un ancien existant a le meme
	// NB : cette fonction ne retourne pas un tableau, mais un id, ou 0
	$ids_ancien_email = rechercheAnciensEmail($T, $cnx) ;
	$nbAnciensEmail = count($ids_ancien_email) ;
//	echo $nbAnciensEmail ;

	$ids_ancien_nom_naissance = array_diff($ids_ancien, $ids_ancien_email) ;
	$nbAnciensNomNaissance = count($ids_ancien_nom_naissance) ;
	
	//
	// Pas d'ancien existant correspondant
	//
	if ( $nbAnciens == 0 ) {
		echo "<div class='diplome'>\n" ;
		echo ancienDiplome($T, TRUE) ;
		echo "</div>\n" ;
		echo "<div class='diplome'>\n" ;
		$A = candidat2anciens($T) ;
		echo ancienPublic($A) ;
		echo ancienPrive($A) ;
		echo formulaireAjoutAncien($T) ;
		echo "</div>\n" ;
	}
	else {
		echo "<p>Vous êtes sur le point d'attribuer le diplôme correspondant à la candidature suivante :</p>\n" ;

		echo "<div class='diplome'>\n" ;
		echo ancienDiplome($T, TRUE) ;
		echo "</div>\n" ;

		echo "<div class='diplome'>\n" ;
		$A = candidat2anciens($T) ;
		echo ancienPublic($A) ;
		echo ancienPrive($A) ;
		echo "</div>\n" ;

/*
$anciens
$nbAnciens
$ids_ancien_email
$nbAnciensEmail
$ids_ancien_nom_naissance
$nbAnciensNomNaissance
*/

		echo "<p class='erreur'>Il existe déjà " ;
		if ( $nbAnciens == 1 ) {
			echo "1 ancien qui pourrait correspondre au même individu." ;
		}
		else {
			echo $nbAnciens ;
			echo " anciens qui pourraient correspondre au même individu." ;
		}
		echo "</p>\n" ;
		echo "<ul class='erreur'>\n" ;
		if ( $nbAnciensEmail != 0 ) {
			if ( $nbAnciensEmail == 1 ) {
				echo "<li>$nbAnciensEmail ancien avec la même adresse électronique.</li>\n" ;
			}
			else {
				echo "<li>$nbAnciensEmail anciens avec la même adresse électronique.</li>\n" ;
			}
		} 
		if ( $nbAnciensNomNaissance != 0 ) {
			if ( $nbAnciensNomNaissance == 1 ) {
				echo "<li>$nbAnciensNomNaissance ancien avec le même nom et la même date de naissance.</li>\n" ;
			}
			else {
				echo "<li>$nbAnciensNomNaissance anciens avec le même nom et la même date de naissance.</li>\n" ;
			}
		} 
		echo "</ul>\n" ;

		foreach($ids_ancien_email as $ancien)
		{
			echo "<div class='diplome'>\n" ;
			afficheAncien($ancien, $cnx, TRUE, TRUE, TRUE, FALSE) ;
			echo formulaireAjoutDiplome($T, $ancien) ;
			echo "</div>\n" ;
		}
		foreach($ids_ancien_nom_naissance as $ancien)
		{
			echo "<div class='diplome'>\n" ;
			afficheAncien($ancien, $cnx, TRUE, TRUE, TRUE, FALSE) ;
			echo formulaireAjoutDiplome($T, $ancien) ;
			echo "</div>\n" ;
		}

		// On ne peut ajouter un nouvel ancien que s'il n'en existe pas avec le même email
		if ( $nbAnciensEmail == 0 )
		{
			echo "<p class='erreur'>Si les individus sont différents, vous pouvez aussi créer un nouvel ancien :</p>\n" ;

			echo "<div class='diplome'>\n" ;
			$A = candidat2anciens($T) ;
			echo ancienPublic($A) ;
			echo ancienPrive($A) ;
			echo formulaireAjoutAncien($T) ;
			echo "</div>\n" ;
		}
		else {
			echo "<p class='erreur'>La création d'un nouvel ancien est généralement inappropriée quand il en existe déjà un avec la même adresse électronique.</p>" ;
			if ( $_GET["passer"] != "outre" ) {
				echo "<p class='erreur'>Il est cependant possible de passer <a href='".$_SERVER["REQUEST_URI"]."&passer=outre'>outre</a>.</p>" ;
			}
			else {
				echo "<p class='erreur'>Mais vous êtes passé(e) outre :</p>" ;
				echo "<div class='diplome'>\n" ;
				$A = candidat2anciens($T) ;
				echo ancienPublic($A) ;
				echo ancienPrive($A) ;
				echo formulaireAjoutAncien($T) ;
				echo "</div>\n" ;
			}
		}
	}
}

deconnecter($cnx) ;
echo $end ;
?>
