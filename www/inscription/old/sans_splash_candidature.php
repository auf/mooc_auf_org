<?php
/*
Dépot initial de la candidature (insert) :   formulaire = e1
	Erreurs => réaffichage
	Sinon, INSERT, envoi mail
Modification d'un dossier (update) :   formulaire = maj
*/
require_once("inc_config.php") ;
include_once("inc_html.php") ;
require_once("inc_noms.php") ;

$message_aide = "
<p>Votre numéro de dossier et votre mot de passe vous permettent de modifier votre dossier jusqu'à la date de clôture des candidatures.
<br />Vous pouvez, en cliquant sur un des boutons ci-dessous&nbsp;:</p>
<ul>
<li>Voir votre dossier de candidature tel qu'il sera vu par les sélectionneurs qui l'examineront.<br />
	Vous pouvez aussi l'imprimer (Menu «&nbsp;Fichier&nbsp;» de votre navigateur, puis «&nbsp;Imprimer&nbsp;»).</li>
<li><strong>Envoyer un </strong>(nouveau)<strong> courriel</strong> contenant votre numéro de dossier et votre mot de passe
	à votre adresse électronique (utile si vous la modifiez).</li>
<li><strong>Joindre un fichier</strong> à votre dossier, par exemple une lettre de recommandations, ou supprimer un fichier déjà joint.</li>
<li><strong>Modifier</strong> vos réponses aux questions auxquelles vous avez répondu.</li>
</ul>
<form method='post' action='candidature.php'>
<p><span class='erreur'>Quittez votre navigateur quand vous aurez fini pour éviter que quelqu'un d'autre que vous ne modifie votre dossier.</span>
Fermer la fenêtre ou l'onglet courant suffit, il est inutile de fermer toutes les fenêtres ou tous les onglets de votre navigateur.
</p>
" ;

