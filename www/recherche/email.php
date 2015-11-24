<?php
include_once("inc_session.php") ;
if ( $_SESSION["id"] != "00" ) {
	header("Location: /recherche/") ;
	exit ;
}
if ( isset($_GET["id_dossier"]) AND is_numeric($_GET["id_dossier"]) ) {
	$id_dossier = $_GET["id_dossier"] ;
}
else if ( isset($_POST["id_dossier"]) AND is_numeric($_POST["id_dossier"]) ) {
	$id_dossier = $_POST["id_dossier"] ;
}
else {
	header("Location: /recherche/") ;
	exit ;
}



$titre = "Rappel de mot de passe OU modification d'adresse électronique" ;
include("inc_html.php");
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
include("inc_menu.php");
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/recherche/'>Recherche</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

/*
echo "<pre>" ;
print_r($_SESSION["filtres"]["recherche"]) ;
echo "</pre>" ;
*/

include("inc_mysqli.php");
$cnx = connecter() ;



$req = "SELECT * FROM dossier, session, atelier
	WHERE dossier.ref_session=session.id_session
	AND session.ref_atelier=atelier.id_atelier
	AND dossier.id_dossier='".$id_dossier."'" ;
$res = mysqli_query($cnx, $req) ;
$T = mysqli_fetch_assoc($res) ;

if ( isset($_POST["action"]) AND ($_POST["action"] == "modif") )
{
	$req = "UPDATE dossier SET email='".trim($_POST["email"])."'
		WHERE id_dossier='".$id_dossier."'" ;
	mysqli_query($cnx, $req) ;
	$T["email"] = $_POST["email"] ;
}

$message = "Bonjour ".$T["prenom"]." ".$T["nom"].",

Le numéro de dossier et le mot de passe de votre inscription pour :
  " . $T["universite"] . "
  " . $T["intitule"] . "
  " . $T["intit_ses"] . "
sur le site https://" . URL_DOMAINE . "/inscription/ sont :

  Numéro de dossier : $id_dossier
  Mot de passe      : ".$T["pwd"]."

Pensez à modifier votre adresse électronique dans votre inscription
si elle n'est pas fiable !

Cordialement,

Agence universitaire de la Francophonie
http://" . URL_DOMAINE_PUBLIC . "
" ;

$sujet = "Numéro et mot de passe de votre dossier" ;

$debut_email = "<table class='formulaire'><tr>
	<th>Expéditeur :</th>
	<td>".EMAIL_FROMNAME." &lt;".EMAIL_FROM."&gt;</td>
</tr><tr>
	<th>Destinataire :</th>\n" ;

$adresse = "<td><input type='text' name='email' size='50' value='".$T['email']."' /></td>" ;
$adresse_envoyee = "<td><strong>"
	. ( isset($_POST["email"]) ? $_POST["email"] : "" )
	. "</strong></td>" ;

$suite1_email = "</tr><tr>
	<th>Sujet :</th>
	<td>$sujet</td>
</tr><tr>
	<th>Message :</th>
	<td><pre>".$message."</pre></td>
</tr>\n" ;

$suite2_email = "<tr><td colspan='2' class='c'>
<p class='c'><strong><input type='submit' value='Envoyer ce courriel' /></strong></p>
</td></tr>\n" ;

$fin_email = "</table>" ;





// Envoi ou modification du courriel
if ( isset($_POST["action"]) AND ($_POST["action"] == "email") )
{
	require_once("inc_aufPhpmailer.php") ;
	$mail = new aufPhpmailer() ;
	$mail->From = EMAIL_FROM ;
	$mail->FromName = EMAIL_FROMNAME ;
	$mail->AddReplyTo(EMAIL_REPLYTO, "") ;
	$mail->Sender = EMAIL_SENDER ;
	$mail->Subject = $sujet ;
	$mail->Body = $message ;
	$mail->AddAddress($_POST["email"]) ;
	if ( $mail->Send() )
	{
		echo "<p class='msgok c'>Courriel envoyé :</p>" ;
		echo $debut_email ;
		echo $adresse_envoyee ;
		echo $suite1_email ;
		echo $fin_email ;
	}
	else {
		echo "<p class='erreur'>Echec de l'envoi du courriel</p>" ;
	}
}
else
{
	if ( isset($_POST["action"]) AND ($_POST["action"] == "modif") )
	{
		echo "<p class='msgok c'>Adresse modifiée.</p>" ;
	}
	// Envoi de courriel
	echo "<div style='float: left'>\n" ;
		echo "<form method='post' action='email.php'>
		<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
		echo "<input type='hidden' name='action' value='email' />\n" ;
		echo $debut_email ;
		echo $adresse ;
		echo $suite1_email ;
		echo $suite2_email ;
		echo $fin_email ;
		echo "</form>\n" ;
	echo "</div>\n" ;

	// Modification adresse
	echo "<div>\n" ;
		echo "<form method='post' action='email.php?id_dossier=".$id_dossier."'>\n" ;
		echo "<table class='formulaire'>\n" ;
		echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
		echo "<input type='hidden' name='action' value='modif' />\n" ;
		echo "<tr><th>Remplacer :</th><td>".$T['email']."</td></tr>\n" ;
		echo "<tr><th>Par :</th><td><input type='text' size='50' name='email' /></td></tr>\n" ;
		echo "<tr><td colspan='2' class='c'><p class='c'><strong><input type='submit' value='Enregistrer' /></strong></p></td></tr>\n" ;
		echo "</table>\n" ;
		echo "</form>\n" ;
	echo "</div>\n" ;

	echo "<div style='clear: both;'></div>\n" ;

	echo "<h2>".$T["intitule"]."<br />".$T["annee"]." - ".$T["intit_ses"]."</h2>\n" ;
	
	include("inc_formulaire_inscription.php") ;
	include("inc_date.php");
	include("inc_etat_dossier.php") ;
	include("inc_dossier.php");
	affiche_dossier($cnx, $T) ;

}


echo $end ;
deconnecter($cnx) ;
?>
