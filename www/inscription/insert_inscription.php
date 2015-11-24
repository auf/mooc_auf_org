<?php

while ( list($key, $val) = each($T) ) {
	$Ts[$key] = mysqli_real_escape_string($cnx, remets_guillemets($val)) ;
}

$date_n     = $Ts["annee_n"] ."-". $Ts["mois_n"] ."-".  $Ts["jour_n"] ;
$date_ident = $Ts["annee_ident"] ."-". $Ts["mois_ident"] ."-".  $Ts["jour_ident"] ;
$pwd = key_generator(8) ;

$req = "INSERT INTO dossier (
	ref_session,
	id_mooc,
	email, genre, nom, prenom,
	naissance, lieu_naissance,
	pays_naissance, pays_nationalite, pays_residence,
	situation_actu, sit_autre,
	ident_nature, ident_autre, ident_numero, ident_date, ident_lieu,
	signature, certifie, accepte,
	pwd, date_inscrip, date_maj)
	VALUES (
	'$id_session',
	'".$Ts["id_mooc"]."',
	'".$Ts["email"]."', '".$Ts["genre"]."', '".$Ts["nom"]."', '".$Ts["prenom"]."',
	'".$date_n."',
	'".$Ts["lieu_naissance"]."', '".$Ts["pays_naissance"]."',
	'".$Ts["pays_nationalite"]."', '".$Ts["pays_residence"]."',
	'".$Ts["situation_actu"]."', '".$Ts["sit_autre"]."',
	'".$Ts["ident_nature"]."', '".$Ts["ident_autre"]."', '".$Ts["ident_numero"]."',
	'".$date_ident."',
	'".$Ts["ident_lieu"]."',
	'".$Ts["signature"]."',
	'".$Ts["certifie"]."',
	'".$Ts["accepte"]."',
	'$pwd', CURRENT_DATE, CURRENT_DATE)" ;
$res = mysqli_query($cnx, $req) ;
$id_dossier = mysqli_insert_id($cnx) ;

/*
if ( $nombre_questions > 0 )
{
	for ( $i=1 ; $i <= $nombre_questions ; $i++ )
	{
		$req = "INSERT INTO reponse VALUES ('',
			'".$Ts["id_question$i"]."',
			'".$Ts["question$i"]."',
			$id_dossier)" ;
		mysqli_query($cnx, $req) ;
	}
}
*/

// Envoi d'email de confirmation
$req = "SELECT nom, prenom, email FROM dossier WHERE id_dossier=".$id_dossier ;
$res = mysqli_query($cnx, $req);
$enr = mysqli_fetch_assoc($res);
$nom = $enr["nom"];
$prenom = $enr["prenom"];
$email = $enr["email"];

// FIXME phpMailer pour l'encodage...

require_once("inc_config.php") ;
require_once("inc_aufPhpmailer.php") ;
$mail = new aufPhpmailer() ;
$mail->From = EMAIL_FROM ;
$mail->FromName = NOM_CONTACT ;
$mail->AddReplyTo(EMAIL_CONTACT, "") ;
$mail->Sender = EMAIL_SENDER ;
$mail->Subject = "Votre inscription sur le site MOOC de l'AUF" ;
$mail->Body = "Bonjour $prenom $nom".",

Votre inscription à la formation :
" . $tabInscription["intitule"] ."
" . $tabInscription["intit_ses"] ."
a été enregistrée.

Vous pouvez la modifier jusqu'à la date de clôture des inscriptions sur
https://" . URL_DOMAINE . "/inscription/
en utilisant les paramètres suivants :

  Numéro de dossier : $id_dossier
  Mot de passe      : $pwd

Cordialement,

".NOM_CONTACT."
http://" . URL_DOMAINE_PUBLIC . "

" . MESSAGE_AUTOMATIQUE ;

$mail->AddAddress($email) ;
if ( !$mail->Send() ) {
	echo "<p class='erreur'>L'envoi du mail a échoué.</p>" ;
}
$mail->ClearAddresses() ;

?>
