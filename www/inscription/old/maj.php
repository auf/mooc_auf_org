<?php
require_once("inc_config.php") ;
include("inc_html.php");
$titre = "Mise à jour d'un dossier d'inscription" ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2Public ;
echo "<div style='padding: 0.5em; background: #eee'>\n" ;
echo "<h1 class='noprint'>$titre</h1>\n" ;

//
// Pas encore d'identification
//
if ( ( $_POST["id_dossier"] == "" ) OR ( $_POST["pass"] == "" ) ) {
	include("inc_formulaire_maj.php") ;
	echo $endPublic ;
	exit() ;
}

include("inc_mysqli.php");
$cnx = connecter() ;

//
// Vérification id_dossier et pwd ; affichage promotion
//
$req = "SELECT * from dossier, session, atelier
	WHERE id_dossier=" .intval($_POST["id_dossier"]). "
	AND pwd='". $_POST["pass"] ."'
	AND dossier.id_session=session.id_session
	AND atelier.id_atelier=session.id_atelier" ;
$res = mysqli_query($cnx, $req) ;
// Erreur d'identifiaction
if ( mysqli_num_rows($res) == 0 ) {
	echo "<p class='erreur'>" ;
	echo "Erreur. Veuillez vérifier votre numéro de dossier et votre mot de passe (majuscules et minuscules sont significatives).</p>\n" ;
	include("inc_formulaire_maj.php") ;
	echo $endPublic ;
	deconnecter($cnx) ;
	exit() ;
}
// Identification correcte, affichage intitule promotion
else {
	$ligne = mysqli_fetch_assoc($res) ;
	echo "<h1 style='margin-bottom: 0'>" ;
	echo $ligne["intitule"]."</h1>\n" ;
	echo "<p class='c' style='margin-top: 0; font-size: larger;'>Promotion : " ;
	echo $ligne["intit_ses"]."</p>" ;
}
// Candidatures closes
if ( ($ligne["etat"] != "Active") OR ($ligne["candidatures"] != "Ouvertes") ) {
    echo "<p class='c erreur'>Les candidatures sont closes</p>\n" ;
	echo $endPublic ;
	deconnecter($cnx) ;
	exit() ;
}

// ========================================================
//
// Confirmation
//
if ( $_POST["confirmation"] == "oui" ) {
	$req = "UPDATE dossier SET confirmation='oui'
		WHERE id_dossier=" .intval($_POST["id_dossier"]). "
		AND pwd='". $_POST["pass"] ."'" ;
	echo $req ;
	if ( mysqli_query($cnx, $req) ) {
		echo "<p class='msgok'>Votre candidature est confirmée.</p>" ;
	}
	// FIXME
	// Date de candidature = date de confirmation
}

//
// Récupération
//
$req = "SELECT dossier.*, candidat.*
    FROM dossier, candidat
	WHERE id_dossier=" .intval($_POST["id_dossier"]). "
	AND pwd='". $_POST["pass"] ."'
	AND dossier.id_candidat=candidat.id_candidat" ;
$res = mysqli_query($cnx, $req) ;
$T = mysqli_fetch_assoc($res) ;

//
// Absence de confirmation
//
if ( $T["confirmation"] == "non" ) {
	echo "<div class='cadre noprint'>\n" ;
	echo "<div class='erreur'>Vous n'avez pas encore confirmé votre candidature.</div>\n" ;
	echo "<div style='float: right'>\n" ;
	echo "<form method='post' action='maj.php'>\n" ;
	echo "<input type='hidden' name='confirmation' value='oui' />\n" ;
	echo "<input type='hidden' name='id_dossier' value='".$_POST["id_dossier"]."' />\n" ;
	echo "<input type='hidden' name='pass' value='".$_POST["pass"]."' />\n" ;
	echo "<strong><input type='submit' name='submit' value='Confirmer la candidature' /></strong>\n" ;
	echo "</form>\n" ;
	echo "</div>\n" ;
	echo "<div>Pour éviter aux sélectionneurs plusieurs examens de votre candidature,<br />il est préférable de ne la confirmer que lorsqu'elle est complète,<br />mais <span class='erreur'>votre candidature ne sera pas examinée si elle n'est pas confirmée</span>.</div>\n" ;
	echo "</div>\n" ;
	
}



if ( !isset($_POST["formulaire"]) )
{
	include("inc_date.php");
	include("inc_formulaire_candidature.php") ;
	include("inc_etat_dossier.php") ;
	include("inc_dossier.php");
	include("inc_guillemets.php");

	echo "<form method='post' action='maj.php'>\n" ;
//	echo "<input type='hidden' name='formulaire' value='' />\n" ;
	echo "<input type='hidden' name='id_dossier' value='".$_POST["id_dossier"]."' />\n" ;
	echo "<input type='hidden' name='pass' value='".$_POST["pass"]."' />\n" ;
	echo "<input type='submit' name='submit' value='Imprimer' />\n" ;
	echo "<input type='submit' name='submit' value='Modifier' />\n" ;
	echo "<input type='submit' name='submit' value='Joindre un fichier' />\n" ;
	echo "</form>\n" ;

	echo "<p>Votre dossier de candidature, tel qu'il sera vu par les sélectionneurs qui l'examineront, est affiché ci-dessous.\n" ;
	echo "Vous pouvez&nbsp;:</p>\n" ;
	echo "<ul>\n" ;
	echo "<li>l'imprimer (Menu «&nbsp;Fichier&nbsp;» de votre navigateur, «&nbsp;Imprimer&nbsp;»),</li>\n" ;
	echo "<li>le modifier,</li>\n" ;
	echo "<li>y joindre un ou plusieurs fichiers.</li>\n" ;
	echo "</ul>\n" ;

	echo "<div class='apercu'>\n" ;
	affiche_dossier($T, array(), $cnx, FALSE, FALSE, TRUE) ;
	echo "</div>\n" ;
	echo $endPublic ;
	deconnecter($cnx) ;
	exit() ;
}




include("inc_guillemets.php") ;
include("inc_pays.php") ;
include("inc_formulaire_candidature.php") ;
include("fonctions_formulaire_candidature.php") ;











echo "<div class='noprint'>\n" ;

// Arrivée dans le formulaire
if ( $_POST["formulaire"] != "maj" )
{
	$req = "SELECT * from dossier, candidat, session, atelier
		WHERE id_dossier=".$_GET["id_dossier"]
		." AND dossier.id_candidat=candidat.id_candidat"
		." AND dossier.id_session=session.id_session"
		." AND atelier.id_atelier=session.id_atelier" ;
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
