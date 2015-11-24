<?php
include("inc_sess.php") ;
include("inc_html.php") ;

$titre = "Rappel de mot de passe par courriel" ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;

include("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "SELECT nom, prenom, email1, pwd FROM candidat, dossier
	WHERE dossier.id_candidat=candidat.id_candidat
	AND dossier.id_dossier=$id_dossier" ;
$res = mysqli_query($cnx, $req);
$enr = mysqli_fetch_assoc($res);
$civilite = $enr["civilite"];
$nom = $enr["nom"];
$prenom = $enr["prenom"];
$email = $enr["email1"];
$pass = $enr["pwd"];

$sujet = "Rappel de mot de passe";

$body = "Bonjour $civilite $nom $prenom,

Vous pouvez mettre à jour votre dossier de candidature en vous
reconnectant sur notre site :

http://www.foad.refer.org/candidature/identification.php

et en utilisant les paramètres suivants :

Numéro de dossier : $id_dossier
Mot de passe      : $pass

Cordialement,

Agence Universitaire de la Francophonie
http://foad.refer.org
" ;

if ( @mail($email, $sujet, $body, "From: FOAD <foad@auf.org>\r\nReply-To: foad@auf.org\r\n") ) 
{
	echo "<p><strong>Courriel envoyé à $email :</strong></p>" ;
	echo "<pre>$body</pre>\n" ;
}
else
	echo "<p class='erreur'>Echec de l'envoi du courriel</p>" ;


echo $end ;
