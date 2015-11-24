<?php
include_once("inc_etat_dossier.php") ;
include_once("inc_etat_inscrit.php") ;
include_once("inc_cnf.php") ;
include_once("inc_identite.php") ;

function formulaire_courriel($messagerie, $courriel, $cnx)
{
	global $ETAT_DOSSIER ;
	global $ETAT_DOSSIER_IMG_CLASS ;

	$req = "SELECT id_dossier, genre, dossier.nom, prenom, etat_dossier, id_imputation, lieu_examen
		FROM dossier
		LEFT JOIN imputations ON imputations.ref_dossier=dossier.id_dossier
		WHERE dossier.ref_session=".$messagerie["promotion"] ;
	if ( !empty($messagerie["etat"]) ) {
		if ( $messagerie["etat"] == "P" ) {
			$req .= " AND id_imputation IS NULL " ;
		}
		else if ( $messagerie["etat"] == "I" ) {
			$req .= " AND id_imputation IS NOT NULL " ;
		}
	}
	if ( !empty($messagerie["lieu_examen"]) ) {
		$req .= " AND lieu_examen='".mysqli_real_escape_string($cnx, $messagerie["lieu_examen"])."'
			AND id_imputation IS NOT NULL" ;
	}
	if ( isset($messagerie["etat_dossier"]) AND ($messagerie["etat_dossier"] != "") ) {
		$req .= " AND etat_dossier='".mysqli_real_escape_string($cnx, $messagerie["etat_dossier"])."'
			AND id_imputation IS NOT NULL" ;
	}
	$req .= " ORDER BY nom" ;
	$res = mysqli_query($cnx, $req) ;
	$nbDestinataires = mysqli_num_rows($res) ;

	echo "<form enctype='multipart/form-data' action='session.php' method='post'>\n" ;
	echo "<input type='hidden' name='ok' value='ok' />\n" ;
	echo "<input type='hidden' name='promotion' " ;
		echo "value='".$messagerie["promotion"]."' />\n" ;

	echo "<table class='formulaire' style='margin-bottom: 1em;'>\n<tr>\n" ;
	echo "<th>&Eacute;tat&nbsp;:</th>\n<td colspan='2'>" ;
	liste_etat_inscrit("etat", $messagerie["etat"], TRUE) ;
	echo "</td></tr>" ;

	/*
	echo "<tr>\n<th>Lieu d'examen&nbsp;:</th>\n<td>" ;
	echo listeCnf("lieu_examen",
		( isset($_SESSION["messagerie"]["lieu_examen"]) ? $_SESSION["messagerie"]["lieu_examen"] : "" ),
		TRUE) ;
	echo "</td></tr>\n" ;
	*/

	echo "<tr>\n<th>Lieu d'examen&nbsp;:</th>\n<td>" ;
	echo "<select name='lieu_examen'>\n" ;
	echo "<option value=''></option>\n" ;
	$req2 = "SELECT DISTINCT lieu_examen FROM dossier, imputations
		WHERE ref_dossier=id_dossier
		AND ref_session=".$messagerie["promotion"]."
		ORDER BY lieu_examen" ;
	$res2 = mysqli_query($cnx, $req2) ;
	while ( $enr2 = mysqli_fetch_assoc($res2) ) {
		echo "<option value=\"".$enr2["lieu_examen"]."\"" ;
		if  (
				isset($_SESSION["messagerie"]["lieu_examen"])
				AND ($_SESSION["messagerie"]["lieu_examen"] == $enr2["lieu_examen"])
			)
		{
			echo " selected='selected'" ;
		}
		echo ">".$enr2["lieu_examen"]."</option>\n" ;
	}
	echo "</td>\n" ;
    echo "<td rowspan='2'>\n" ;
    echo " <span class='s'>Ne concerne que les inscrits.<br />(Implique État = Inscrit.)</span>" ;
    echo "</td>\n" ;
	echo "</tr>\n" ;

	echo "<tr>\n<th>Résultat&nbsp;:</th>\n<td>" ;
	echo liste_etats("etat_dossier",
		( isset($_SESSION["messagerie"]["etat_dossier"]) ? $_SESSION["messagerie"]["etat_dossier"] : "" ),
		TRUE) ;
	echo "</td>\n"; 
	echo "</tr>\n" ;

	echo "<tr><td colspan='3'><div class='c'><input type='submit' style='font-weight: bold' " ;
	echo "name='dest' value='Modifier les destinataires' /></div></td>" ;
	echo "</tr>\n</table>\n" ;

	// Pour les fonctions strtoupper etc
	echo "<table class='formulaire'>\n" ;
	// Destinataires
	echo "<tr>\n" ;
	if ( $nbDestinataires == 0 ) {
		echo "<th>Destinataires ($nbDestinataires)&nbsp;:</th>" ;
		echo "<td colspan='3'>Il n'y a aucun destinataire potentiel" ;
		if ( !empty($messagerie["etat"]) ) {
			echo " pour un état ".$messagerie["etat"] ;
		}
		else {
			echo "." ;
		}
		echo "</td></tr>\n" ;
	}
	else {
		echo "<th rowspan='$nbDestinataires'>" ;
		echo "Destinataires ($nbDestinataires)&nbsp;:<br />" ;
		echo "<a href='javascript:checkAll()'>Tout cocher</a> - " ;
		echo "<a href='javascript:unCheck()'>Tout décocher</a>" ;
		echo "</th>\n" ;
		$i = 0 ;
		while ( $enr = mysqli_fetch_assoc($res) )
		{   
			if ( $i != 0 ) {
				echo "<tr>\n" ;
			}
			if ( @in_array($enr["id_dossier"], $messagerie["destinataires"]) ) 
			{
				echo "<td style='width: 1em' class='aex'>" ;
				echo "<input type='checkbox' " ;
				echo "name='destinataires[]' id='".$enr["id_dossier"]."' " ;
				echo "checked='checked' " ;
				echo "value='".$enr["id_dossier"]."' /></td>\n" ;
				echo "<td class='aex'><label class='bl' for='".$enr["id_dossier"]."'>" ;
				echo identite($enr, TRUE) ;
				echo "</label></td>\n" ;
				echo "<td class='aex'>" ;
				if ( $enr["id_imputation"] == "" ) {
					echo "Pré-inscrit" ;
				}
				else {
					echo "Inscrit" ;
				}
				echo "</td>\n" ;
				echo "<td class='aex'>".$enr["lieu_examen"]."</td>\n" ;
				echo "<td class='aex'>" ;
				if ( $enr["id_imputation"] != "" ) {
					echo "<span class='".$ETAT_DOSSIER_IMG_CLASS[$enr["etat_dossier"]]."'>" ;
					echo $ETAT_DOSSIER[$enr["etat_dossier"]]."</span>" ;
				}
				echo "</td>\n" ;
			}
			else {
				echo "<td style='width: 1em'>" ;
				echo "<input type='checkbox' " ;
				echo "name='destinataires[]' id='".$enr["id_dossier"]."' " ;
				echo "value='".$enr["id_dossier"]."' /></td>\n" ;
				echo "<td><label class='bl' for='".$enr["id_dossier"]."'>" ;
				echo identite($enr, TRUE) ;
				echo "</label></td>\n" ;
				echo "<td>" ;
				if ( $enr["id_imputation"] == "" ) {
					echo "Pré-inscrit" ;
				}
				else {
					echo "Inscrit" ;
				}
				echo "</td>\n" ;
				echo "<td>".$enr["lieu_examen"]."</td>\n" ;
				echo "<td>" ;
				if ( $enr["id_imputation"] != "" ) {
					echo "<span class='".$ETAT_DOSSIER_IMG_CLASS[$enr["etat_dossier"]]."'>" ;
					echo $ETAT_DOSSIER[$enr["etat_dossier"]]."</span>" ;
				}
				echo "</td>\n" ;
			}
			echo "</tr>\n" ;
			$i++ ;
		}
	}






	// from
	echo "<tr>\n" ;
	echo "<th>Expéditeur&nbsp;:</th>\n" ;
	echo "<td colspan='5'>";
	if ( intval($_SESSION["id"]) < 3 ) {
		echo "Agence universitaire de la Francophonie" ;
	}
	else {
		echo "MOOC" ;
	}
	echo " &lt;" ;
	if ( EMAIL_FROM_TOUJOURS ) {
		echo "<del class='barre'>".$courriel."</del> " ;
		echo EMAIL_FROM ;
	}
	else {
		echo $courriel ;
	}
	echo "&gt;</td>\n" ;
	echo "</tr>\n" ;
	// reply to
	echo "<tr>\n" ;
	echo "<th>Adresse de retour&nbsp;:</th>\n" ;
	echo "<td colspan='5'>" ;
	if ( EMAIL_SENDER_TOUJOURS ) {
		echo "<del class='barre'>".$courriel."</del> " ;
		echo EMAIL_SENDER ;
	}
	else {
		echo $courriel ;
	}
	echo "</td>\n" ;
	echo "</tr>\n" ;
	// reply to
	echo "<tr>\n" ;
	echo "<th>Réponse à&nbsp;:</th>\n" ;
	echo "<td colspan='5'>" . $courriel . "</td>\n" ;
	echo "</tr>\n" ;

	// cc
	echo "<tr>\n" ;
	echo "<th><label for='cc'>Copie à&nbsp;:</label><br />" ;
//	echo "<a href='#' onClick='document.forms[0].cc.value=\"".$courriel."\"'>Copie à l'expéditeur</a></th>\n" ;
	echo "<a href='javascript:copie()'>Copie à l'expéditeur</a></th>\n" ;
	echo "<td colspan='5'><input type='text' id='cc' name='cc' size='80' maxlength='250' " ;
	echo "value=\""
		. ( isset($messagerie["cc"]) ? $messagerie["cc"] : "" )
		. "\" " ;
	echo "/></td>\n" ;
	echo "</tr>\n" ;
	// commentaire
	echo "<tr>\n" ;
	echo "<th><label for='commentaire'>Commentaire&nbsp;:<br />" ;
	echo "<span class='normal'>Affiché seulement dans la<br /> liste des courriels envoyés</span></label></th>\n" ;
	echo "<td colspan='5'><textarea name='commentaire' id='commentaire' " ;
	echo "rows='2' cols='90'>" ;
	echo ( isset($messagerie["commentaire"]) ? $messagerie["commentaire"] : "" ) ;
	echo "</textarea></td>\n" ;
	echo "</tr>\n" ;
	// subject
	echo "<tr>\n" ;
	echo "<th><label for='subject'>Sujet&nbsp;:</label></th>\n" ;
	echo "<td colspan='5'><input type='text' id='subject' name='subject' " ;
	echo "size='80' " ;
	echo "value=\""
		. ( isset($messagerie["subject"]) ? $messagerie["subject"] : "" )
		. "\" " ;
	echo "/></td>\n" ;
	echo "</tr>\n" ;
	// body
	echo "<tr>\n" ;
	echo "<th><label for='body'>Message&nbsp;:</label></th>\n" ;
	echo "<td colspan='5'><textarea name='body' id='body' " ;
	echo "rows='20' cols='90'>" ;
	echo ( isset($messagerie["body"]) ? $messagerie["body"] : "" ) ;
	echo "</textarea></td>\n" ;
	echo "</tr>\n" ;
	// attach
	echo "<tr>\n" ;
	echo "<th rowspan='3'>Fichiers joints&nbsp;:</th>\n" ;
	echo "<td colspan='5'>";

	$chemin = "/attachements/".$messagerie["promotion"]."/" ;

	if ( isset($messagerie["nbAttachements"]) AND (count($messagerie["nbAttachements"]) > 0) )
	{
		$i = 0 ;
		foreach($messagerie["attachements"] as $attach) {
			if ( $i == 0 ) {
				$liste_attachements = "$attach" ;
			}
			else {
				$liste_attachements .= ", " . "$attach" ;
			}
			$i++ ;
		}
	
		$req = "SELECT * FROM attachements
			WHERE ref_courriel=0
			AND ref_session=".$messagerie["promotion"] ;
		if ( $liste_attachements != "" ) {
			$req .= " AND id_attachement IN ($liste_attachements)" ;
		}
		$req .= " ORDER BY id_attachement" ;
		$res = mysqli_query($cnx, $req) ;
		if ( mysqli_num_rows($res) != 0 ) {
			echo "<ul>\n" ;
			while ( $enr = mysqli_fetch_assoc($res) ) {
				echo "<li>" ;
				echo "<strong>" ;
				echo "<a href='".$chemin.$enr["nom"]."'>".$enr["nom"]."</a>" ;
				echo "</strong>" ;
				echo " (".intval($enr["taille"]/1024.0)."ko) " ;
				echo "<a href='supprimer.php?id=".$enr["id_attachement"]."'>Supprimer</a>" ;
				echo "</li\n>" ;
			}
			echo "</ul>\n" ;
		}
	}
	echo "<input type='submit' style='font-weight:bold;' " ;
	if ( isset($messagerie["nbAttachements"]) AND (count($messagerie["nbAttachements"]) > 0) ) {
		echo "name='attachement' value='Joindre un autre fichier' /></p>\n" ;
	}
	else {
		echo "name='attachement' value='Joindre un fichier' /></p>\n" ;
	}
	echo "</td>" ;
	echo "</tr>\n" ;
	echo "</table>\n" ;
	
	echo "<p class='c'>" ;
	echo "<input type='submit' style='font-weight:bold;' " ;
	echo "name='envoi' value='Envoyer ' /></p>\n" ;
	echo "</form>\n" ;
}

