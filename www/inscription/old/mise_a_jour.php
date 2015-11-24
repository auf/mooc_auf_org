<?php
require_once("inc_config.php") ;
include("inc_html.php") ;
echo $dtd1 ;
$titre = "Mise à jour d'un dossier de candidature" ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
echo "<div style='margin: 0.5em'>\n" ;

/*
Pas de session HTTP pour éviter son expiration
Deux variables id_dossier et pass (md5 de pwd)) :
- en GET lors de l'arrivée dans la page : $_POST["formulaire"] vide
- en POST pour chaque action dans la page
*/
if ( isset($_GET["id_dossier"]) AND isset($_GET["pass"]) ) {
	$id_dossier = $_GET["id_dossier"] ;
	$pass = $_GET["pass"] ;
}
else if ( isset($_POST["id_dossier"]) AND isset($_POST["pass"]) ) {
	$id_dossier = $_POST["id_dossier"] ;
	$pass = $_POST["pass"] ;
}
else {
	unset($id_dossier) ;
	unset($pass) ;
}


include("inc_mysqli.php") ;
$cnx = connecter() ;


$req = "SELECT * from dossier, session, atelier
	WHERE id_dossier=".$id_dossier 
	." AND dossier.id_session=session.id_session"
	." AND atelier.id_atelier=session.ref_atelier" ;
$res = mysqli_query($cnx, $req) ;
$ligne = mysqli_fetch_assoc($res) ;
if ( md5($ligne["pwd"]) != $pass ) {
	echo "<p class='erreur'>Accès interdit !</p>" ;
	echo $endPublic ; deconnecter($cnx) ; exit() ;
}

echo "<h1 style='margin-bottom: 0'>" ;
echo "<span  class='noprint' style='font-size: smaller'>$titre<br /></span>\n" ;
echo $ligne["intitule"]."</h1>\n" ;
echo "<p class='c' style='margin-top: 0; font-size: larger;'><strong>Promotion : " ;     
echo $ligne["intit_ses"]."</strong></p>" ;

if ( ($ligne["etat"] != "Active") OR ($ligne["candidatures"] != "Ouvertes") ) {
    echo "<p class='c erreur'>Les candidatures sont closes</p>\n" ;
	echo $endPublic ; deconnecter($cnx) ; exit() ;
}



include("inc_guillemets.php") ;
include("inc_pays.php") ;
include("inc_formulaire_candidature.php") ;
include("fonctions_formulaire_candidature.php") ;



include("inc_date.php");
include("inc_etat_dossier.php") ;
include("inc_dossier.php");

$req = "SELECT dossier.*, candidat.*
    FROM dossier JOIN candidat ON dossier.id_candidat=candidat.id_candidat
    WHERE dossier.id_dossier=$id_dossier" ;
$res = mysqli_query($cnx, $req) ;
$T = mysqli_fetch_assoc($res) ;



echo "<div class='apercu'>\n" ;
affiche_dossier($T, $_SESSION, $cnx, FALSE, FALSE) ;
echo "</div>\n" ;









echo "<div class='noprint'>\n" ;

