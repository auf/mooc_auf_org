<?php
require_once("inc_config.php") ;
include_once("inc_html.php") ;
include_once("inc_date.php") ;
include_once("inc_inscription.php") ;

// Maintenance
if ( SITE_EN_LECTURE_SEULE )
{
	echo $dtd1
		. "<title>Inscription - Maintenance</title>"
		. $dtd2Public
		. enteteAufMooc("Inscription")
		. "<h2><p class='c erreur'>" . EN_MAINTENANCE . "</p></h2>\n"
		. $endPublic ;
}
else
{
	/*
	Dépot initial de la inscription (insert) :   formulaire = e1
		Erreurs => réaffichage
		Sinon, insert, envoi mail
	Modification d'un dossier (update) :   formulaire = maj
	*/
	
	//
	// Enregistrement d'un dossier encore inexistant
	//
	if	(
			!isset($_POST["formulaire"])
			OR ( isset($_POST["formulaire"]) AND ($_POST["formulaire"] != "maj") ) 
		)
	{
		$choisir_avant = $dtd1
			. "<title>Inscription</title>"
			. $dtd2Public
			. enteteAufMooc("Inscription")
			. "<p class='c erreur'>" 
			. "Pour déposer une inscription, vous devez commencer par "
			. "<a href='http://".URL_DOMAINE_PUBLIC."'>choisir une formation</a>."
			. "</p>\n"
			. $endPublic ;
		
		if ( isset($_GET["id_session"]) ) {
			$id_session = $_GET["id_session"] ;
		}
		else if ( isset($_POST["id_session"]) ) {
			$id_session = $_POST["id_session"] ;
		}
		else {
			unset($id_session) ;
		}
	
		if ( !isset($id_session) )
		{
			echo $choisir_avant ;
			exit() ;
		}
		
		include_once("inc_mysqli.php") ;
		$cnx = connecter() ;
		
		$req = "SELECT atelier.intitule, universite, ref_institution,
			session.*
			FROM atelier, session
			WHERE session.id_session=$id_session
			AND session.ref_atelier=atelier.id_atelier" ;
		$res = mysqli_query($cnx, $req) ;
		if ( @mysqli_num_rows($res) == 0 )
		{
			echo $choisir_avant ;
			deconnecter($cnx) ;
			exit() ;
		}
		$tabInscription = mysqli_fetch_assoc($res) ;
		
		$haut_de_page = $dtd1
			. "<title>Inscription</title>"
			. $dtd2Public
			. enteteAufMooc("Inscription")
			. titreInscription($tabInscription) ;
		
		if ( dateOuiNon($tabInscription["inscriptions_deb"], $tabInscription["inscriptions_fin"]) == "Non" )
		{
			echo $haut_de_page ;
			echo "<p class='c erreur'>Les inscriptions sont closes</p>\n" ;
			echo $endPublic ;
			deconnecter($cnx) ;
			exit() ;
		}
		
		/*
		if ( $id_session == 1 ) {
			while (list($key, $val) = each($_POST)) {
			   echo "$key => $val<br />";
			}
		}
		*/
		
		include_once("inc_pays.php") ;
		include_once("inc_guillemets.php") ;
		include_once("inc_formulaire_inscription.php") ;
		include_once("fonctions_formulaire_inscription.php") ;
		//include_once("inc_etat_dossier.php") ;
		include_once("inc_dossier.php") ;
		
		// Traitement des guillemets
		// Et en même temps, traitement des noms
		unset($T) ;
		$T = $tabInscription ;
		reset($_POST) ;
		while ( list($key, $val) = each($_POST) ) {
			$T[$key] = trim(enleve_guillemets($val)) ;
		}
		
		//
		// Formulaire posté
		//
		if ( isset($_POST["formulaire"]) AND ($_POST["formulaire"] == "e1") )
		{
			include_once("controle_formulaire_inscription.php") ;
		
			// Il y a des erreurs
			if ( $erreurs )
			{
				echo $haut_de_page ;
				echo $message_erreur ;
				echo consignesPreInscription($tabInscription) ;
				?><form action="inscription.php" method="post"><?php
				include_once("formulaire_inscription.php") ;
				echo "<input type='hidden' name='id_session' value='$id_session' />\n" ;
				?><input type="hidden" name="formulaire" value="e1" />
				<p class='c'><strong><input type="submit" name='submit' value="Valider" /></strong></p>
				</form><?php
			}
			// Il n'y a pas d'erreur : enregistrement
			else {
				include_once("insert_inscription.php") ; // $email, $id_session, $id_dossier proviennent de là
				echo $haut_de_page ;
				echo "<div class='msgok'>\n" ;
				echo "<p>Votre dossier d'inscription a été enregistré.</p>\n" ;
				echo "<p>Un courrier électronique contenant votre numéro de dossier et votre mot de passe vous a été envoyé à : " ;
					echo "<code>$email</code><br />" ;
					echo "(Vérifiez, le cas échéant, le dossier de courrier indésirable (<i>spam</i>) de votre messagerie.)</p>\n" ;
				echo "</div>\n" ;
				echo consignesDossierInscription($tabInscription) ;
				?><form action="inscription.php" method="post"><?php
				echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
				echo "<input type='hidden' name='id_session' value='$id_session' />\n" ;
				echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
				echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
				echo "<div class='apercu'>\n" ;
				// On refait ce qu'on a défait dans insert_inscription.php...
				// FIXME ?
				reset($T) ;
				/*
				while ( list($key, $val) = each($T) ) {
					$T[$key] = trim(enleve_guillemets($val)) ;
				}
				*/
				$T["id_dossier"] = $id_dossier ;
				affiche_dossier($cnx, $T, FALSE, FALSE, FALSE, TRUE) ;
				echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
				echo "</div>\n" ;
				?></form><?php
			}
		}
		//
		// Arrivée dans le formulaire
		//
		else 
		{
			if ( FLAG_SPLASH AND ($_POST["splash"] != "splash") )
			{
				echo $haut_de_page ;
				echo splashInscription($id_session) ;
			}
			else
			{
				if ( isset($_GET["id_mooc"]) ) {
					$T["id_mooc"] = $_GET["id_mooc"] ;
				}

				echo $haut_de_page ;
				echo consignesPreInscription($tabInscription) ;
				?><form action="inscription.php" method="post"><?php
				include_once("formulaire_inscription.php") ;
				echo "<input type='hidden' name='id_session' value='$id_session' />\n" ;
				?><input type="hidden" name="formulaire" value="e1" />
				<p class='c'><strong><input type="submit" name='submit' value="Valider" /></strong></p>
				</form><?php
			}
		}
		echo $endPublic ;
		deconnecter($cnx) ;
	}
	
	
	
	
	
	
	
	//
	// Mise à jour d'une dossier existant
	//
	else
	{
		// Retour à l'identification
		if ( !isset($_POST["id_dossier"]) OR !isset($_POST["pwd"]) )
		{
			header("Location: /inscription/") ;
			exit() ;
		}
	
		// Candidature initiale FIXME pas grave
		if ( isset($_POST["candidature"]) AND ($_POST["candidature"] == "candidature") ) {
			$titre = "Inscription" ;
		}
		else {
			$titre = "Mise à jour d'un dossier d'inscription" ;
		}
		$haut_de_page = $dtd1
			. "<title>$titre</title>"
			. $dtd2Public
			. "<div style='margin: 0.5em'>\n"
			//. $logoAufFoad ;
			. enteteAufMooc($titre) ;
		
	
		$id_dossier = trim($_POST["id_dossier"]) ;
		$pwd = $_POST["pwd"] ;
		
		include_once("inc_mysqli.php") ;
		$cnx = connecter() ;
		
		$req = "SELECT * FROM atelier, session, dossier
			LEFT JOIN imputations ON ref_dossier=id_dossier
			WHERE id_dossier=$id_dossier 
			AND pwd='$pwd'
			AND dossier.ref_session=session.id_session
			AND session.ref_atelier=atelier.id_atelier" ;
		$res = mysqli_query($cnx, $req) ;
		if ( @mysqli_num_rows($res) == 0 )
		{
			header("Location: /inscription/?erreur=1") ;
			deconnecter($cnx) ;
			exit() ;
		}

		$tabInscription = mysqli_fetch_assoc($res) ;
		
		echo $haut_de_page ;
		echo titreInscription($tabInscription) ;
		
		// FIXME amélioration ?
		/*
		if ( $tabInscription["id_imputation"] != "" ) {
		    echo "<p class='c erreur'>Vous êtes déjà inscrit.</p>\n" ;
			echo $endPublic ;
			deconnecter($cnx) ;
			exit() ;
		}
		*/

		// 
		$dateCourante = date("Y-m-d", time()) ;
		// Jusqu'à la veille ou jusqu'au jour de l'examen inclus (cf aussi inc_inscription.php)
		//if ( $dateCourante >= $tabInscription["date_examen"]  )
		if ( $dateCourante > $tabInscription["date_examen"]  )
		{
		    echo "<p class='c erreur'>Les inscriptions sont closes.</p>\n" ;
			echo $endPublic ;
			deconnecter($cnx) ;
			exit() ;
		}
		
		include_once("inc_guillemets.php") ;
		include_once("inc_pays.php") ;
		include_once("inc_formulaire_inscription.php") ;
		include_once("fonctions_formulaire_inscription.php") ;
		
		include_once("inc_etat_dossier.php") ;
		include_once("inc_dossier.php");
		
		//
		// Arrivée dans le formulaire de modification
		//
		if ( $_POST["submit"] == "Modifier" )
		{
			$T = $tabInscription ;

			$T["verif_email"] = $T["email"] ;
			$tab_naissance = explode("-", $T["naissance"]) ;
			$T["annee_n"] = $tab_naissance[0] ;
			$T["mois_n"] = $tab_naissance[1] ;
			$T["jour_n"] = $tab_naissance[2] ;
			$tab_ident = explode("-", $T["ident_date"]) ;
			$T["annee_ident"] = $tab_ident[0] ;
			$T["mois_ident"] = $tab_ident[1] ;
			$T["jour_ident"] = $tab_ident[2] ;
		
			?><form action="inscription.php" method="post"><?php
			include_once("formulaire_inscription.php") ;
			echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
			echo "<input type='hidden' name='id_session' value='".$T["ref_session"]."' />\n" ;
			echo "<input type='hidden' name='id_dossier' value='".$T["id_dossier"]."' />\n" ;
			echo "<input type='hidden' name='pwd' value='".$T["pwd"]."' />\n" ;
			?>
			<p class='c'><strong><input type="submit" name='submit' value="Enregistrer" /></strong></p>
			</form><?php
		}
		//
		// Formulaire posté
		//
		else if ( $_POST["submit"] == "Enregistrer" )
		{
			//$T = array() ;
			$T = $tabInscription ; // On a besoin du parametrage du formulaire
			// Traitement des guillemets
			while ( list($key, $val) = each($_POST) ) {
				$T[$key] = trim(enleve_guillemets($val)) ;
			}
		
			include_once("controle_formulaire_inscription.php") ;
			if ( $erreurs )
			{
				echo $message_erreur ;
				?><form action="inscription.php" method="post"><?php
				include_once("formulaire_inscription.php") ;
				echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
				echo "<input type='hidden' name='id_dossier' value='".$T["id_dossier"]."' />\n" ;
				echo "<input type='hidden' name='pwd' value='".$T["pwd"]."' />\n" ;
				?>
				<p class='c'><strong><input type="submit" name='submit' value="Enregistrer" /></strong></p>
				</form><?php
			}
			else
			{
				include_once("update_inscription.php") ;
				echo consignesDossierInscription($T) ;
				?><form action="inscription.php" method="post"><?php
				echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
				echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
				echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
				echo "<div class='apercu'>\n" ;
				affiche_dossier($cnx, $T, FALSE, FALSE, TRUE, TRUE) ;
				echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
				echo "</div>\n" ;
				?></form><?php
			}
		}
		//
		//
		//
		else if ( $_POST["submit"] == "Envoyer un courriel" )
		{
			$T = $tabInscription ;
	
			require_once("inc_aufPhpmailer.php") ;
			$mail = new aufPhpmailer() ;
			$mail->From = EMAIL_FROM ;
			$mail->FromName = EMAIL_FROMNAME ;
			$mail->AddReplyTo(EMAIL_REPLYTO, "") ;
			$mail->Sender = EMAIL_SENDER ;
			$mail->Subject = "Votre inscription sur le site FOAD-MOOC de l'AUF" ;
			$mail->Body = "Bonjour " . $T["prenom"] . " " . $T["nom"] . ",
	
Le numéro de dossier et le mot de passe de votre inscription à
  " . $tabInscription["universite"] ."
  " . $tabInscription["intitule"] ."
  " . $tabInscription["intit_ses"] ."
sont les suivants :

  Numéro de dossier : $id_dossier
  Mot de passe      : $pwd

Vous pouvez modifier votre dossier jusqu'à la date de clôture des
inscriptions en vous rendant sur
https://" . URL_DOMAINE . "/inscription/

Cordialement,

".NOM_CONTACT."
http://" . URL_DOMAINE_PUBLIC . "

" . MESSAGE_AUTOMATIQUE ;
	
			$mail->AddAddress($T["email"]) ;
			if ( $mail->Send() ) {
			    echo "<div class='msgok'>\n" ;
				echo "<p>Un courriel contenant votre numéro de dossier et votre mot de passe " ;
				echo "vous a été envoyé à <code>".$T["email1"]."</code><br />" ;
				echo "(Vérifiez, le cas échéant, le dossier de courrier indésirable (<i>spam</i>) de votre messagerie.)</p>\n" ;
				echo "</div>\n" ;
			}
			$mail->ClearAddresses() ;
	
			echo consignesDossierInscription($tabInscription) ;
			?><form action="inscription.php" method="post"><?php
			echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
			echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
			echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
			echo "<div class='apercu'>\n" ;
			affiche_dossier($cnx, $T, FALSE, FALSE, TRUE, TRUE) ;
			echo "</div>\n" ;
			echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
			?></form><?php
		}
		//
		// Formulaire pour joindre un fichier
		//
		else if ( $_POST["submit"] == "Joindre un fichier" )
		{
			include_once("upload_pj.php") ;
		}
		//
		// Fichier joint à traiter
		//
		else if ( $_POST["submit"] == "Joindre ce fichier" )
		{
			require_once("class.upload/class.upload.php") ;
			$handle = new Upload($_FILES['fichier'], "fr_FR") ;
			$handle->allowed = array(
				'image/jpeg',
				'image/png',
				'application/pdf',
			) ;
			if ($handle->uploaded)
			{
				$chemin = $_SERVER["DOCUMENT_ROOT"] . "/../pj/" . $id_dossier ."/" ;
				/*
				// Inutile, car pris en charge par la classe
				if ( !is_dir($chemin) ) {
					mkdir($chemin) ;
				}
				*/
				/*
				$handle->image_resize = true ; // Redimensionner
				$handle->image_ratio = true ; // Conserver proprotions
				$handle->image_ratio_no_zoom_in = true ; // Si plus grand
				$handle->image_x = 2000 ;
				$handle->image_y = 2000 ;
				*/
				$handle->Process($chemin) ;
				if ($handle->processed)
				{
					$fichier = $handle->file_dst_name ;
					$taille = filesize($handle->file_dst_pathname) ;
					$req = "INSERT INTO pj(ref_dossier, fichier, mime, largeur, hauteur, taille)
						VALUES($id_dossier, '".$fichier."',
						'".$handle->file_src_mime."',
						'".$handle->image_dst_x."',
						'".$handle->image_dst_y."',
						'".$taille."')" ;
					mysqli_query($cnx, $req) ;
	
					$T = $tabInscription ;
				
					echo "<div class='msgok'><p>Le fichier <code> $fichier </code> est joint à votre dossier.</p></div>\n" ;
					echo consignesDossierInscription($tabInscription) ;
					?><form action="inscription.php" method="post"><?php
					echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
					echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
					echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
					echo "<div class='apercu'>\n" ;
					affiche_dossier($cnx, $T, FALSE, FALSE, TRUE, TRUE) ;
					echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
					echo "</div>\n" ;
					?></form><?php

					if ( $handle->file_is_image )
					{
						$handle->image_resize = true ; // Redimensionner
						$handle->image_ratio_no_zoom_in = true ; // Si plus grand
						$handle->image_ratio = true ; // Conserver proprotions
						$handle->image_x = 120 ;
						$handle->image_y = 120 ;
						$handle->Process($chemin) ;
						rename($chemin.$handle->file_dst_name, $chemin."zz_".$fichier) ;
					}

					$handle-> Clean();
				}
				else
				{
					echo "<div class='erreur'><p>" . $handle->error . "</p></div>\n" ;
					include_once("upload_pj.php") ;
				}
			}
			else
			{
				echo "<p class='c erreur'>" . $handle->error . "</p>\n" ;
				include_once("upload_pj.php") ;
			}
		}
		//
		// Suppression d'un fichier joint
		//
		else if ( $cle = array_search("Supprimer ce fichier", array_values($_POST)) )
		{
			$cles = array_keys($_POST) ;
			$cle = $cles[$cle] ;
			if ( is_numeric($cle) )
			{
				$req = "SELECT fichier, ref_dossier FROM pj WHERE id_pj=$cle" ;
				$res = mysqli_query($cnx, $req) ;
				if ( @mysqli_num_rows($res) == 1 ) {
					$row = mysqli_fetch_assoc($res) ;
					if ( $row["ref_dossier"] == $id_dossier ) {
						$fichier = $row["fichier"] ;
						$req = "DELETE FROM pj WHERE id_pj=$cle" ;
						mysqli_query($cnx, $req) ;
						$chemin = $_SERVER["DOCUMENT_ROOT"] . "/../pj/" . $id_dossier ."/" ;
						unlink($chemin . $row["fichier"]) ;
						unlink($chemin . "zz_" . $row["fichier"]) ;
						@rmdir($chemin) ;
						echo "<div class='msgok'><p>Le fichier <code> $fichier </code> a été supprimé.</p></div>\n" ;
					}
				} 
			}

			$T = $tabInscription ;
		
			echo consignesDossierInscription($tabInscription) ;
			?><form action="inscription.php" method="post"><?php
			echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
			echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
			echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
			echo "<div class='apercu'>\n" ;
			affiche_dossier($cnx, $T, FALSE, FALSE, TRUE, TRUE) ;
			echo "</div>\n" ;
			echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
			?></form><?php
		}
		//
		// Arrivée après identification
		//
		else
		{
			$T = $tabInscription ;
		
			echo consignesDossierInscription($tabInscription) ;
			?><form action="inscription.php" method="post"><?php
			echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
			echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
			echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
			echo "<div class='apercu'>\n" ;
			// Date de naissance
			$tab_naissance = explode("-", $T["naissance"]) ;
			$T["annee_n"] = $tab_naissance[0] ;
			$T["mois_n"] = $tab_naissance[1] ;
			$T["jour_n"] = $tab_naissance[2] ;
			$tab_ident = explode("-", $T["ident_date"]) ;
			$T["annee_ident"] = $tab_ident[0] ;
			$T["mois_ident"] = $tab_ident[1] ;
			$T["jour_ident"] = $tab_ident[2] ;
			affiche_dossier($cnx, $T, FALSE, FALSE, TRUE, TRUE) ;
			echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
			echo "</div>\n" ;
			?></form><?php
		}
		deconnecter($cnx) ;
		echo $endPublic ;
	}
} // else de if ( SITE_EN_LECTURE_SEULE )
/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
diagnostic() ;
*/
?>
