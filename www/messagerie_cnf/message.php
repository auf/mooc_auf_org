<?php
include("inc_session.php") ;
if ( intval($_SESSION["id"]) > 9 ) {
	header("Location: /bienvenue.php") ;
	exit() ;
}

if ( !isset($_GET["id_message"]) ) {
	header("Location: /messagerie_cnf/") ;
	exit() ;
}

require_once("inc_guillemets.php") ;
require_once("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "SELECT * FROM messages WHERE id_message=".$_GET["id_message"] ;
$res = mysqli_query($cnx, $req) ;
if ( mysqli_num_rows($res) != 1 ) {
	header("Location: /messagerie_cnf/") ;
	deconnecter($cnx) ;
	exit() ;
}
$message = mysqli_fetch_assoc($res) ;
$id_session = $message["ref_session"] ;

include("inc_promotions.php") ;
$promo = idpromotion2nom($id_session, $cnx) ;
$titrePromo = $promo["intitule"]." - ".$promo["intit_ses"] ;


require_once("inc_html.php") ;

$titre = $message["subject"] . " - " . $fil["annee"] . " - " . $fil["titre"] ;
$haut_page_1 = $dtd1 . "<title>$titre</title>\n" . $dtd2 ;
$haut_page_2 = $debut_chemin
	. "<a href='/bienvenue.php'>Accueil</a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/messagerie_cnf/'>Messagerie <span>(CNF)</span></a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/messagerie_cnf/promotion.php?promotion=".$message["ref_session"]."'>"
	. $titrePromo
	. "</a>"
	. " <span class='arr'>&rarr;</span> " ;
$haut_page_2_sans = $haut_page_2
	. $message["subject"]
	. $fin_chemin ;
$haut_page_2_lien = $haut_page_2
	. "<a href='/messagerie_cnf/message.php?id_message=".$_GET["id_message"]."'>"
	. $message["subject"] . "</a>"
	. $fin_chemin ;

$url_message = "/messagerie_cnf/message.php?id_message=".$_GET["id_message"] ;

require_once("inc_messagerie_cnf.php") ;

if ( (intval($_SESSION["id"]) < 2) AND ($_GET["action"] == "change") )
{
	if ( isset($_POST["submit"]) )
	{
		$req = "UPDATE messages SET
			commentaire='".mysqli_real_escape_string($cnx, trim($_POST["commentaire"]))."',
			secret='".$_POST["secret"]."'
			WHERE id_message=".$_GET["id_message"] ;
		$res = mysqli_query($cnx, $req) ;

		header("Location: $url_message") ;
	}
	else
	{
		echo $haut_page_1 ;
		include("inc_menu.php") ;
		echo $haut_page_2_lien ;
		formulaire_message($cnx, $fil, $message) ;
		echo $end ;
	}
}
else
{
	echo $haut_page_1 ;
	include("inc_menu.php") ;
	echo $haut_page_2_sans ;
	if	( (intval($_SESSION["id"]) == 2) AND (strval($message["secret"])=="1") )
	{
		// Le cas est dÃ©jÃ  traitÃ© dans la fonction
		//echo "<p class='c'>L'archive de ce message n'est pas consultable par les CNF.</p>\n" ;
		affiche_message($cnx, $message) ;
	}
	else
	{
		affiche_message($cnx, $message) ;
	}
	echo $end ;
}
//diagnostic() ;
deconnecter($cnx) ;
/* "NgaoundÃ©rÃ©", */
?>