// Arrivée dans le formulaire
if ( $_POST["formulaire"] != "maj" )
{
	$req = "SELECT * from dossier, candidat, session, atelier
		WHERE id_dossier=".$_GET["id_dossier"]
		." AND dossier.id_candidat=candidat.id_candidat"
		." AND dossier.id_session=session.id_session"
		." AND atelier.id_atelier=session.ref_atelier" ;
	$res = mysqli_query($cnx, $req) ;
	$ligne = mysqli_fetch_assoc($res) ;

	while ( list($key, $val) = each($ligne) ) {
		$T[$key] = $val ;
	}

	?><form action="mise_a_jour.php" method="post"><?php

	// Date de naissance
	$tab_naissance = explode("-", $T["naissance"]) ;
	$T["annee_n"] = $tab_naissance[0] ;
	$T["mois_n"] = $tab_naissance[1] ;
	$T["jour_n"] = $tab_naissance[2] ;

	// Stages
	$req = "SELECT * FROM stage WHERE id_candidat=".$T["id_candidat"] ;
	$res = mysqli_query($cnx, $req) ;
	$i = 1 ;
	while ( $ligne = mysqli_fetch_assoc($res) )
	{
		echo "<input type='hidden' name='code_stage$i' " ;
			echo "value='".$ligne["code_stage"]."' />\n" ;
		$T["code_stage$i"] = $ligne["code_stage"] ;
		$T["annee_stage$i"] = $ligne["annee_stage"] ;
		$T["titre_stage$i"] = $ligne["titre_stage"] ;
		$T["org_stage$i"] = $ligne["org_stage"] ;
		$i++ ;
	}

	// Diplomes
	$req = "SELECT * FROM diplomes WHERE id_candidat=".$T["id_candidat"] ;
	$res = mysqli_query($cnx, $req) ;
	$i = 1 ;
	while ( $ligne = mysqli_fetch_assoc($res) )
	{
		echo "<input type='hidden' name='code_dip$i' " ;
			echo "value='".$ligne["code_dip"]."' />\n" ;
		$T["code_dip$i"] = $ligne["code_dip"] ;
		$T["annee_dip$i"] = $ligne["annee_dip"] ;
		$T["titre_dip$i"] = $ligne["titre_dip"] ;
		$T["etab_dip$i"] = $ligne["etab_dip"] ;
		$i++ ;
	}

	include("formulaire_candidature.php") ;

	// Questions supplémentaires
	include("questions.php") ;
	if ( $nombre_questions > 0 )
	{
		$req = "SELECT * FROM reponse WHERE id_dossier=$id_dossier
			ORDER BY id_question" ;
		$res = mysqli_query($cnx, $req) ;

		$i = 1 ;
		foreach($Questions as $question)
		{
			$ligne = mysqli_fetch_assoc($res) ;
			echo "<input type='hidden' name='id_reponse$i' " ;
				echo "value='".$ligne["id_reponse"]."' />\n" ;
			$T["id_question$i"] = $ligne["id_question"] ;
			$T["question$i"] = $ligne["texte_rep"] ;
			$i++ ;
		}
		reset($Questions) ;
		include("formulaire_questions.php") ;
	}

	include("signature_candidature.php") ;

	?>
	<input type="hidden" name="formulaire" value="maj" />
	<input type="hidden" name="pass" value="<?php echo $pass ; ?>" />
	<input type="hidden" name="id_dossier" value="<?php echo $id_dossier ; ?>" />
	<input type="hidden" name="id_session" value="<?php echo $T["id_session"] ; ?>" />
	<input type="hidden" name="id_candidat" value="<?php echo $T["id_candidat"] ; ?>" />
	<p class='c'><input type="submit" value="Modifier" /></p>
	</form><?php
}
//
// Formulaire posté
//
else
{
	// Traitement des guillemets
	while ( list($key, $val) = each($_POST) ) {
		$T[$key] = trim(enleve_guillemets($val)) ;
	}

	include("questions.php") ;
	include("controle_formulaire_candidature.php") ;
	include("controle_signature_candidature.php") ;
	include("controle_questions.php") ;
	include("controle_erreurs.php") ;

	if ( $erreurs )
	{
		?><form action="mise_a_jour.php" method="post"><?php
		include("formulaire_candidature.php") ;
		include("formulaire_questions.php") ;
		include("signature_candidature.php") ;
		for ( $i=1; $i <=4 ; $i++ ) {
			echo "<input type='hidden' name='code_stage$i' " ;
				echo "value='".$T["code_stage$i"]."' />\n" ;
		}
		for ( $i=1; $i <=4 ; $i++ ) {
			echo "<input type='hidden' name='code_dip$i' " ;
				echo "value='".$T["code_dip$i"]."' />\n" ;
		}
		for ( $i=1; $i <= $nombre_questions ; $i++ ) {
			echo "<input type='hidden' name='id_reponse$i' " ;
				echo "value='".$T	["id_reponse$i"]."' />\n" ;
		}
		?><input type="hidden" name="formulaire" value="maj" />
		<input type="hidden" name="pass" value="<?php echo $pass ; ?>" />
		<input type="hidden" name="id_dossier" value="<?php echo $id_dossier ; ?>" />
		<input type="hidden" name="id_session" value="<?php echo $T["id_session"] ; ?>" />
		<input type="hidden" name="id_candidat" value="<?php echo $T["id_candidat"] ; ?>" />
		<p class='c'><input type="submit" value="Modifier" /></p>
		</form><?php
	}
	else
	{
		include("maj_candidature.php") ;
	}
}

echo "</div>\n" ; // noprint

deconnecter($cnx) ;
echo $endPublic ;
?>