function verification_courriel($messagerie)
{
	$erreurs = array() ;
	// Aucun destinataire
	if ( count($messagerie["destinataires"]) == 0 ) {
		$erreurs[] = "Votre courriel doit avoir au moins un destinataire&nbsp;!";
	}
	// subject vide
	if ( trim($messagerie["subject"]) == "" ) {
		$erreurs[] = "Le champ «&nbsp;Sujet&nbsp;» est obligatoire." ;
	}
	// body vide
	if ( trim($messagerie["body"]) == "" ) {
		$erreurs[] = "Le champ «&nbsp;Message&nbsp;» est obligatoire." ;
	}
	if ( trim($messagerie["cc"]) != "" ) {
		$CC = trim($messagerie["cc"]) ;
		$tabCC = explode(",", $CC) ;
		if ( count($tabCC)>1 ) {
			foreach($tabCC AS $cc) {
				if (filter_var(trim($cc), FILTER_VALIDATE_EMAIL)) {
				}
				else {
					$erreurs[] = "Le champ «&nbsp;Copie à&nbsp;» est invalide : $cc" ;
				}
			}
		}
		else {
			if (filter_var($CC, FILTER_VALIDATE_EMAIL)) {
			}
			else {
				if ( substr_count($CC, "@") > 1 ) {
					$erreurs[] = "Le champ «&nbsp;Copie à&nbsp;» est invalide. Plusieurs adresses doivent être séparées par une virgule." ;
				}
				else {
					$erreurs[] = "Le champ «&nbsp;Copie à&nbsp;» est invalide." ;
				}
			}
		}
	}
	return $erreurs ;
}

?>
