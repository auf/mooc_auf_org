<?php
include("inc_session.php") ;

if ( count($_POST) == 0 ) {
	$_SESSION["messagerie"]["action"] = "" ;
	header("Location: /messagerie/courriel.php") ;	
	exit ;
}

// Changement de promotion
if ( $_SESSION["messagerie"]["promotion"] != $_POST["promotion"] ) {
	unset($_SESSION["messagerie"]) ;
}

if ( (count($_POST["destinataires"]) == 0) AND ($_POST["ok"] == "ok") )
{
	unset($_SESSION["messagerie"]["destinataires"]) ;
}

while ( list($key, $val) = each($_POST) )
{
	if ( $key == "destinataires" ) {
		$_SESSION["messagerie"][$key] = $val ;
	}
	else if ( $key == "envoi" ) {
		$_SESSION["messagerie"]["action"] = "envoi" ;
	}
	else if ( $key == "dest" ) {
		$_SESSION["messagerie"]["action"] = "dest" ;
	}
	else if ( $key == "attachement" ) {
		$_SESSION["messagerie"]["action"] = "attachement" ;
	}
	else {
		$_SESSION["messagerie"][$key] = $val ;
	}
}

include("inc_messagerie.php") ;
$erreurs = verification_courriel($_SESSION["messagerie"]) ;
//
// Envoi, et pas d'erreur
//
if ( ( $_SESSION["messagerie"]["action"] == "envoi" )
	AND ( count($erreurs) == 0 ) ) 
{
	include("inc_mysqli.php") ;
	$cnx = connecter() ;
	// Enregistrement du courriel et récupération de son id
	$req = "INSERT INTO courriels
		(ref_session, etat, lieu_examen, etat_dossier, date, expediteur, cc, subject, body, commentaire)
		VALUES(".$_SESSION["messagerie"]["promotion"].", 
		'".$_SESSION["messagerie"]["etat"]."',
		'".mysqli_real_escape_string($cnx, $_SESSION["messagerie"]["lieu_examen"])."',
		'".$_SESSION["messagerie"]["etat_dossier"]."',
		'".date("Y-m-d")."',
		'".$_SESSION["courriel"]."',
		'".mysqli_real_escape_string($cnx, trim($_SESSION["messagerie"]["cc"]))."',
		'".mysqli_real_escape_string($cnx, trim($_SESSION["messagerie"]["subject"]))."',
		'".mysqli_real_escape_string($cnx, trim($_SESSION["messagerie"]["body"]))."',
		'".mysqli_real_escape_string($cnx, trim($_SESSION["messagerie"]["commentaire"]))."')" ;
	//echo $req ;
	//diagnostic() ;
	$res = mysqli_query($cnx, $req) ;
	$req = "SELECT LAST_INSERT_ID() AS N" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;
	$ref_courriel = $enr["N"] ;
	// Chemin vers les fichiers joints
	$chemin = $_SERVER["DOCUMENT_ROOT"] . "attachements/"
        . $_SESSION["messagerie"]["promotion"] . "/" ;

	// Début envoi de mail
	require_once("inc_aufPhpmailer.php") ;
	$mail = new aufPhpmailer() ;
	if ( EMAIL_FROM_TOUJOURS ) {
		$mail->From = EMAIL_FROM ;
	}
	else {
		$mail->From = $_SESSION["courriel"] ;
	}
	if ( intval($_SESSION["id"]) < 3 ) {
		$mail->FromName = "Agence universitaire de la Francophonie" ;
	}
	else {
		$mail->FromName = "MOOC" ;
	}
	if ( EMAIL_SENDER_TOUJOURS ) {
		$mail->Sender = EMAIL_SENDER ;
	}
	else {
		$mail->Sender = $_SESSION["courriel"] ;
	}
	$mail->AddReplyTo($_SESSION["courriel"], "") ;
	$mail->Subject = $_SESSION["messagerie"]["subject"] ;
	$mail->Body = $_SESSION["messagerie"]["body"] ;
	$mail->WordWrap = 70;
	// Fichiers joints
	if ( count($_SESSION["messagerie"]["nbAttachements"]) > 0 ) {
		$i = 0 ;
		foreach($_SESSION["messagerie"]["attachements"] as $attach) {
			if ( $i == 0 ) {
				$liste_attachements = "$attach" ;
			}
			else {
				$liste_attachements .= ", " . "$attach" ;
			}
			$i++ ;
		}
		$req = "SELECT nom FROM attachements
			WHERE id_attachement IN ($liste_attachements)" ;
		$res = mysqli_query($cnx, $req) ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
			$mail->AddAttachment($chemin.$enr["nom"]) ;
		}

		$req = "UPDATE attachements SET ref_courriel=$ref_courriel
			WHERE id_attachement IN ($liste_attachements)" ;
		mysqli_query($cnx, $req) ;
	}
	// Destinataires
	$i = 0 ;
	foreach($_SESSION["messagerie"]["destinataires"] as $destinataire)
	{
		if ( $i == 0 ) {
			$liste_destinataires = "$destinataire" ;
		}
		else {
			$liste_destinataires .= ", " . "$destinataire" ;
		}
		$i++ ;
	}
	$courriel_destinataires = array() ;
	$req = "SELECT id_dossier, email FROM dossier
		WHERE id_dossier IN ($liste_destinataires)" ;
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
			$courriel_destinataires[$enr["id_dossier"]] = trim($enr["email"]) ;
	}
	// Envoi
	$erreurs = 0 ;
	$listeErreurs = "" ;
	$envoyes = 0 ;
	while ( list($idc, $dest) = each($courriel_destinataires) ) {
		$mail->AddAddress($dest) ;
		if ( !$mail->Send() ) {
			$erreurs += 1 ;
			$listeErreurs .= "$dest ($idc) " ;
		}
		else {
			$envoyes += 1 ;
			$req = "INSERT INTO destinataires(ref_courriel, ref_dossier)
				VALUES($ref_courriel, $idc)" ;
			mysqli_query($cnx, $req) ;
		}
		$mail->ClearAddresses() ;
	}
	if ( trim($_SESSION["messagerie"]["cc"]) != "" ) {
		$CC = trim($_SESSION["messagerie"]["cc"]) ;
		$tabCC = explode(",", $CC) ;
		if ( count($tabCC)>1 ) {
			foreach($tabCC AS $cc) {
				$mail->AddAddress($cc) ;
				if ( !$mail->Send() ) {
					$erreurs += 1 ;
					$listeErreurs .= $cc . " " ;
				}
				else {
					$envoyes += 1 ;
				}
				$mail->ClearAddresses() ;
			}
		}
		else {
			$mail->AddAddress($CC) ;
			if ( !$mail->Send() ) {
				$erreurs += 1 ;
				$listeErreurs .= $CC . " " ;
			}
			else {
				$envoyes += 1 ;
			}
			$mail->ClearAddresses() ;
		}
	}


	$promo = $_SESSION["messagerie"]["promotion"] ;

	if ( $erreurs != 0 ) {
		echo "<p class='erreur'>Erreur ! ($erreurs)<br />Echec pour :<strong> $listeErreurs </strong></p>\n" ;
		if ( $envoyes == 0 ) {
			echo "<p>Aucun message n'a été envoyé.</p>" ;
		}
		else {
			echo "<p>Les autres messages ont bien été envoyés.</p>" ;
			echo "<p>(Veuillez mémoriser la/les adresse(s) pour lesquel l'envoi a échoué.)</p>" ;
			echo "<p><a href='/messagerie/promotion.php?promotion=$promo'>Retour à la messagerie</a>.</p>" ;
			unset($_SESSION["messagerie"]) ;
		}
	}
	else 
	{
		unset($_SESSION["messagerie"]) ;

		header("Location: /messagerie/promotion.php?promotion=$promo") ;
	}
	deconnecter($cnx) ;
}
else {
	header("Location: /messagerie/courriel.php") ;
}

?>
