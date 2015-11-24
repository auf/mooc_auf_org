<?php

function splashInscription($id_session)
{
	$splash = "
	<div id='splash'>
		<h1><span class='sur'>À lire impérativement avant de s'inscrire</span></h1>

		<p>Ecran facultatif</p>

		<form action='inscription.php?id_session=".$id_session."' method='post'>
		<input type='hidden' name='splash' value='splash' />
		<p class='c'><strong><input type='submit' value='OK' /></strong></p>
		</form>
	</div>\n" ;
	return $splash ;
}

function titreInscription($tab)
{
	$titre  = "<div class='titreSession'>" ;
	$titre .= "<h3>" . $tab["universite"] . "</h3>\n" ;
	$titre .= "<h1>" . $tab["intitule"] . "</h1>\n" ;

	$titre .= "<h1><span class='normal s'>Inscription pour : </span>" ;
	$titre .= $tab["intit_ses"] ;
	if ( $tab["ects"] != "0" ) {
		$titre .= " <span class='normal s' title=\"ECTS : Système européen de transfert et d’accumulation de crédits
(European Credits Transfer System)\">(".$tab["ects"]."&nbsp;ECTS)</span>" ;
	}
	$titre .= "</h1>" ;

	//$titre .= "<h4>Du " . mysql2datealpha($tab["date_deb"]) . " au " . mysql2datealpha($tab["date_fin"]) . "</h4>\n" ;
	$titre .= "<h2><span class='normal s'>Date de l'examen authentifié : </span>" . mysql2datealphajour($tab["date_examen"]) . "</h2>\n" ;
	$titre .= "</div>\n" ;

	if ( trim($tab["chapeau"]) != "" ) {
		$titre .= "<p>".nl2br(trim($tab["chapeau"]))."</p>\n" ;
	}
	return $titre ;
}

// Avant le formulaire à créer
function consignesPreInscription($tab)
{
	$consignes  = "<div class='consignesForm'>\n" ;
	$consignes .= "<p>Complétez le formulaire d'inscription ci-dessous, et cliquez sur le bouton «&nbsp;<strong>Valider</strong>&nbsp;» en bas de page.</p>\n" ;
	if ( $tab["pj"] == "1" ) {
		$consignes .= "Vous pourrez ensuite, immédiatement ou plus tard, joindre un ou plusieurs fichiers à votre dossier d'inscription dans la page suivante, dans laquelle les consignes qui suivent vous seront répétées&nbsp;:</p>\n" ;
		$consignes .= "<p>".nl2br($tab["consignes_pj"])."</p>" ;
	}
	$consignes .= "</div>\n" ;
	return $consignes ;
}



// FIXME jusqu'à date de fin d'imputation ou imputé
require_once("inc_date.php") ;
function consignesDossierInscription($tab)
{
	

	$consignes  = "<div class='consignesForm'>\n" ;

	if ( $tab["genre"] == "Homme" ) {
		$feminin = "" ;
	}
	else {
		$feminin = "e" ;
	}

	if ( $tab["id_imputation"] != "" ) {
		$consignes .= "<p>Vous êtes inscrit".$feminin.".</p>\n" ;
	}
	if ( $tab["id_imputation"] == "" ) {
		$consignes .= "<p>Vous êtes pré-inscrit".$feminin.".<br />"
			. "Date limite pour le paiement et l'inscription définitive : "
			. mysql2datealpha($tab["imputations_fin"]) . ".</p>\n" ;
	}

	// Jusqu'à la veille ou jusqu'au jour de l'examen inclus (cf aussi inscription.php)
	//$consignes .= "<p>Votre numéro de dossier et votre mot de passe vous permettent de modifier votre dossier d'inscription jusqu'à la veille de l'examen.</p>\n" ;
	$consignes .= "<p>Votre numéro de dossier et votre mot de passe vous permettent de modifier votre dossier d'inscription jusqu'au jour de l'examen.</p>\n" ;


	$consignes.= "<p>Vous pouvez imprimer votre dossier d'inscription "
		. "(Menu «&nbsp;Fichier&nbsp;» de votre navigateur, puis «&nbsp;Imprimer&nbsp;»).\n"
		. "<br />Vous pouvez aussi, en cliquant sur un des boutons ci-dessous&nbsp;:</p>\n" ;

	$consignes.= "<ul>\n" ;
	$consignes.= "<li><strong>Envoyer un</strong> (nouveau) <strong>courriel</strong> "
		. "contenant votre numéro de dossier et votre mot de passe "
		. "à votre adresse électronique (utile si vous la modifiez).</li>\n" ;

	if ( $tab["pj"] == "1" )
	{
		$consignes.= "<li><strong>Joindre un fichier</strong> à votre dossier, ou supprimer un fichier déjà joint." ;
		if ( trim($tab["consignes_pj"]) != "" ) {
			$consignes .= "<br />" . nl2br($tab["consignes_pj"]) ;
		}
		$consignes .= "</li>\n" ;
	}
	$consignes.= "<li><strong>Modifier</strong> votre dossier.</li>\n" ;
	$consignes.= "</ul>\n" ;

	$consignes.= "<p>N'oubliez pas de <span class='sur'>quitter votre navigateur quand vous aurez fini.</span> "
		. "Fermer la fenêtre ou l'onglet courant suffit.</p>\n" ;

	$consignes .= "</div>\n" ;

	return $consignes ;
}
?>