//
// Enregistrement d'un dossier encore inexistant
//
if ( $_POST["formulaire"] != "maj" )
{
	$choisir_avant = $dtd1
		. "<title>Candidature</title>"
		. $dtd2
		. "<div style='padding: 0.5em; '>\n"
		. $logoAufFoad
		. "<h1 class='noprint'>Candidature en ligne</h1>\n"
		. "<p class='c erreur'>" 
		. "Pour déposer une candidature, vous devez commencer par "
		. "<a href='http://foad.refer.org'>choisir une formation</a>."
		. "</p>\n"
//	. "<pre>"
//	. print_r($_POST)
//	. print_r($_FILES)
//	. "</pre>\n"
		. $end ;
	
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
	
	$requete = "SELECT atelier.intitule, universite, session.intit_ses, candidatures
		FROM atelier, session
		WHERE session.id_session=$id_session
		AND session.ref_atelier=atelier.id_atelier" ;
	$resultat = mysqli_query($cnx, $requete) ;
	
	if ( @mysqli_num_rows($resultat) == 0 )
	{
		echo $choisir_avant ;
		deconnecter($cnx) ;
		exit() ;
	}
	
	$enregistrement = mysqli_fetch_assoc($resultat) ;
	$intitule = $enregistrement["intitule"] ;
	$universite = $enregistrement["universite"] ;
	$intit_ses = $enregistrement["intit_ses"] ;
	
	$haut_de_page = $dtd1
		. "<title>Candidature</title>"
		. $dtd2
		. "<div style='padding: 0.5em; '>\n"
		. $logoAufFoad
		. "<h1 class='noprint'>Candidature en ligne</h1>\n"
		. "<h3 class='c' style='margin-bottom: 0; font-size: normal;'>$universite</h3>\n"
		. "<h1 style='margin-bottom: 0'>$intitule</h1>\n"
		. "<p class='c' style='margin-top: 0;'>"
		. "<strong>Promotion : $intit_ses</strong></p>\n" ;
	
	if ( $enregistrement["candidatures"] != "Oui" )
	{
		echo $haut_de_page ;
		echo "<p class='c erreur'>Les candidatures sont closes</p>\n" ;
		echo $end;
		deconnecter($cnx) ;
		exit() ;
	}
	
	/*
	while (list($key, $val) = each($_POST)) {
	   echo "$key => $val<br />";
	}
	*/
	
	include_once("inc_pays.php") ;
	include_once("inc_guillemets.php") ;
	include_once("inc_formulaire_candidature.php") ;
	include_once("fonctions_formulaire_candidature.php") ;
	
	include_once("inc_date.php") ;
	include_once("inc_etat_dossier.php") ;
	include_once("inc_dossier.php") ;
	
	// Traitement des guillemets
	// Et en même temps, traitement des noms
	unset($T) ;
	while ( list($key, $val) = each($_POST) ) {
		if ( ($key == "nom") OR ($key == "nom_jf") ) {
			$T[$key] = trim(enleve_guillemets(nom($val))) ;
		}
		else {
			$T[$key] = trim(enleve_guillemets($val)) ;
		}
	}
	
	//
	// Formulaire posté
	//
	if ( $_POST["formulaire"] == "e1" )
	{
		include_once("questions.php") ;
		include_once("controle_formulaire_candidature.php") ;
		include_once("controle_signature_candidature.php") ;
		include_once("controle_questions.php") ;
		include_once("controle_erreurs.php") ;
	
		// Il y a des erreurs
		if ( $erreurs )
		{
			echo $haut_de_page ;
			echo $message_erreur ;
			echo $CONSIGNES ;
			?><form action="candidature.php" method="post"><?php
			include_once("formulaire_candidature.php") ;
			include_once("formulaire_questions.php") ;
			include_once("signature_candidature.php") ; 
			echo "<input type='hidden' name='id_session' value='$id_session' />\n" ;
			?><input type="hidden" name="formulaire" value="e1" />
			<p class='c'><strong><input type="submit" name='submit' value="Valider" /></strong></p>
			</form><?php
		}
		// Il n'y a pas d'erreur : enregistrement
		else {
			include_once("insert_candidature.php") ; // $email, $id_session, $id_dossier proviennent de là
			echo $haut_de_page ;
			echo "<p class='c msgok'>Votre candidature a été enregistrée.</p>\n" ;
			echo "<p class='c msgok'>Un courrier électronique contenant votre numéro de dossier et votre mot de passe<br />
vous a été envoyé à <code>$email</code>.<br />
(Vérifier, le cas échéant, le dossier de courrier indésirable (<i>spam</i>) de votre service de messagerie.)
</p>\n" ;
			echo $message_aide ;
			?><form action="candidature.php" method="post"><?php
			echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
			echo "<input type='hidden' name='id_session' value='$id_session' />\n" ;
			echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
			echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
			echo "<div class='apercu'>\n" ;
			// On refait ce qu'on a défait dans insert_candidature.php...
			reset($T) ;
			while ( list($key, $val) = each($T) ) {
				$T[$key] = trim(enleve_guillemets($val)) ;
			}
//			$T["id_candidat"] = $id_candidat ;
			affiche_dossier($T, array(), $cnx, FALSE, FALSE, FALSE, TRUE) ;
			echo "</div>\n" ;
			echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
			?></form><?php
		}
	}
	//
	// Arrivée dans le formulaire
	//
	else 
	{
		echo $haut_de_page ;
		echo $CONSIGNES ;
		?><form action="candidature.php" method="post"><?php
		include_once("formulaire_candidature.php") ;
		include_once("questions.php") ;
		include_once("formulaire_questions.php") ;
		include_once("signature_candidature.php") ; 
		echo "<input type='hidden' name='id_session' value='$id_session' />\n" ;
		?><input type="hidden" name="formulaire" value="e1" />
		<p class='c'><strong><input type="submit" name='submit' value="Valider" /></strong></p>
		</form><?php
		echo $end ;
		deconnecter($cnx) ;
	}
}







//
// Mise à jour d'une dossier existant
//
else
{
	// Retour à l'identification
	if ( !isset($_POST["id_dossier"]) OR !isset($_POST["pwd"]) )
	{
		header("Location: /candidature/") ;
		exit() ;
	}

	// Candidature initiale FIXME pas grave
	if ( $_POST["candidature"] == "candidature" ) {
		$titre = "Candidature en ligne" ;
	}
	else {
		$titre = "Mise à jour d'un dossier de candidature" ;
	}
	$haut_de_page = $dtd1
		. "<title>$titre</title>"
		. $dtd2
		. "<div style='margin: 0.5em'>\n"
		. $logoAufFoad ;
	

	$id_dossier = trim($_POST["id_dossier"]) ;
	$pwd = $_POST["pwd"] ;
	
	include_once("inc_mysqli.php") ;
	$cnx = connecter() ;
	
	$req = "SELECT * FROM dossier, session, atelier
		WHERE id_dossier=$id_dossier 
		AND pwd='$pwd'
		AND dossier.id_session=session.id_session
		AND atelier.id_atelier=session.ref_atelier" ;
	$res = mysqli_query($cnx, $req) ;
	if ( @mysqli_num_rows($res) == 0 )
	{
		header("Location: /candidature/?erreur=1") ;
		deconnecter($cnx) ;
		exit() ;
	}

	$ligne = mysqli_fetch_assoc($res) ;
	$intitule = $ligne["intitule"] ;
	$universite = $ligne["universite"] ;
	$intit_ses = $ligne["intit_ses"] ;
	
	echo $haut_de_page ;
	echo "<h1 class='noprint'>$titre</h1>\n" ;
	echo "<h3 class='c' style='margin-bottom: 0;'>".$ligne["universite"]."</h3>\n" ;
	echo "<h1 style='margin-bottom: 0;'>".$ligne["intitule"]."</h1>\n" ;
	echo "<p class='c' style='margin-top: 0;'><strong>Promotion : " ;     
	echo $ligne["intit_ses"]."</strong></p>" ;
	
	if ( $ligne["candidatures"] != "Oui" ) {
	    echo "<p class='c erreur'>Les candidatures sont closes</p>\n" ;
		echo $end ;
		deconnecter($cnx) ;
		exit() ;
	}
	
	include_once("inc_guillemets.php") ;
	include_once("inc_pays.php") ;
	include_once("inc_formulaire_candidature.php") ;
	include_once("fonctions_formulaire_candidature.php") ;
	
	include_once("inc_date.php");
	include_once("inc_etat_dossier.php") ;
	include_once("inc_dossier.php");
	
	//
	// Arrivée dans le formulaire de modification
	//
	if ( $_POST["submit"] == "Modifier" )
	{
		$req = "SELECT * from dossier, candidat, session, atelier
			WHERE id_dossier=$id_dossier
			AND dossier.id_candidat=candidat.id_candidat
			AND dossier.id_session=session.id_session
			AND atelier.id_atelier=session.ref_atelier" ;
		$res = mysqli_query($cnx, $req) ;
		$ligne = mysqli_fetch_assoc($res) ;
	
		while ( list($key, $val) = each($ligne) ) {
			$T[$key] = $val ;
		}
		$T["verif_email1"] = $T["email1"] ;
	
		?><form action="candidature.php" method="post"><?php
	
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
			$T["pays_dip$i"] = $ligne["pays_dip"] ;
			$i++ ;
		}
	
		include_once("formulaire_candidature.php") ;
	
		// Questions supplémentaires
		include_once("questions.php") ;
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
			include_once("formulaire_questions.php") ;
		}
	
		include_once("signature_candidature.php") ;
	
		echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
		echo "<input type='hidden' name='id_session' value='".$T["id_session"]."' />\n" ;
		echo "<input type='hidden' name='id_candidat' value='".$T["id_candidat"]."' />\n" ;
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
		// Traitement des guillemets
		while ( list($key, $val) = each($_POST) ) {
			$T[$key] = trim(enleve_guillemets($val)) ;
		}
	
		include_once("questions.php") ;
		include_once("controle_formulaire_candidature.php") ;
		include_once("controle_signature_candidature.php") ;
		include_once("controle_questions.php") ;
		include_once("controle_erreurs.php") ;
	
		if ( $erreurs )
		{
			echo $message_erreur ;
			?><form action="candidature.php" method="post"><?php
			include_once("formulaire_candidature.php") ;
			include_once("formulaire_questions.php") ;
			include_once("signature_candidature.php") ;
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
			echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
			echo "<input type='hidden' name='id_dossier' value='".$T["id_dossier"]."' />\n" ;
			echo "<input type='hidden' name='id_candidat' value='".$T["id_candidat"]."' />\n" ;
			echo "<input type='hidden' name='pwd' value='".$T["pwd"]."' />\n" ;
			?>
			<p class='c'><strong><input type="submit" name='submit' value="Enregistrer" /></strong></p>
			</form><?php
		}
		else
		{
			include_once("update_candidature.php") ;
			echo "<p class='c msgok'>Votre candidature a été modifiée.</p>\n" ;
			echo $message_aide ;
			?><form action="candidature.php" method="post"><?php
			echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
			echo "<input type='hidden' name='id_candidat' value='".$T["id_candidat"]."' />\n" ;
			echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
			echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
			echo "<div class='apercu'>\n" ;
			// On refait ce qu'on a défait dans update_candidature.php...
			// FIXME ?
			reset($T) ;
			while ( list($key, $val) = each($T) ) {
				$T[$key] = $val ;
			}
			affiche_dossier($T, array(), $cnx, FALSE, FALSE, TRUE, TRUE) ;
			echo "</div>\n" ;
			echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
			?></form><?php
		}
	}
	//
	//
	//
	else if ( $_POST["submit"] == "Envoyer un courriel" )
	{
		$req = "SELECT dossier.*, candidat.*
		    FROM dossier JOIN candidat ON dossier.id_candidat=candidat.id_candidat
		    WHERE dossier.id_dossier=$id_dossier" ;
		$res = mysqli_query($cnx, $req) ;
		$T = mysqli_fetch_assoc($res) ;

		require_once("inc_aufPhpmailer.php") ;
		$mail = new aufPhpmailer() ;
		$mail->From = "foad@auf.org" ;
		$mail->FromName = "Agence universitaire de la Francophonie" ;
		$mail->AddReplyTo("foad@auf.org", "") ;
		$mail->Sender = "foad@auf.org" ;
		$mail->Subject = "Votre candidature sur le site FOAD de l'AUF" ;
		$mail->Body = "Bonjour " . $T["prenom"] . " " . $T["nom"] . ",

Le numéro de dossier et le mot de passe de votre candidature à
$intitule
(Promotion $intit_ses)
sont les suivants :

  Numéro de dossier : $id_dossier
  Mot de passe      : $pwd

Vous pouvez modifier votre dossier jusqu'à la date de clôture des
candidatures en vous rendant sur
https://foad.refer.org/candidature/

Cordialement,

Agence universitaire de la Francophonie
http://foad.refer.org
" ;
		$mail->AddAddress($T["email1"]) ;
		if ( $mail->Send() ) {
		    echo "<p class='c msgok'>Un courriel contenant votre numéro de dossier et votre mot de passe vous a été envoyé à <code>".$T["email1"]."</code><br />
(Vérifier, le cas échéant, le dossier de courrier indésirable (<i>spam</i>) de votre service de messagerie.)
</p>" ;
		}
		$mail->ClearAddresses() ;

		echo $message_aide ;
		?><form action="candidature.php" method="post"><?php
		echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
		echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
		echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
		echo "<div class='apercu'>\n" ;
		affiche_dossier($T, array(), $cnx, FALSE, FALSE, TRUE, TRUE) ;
		echo "</div>\n" ;
		echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
		?></form><?php
	}
	//
	// Formulaire pour joindre un fichier
	//
	else if ( $_POST["submit"] == "Joindre un fichier" )
	{
		include_once("formulaire_upload_pj.php") ;
	}
	//
	// Fichier joint à traiter
	//
	else if ( $_POST["submit"] == "Joindre ce fichier" )
	{
/*
		if ( $_FILES["fichier"]["error"] == 0 )
		{
		}
*/

		if ( !isset($_FILES["fichier"]) )
		{
			echo "<p class='c erreur'>Votre fichier est beaucoup trop volumineux. "
		        . " Sa taille doit être inférieure à 2Mo.</p>\n" ;
			include_once("formulaire_upload_pj.php") ;
		}
		else if ( ($_FILES["fichier"]["error"] == 2) OR ($_FILES["fichier"]["error"] == 1) )
		{
			echo "<p class='c erreur'>Votre fichier est trop volumineux. "
		        . " Sa taille doit être inférieure à 2Mo (Erreur "
				. $_FILES["fichier"]["error"]
				.").</p>\n" ;
			include_once("formulaire_upload_pj.php") ;
		}
		else if ( $_FILES["fichier"]["error"] == 4 )
		{
			echo "<p class='c erreur'>Vous devez joindre un fichier.</p>\n" ;
			include_once("formulaire_upload_pj.php") ;
		}
		else if ( $_POST["confirmation_pj"] != "oui" )
		{
			echo "<p class='c erreur'>Vous devez certifier l'exactitude des informations " ;
			echo "contenues dans votre fichier (en cochant la case).</p>\n" ;
			include_once("formulaire_upload_pj.php") ;
		}
		else if ( $_FILES["fichier"]["error"] != 0 )
		{
			echo "<p class='c erreur'>Erreur ". $_FILES["fichier"]["error"] ;
			echo " dans le chargement du fichier.</p>\n" ;
			include_once("formulaire_upload_pj.php") ;
		}
		// Pas d'erreur
		else
		{
			$chemin = $_SERVER["DOCUMENT_ROOT"] . "../pj/" . $id_dossier ;
			if ( !is_dir($chemin) ) {
				mkdir($chemin) ;
			}

			include_once("inc_traitements_caracteres.php") ;
			$nom = $_FILES["fichier"]["name"] ;
			$nom = traitementNomFichier($nom) ;
			
			while ( is_file($chemin."/".$nom) ) {
				$nom = "_" . $nom ;
			}

			if ( move_uploaded_file($_FILES["fichier"]["tmp_name"], $chemin."/".$nom) )
			{
				$req = "INSERT INTO pj(ref_dossier, fichier, taille)
					VALUES($id_dossier, '$nom', ". $_FILES["fichier"]["size"].")" ;
				mysqli_query($cnx, $req) ;

				$req = "SELECT dossier.*, candidat.*
				    FROM dossier JOIN candidat ON dossier.id_candidat=candidat.id_candidat
				    WHERE dossier.id_dossier=$id_dossier" ;
				$res = mysqli_query($cnx, $req) ;
				$T = mysqli_fetch_assoc($res) ;
			
				echo "<p class='c msgok'>Votre fichier est joint à votre dossier.</p>\n" ;
				echo $message_aide ;
				?><form action="candidature.php" method="post"><?php
				echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
				echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
				echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
				echo "<div class='apercu'>\n" ;
				affiche_dossier($T, array(), $cnx, FALSE, FALSE, TRUE, TRUE) ;
				echo "</div>\n" ;
				echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
				?></form><?php
			}
			else
			{
				echo "<p class='c erreur'>Erreur dans le chargement du fichier.</p>\n" ;
				include_once("formulaire_upload_pj.php") ;
			}
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
					$req = "DELETE FROM pj WHERE id_pj=$cle" ;
					mysqli_query($cnx, $req) ;
					$chemin = $_SERVER["DOCUMENT_ROOT"] . "../pj/" . $id_dossier ."/" ;
					unlink($chemin . $row["fichier"]) ;
					@rmdir($chemin) ;
					echo "<p class='c msgok'>Votre fichier a été supprimé.</p>\n" ;
				}
			} 
		}
		$req = "SELECT dossier.*, candidat.*
		    FROM dossier JOIN candidat ON dossier.id_candidat=candidat.id_candidat
		    WHERE dossier.id_dossier=$id_dossier" ;
		$res = mysqli_query($cnx, $req) ;
		$T = mysqli_fetch_assoc($res) ;
	
		echo $message_aide ;
		?><form action="candidature.php" method="post"><?php
		echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
		echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
		echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
		echo "<div class='apercu'>\n" ;
		affiche_dossier($T, array(), $cnx, FALSE, FALSE, TRUE, TRUE) ;
		echo "</div>\n" ;
		echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
		?></form><?php
	}
	//
	// Arrivée après identification
	//
	else
	{
		$req = "SELECT dossier.*, candidat.*
		    FROM dossier JOIN candidat ON dossier.id_candidat=candidat.id_candidat
		    WHERE dossier.id_dossier=$id_dossier" ;
		$res = mysqli_query($cnx, $req) ;
		$T = mysqli_fetch_assoc($res) ;
	
		echo $message_aide ;
		?><form action="candidature.php" method="post"><?php
		echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
		echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
		echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
		echo "<div class='apercu'>\n" ;
		// Date de naissance
		$tab_naissance = explode("-", $T["naissance"]) ;
		$T["annee_n"] = $tab_naissance[0] ;
		$T["mois_n"] = $tab_naissance[1] ;
		$T["jour_n"] = $tab_naissance[2] ;
		affiche_dossier($T, array(), $cnx, FALSE, FALSE, TRUE, TRUE) ;
		echo "</div>\n" ;
		echo "<p class='c'><strong><input type='submit' name='submit' value='Modifier' /></strong></p>\n" ;
		?></form><?php
	}
	deconnecter($cnx) ;
	echo $end ;
}
?>
