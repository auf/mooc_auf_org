<?php
include("inc_session.php") ;
if ( intval($_SESSION["id"]) > 1 ) {
	header("Location: /bienvenue.php") ;
	exit() ;
}

if ( !isset($_GET["promotion"]) ) {
	header("Location: /messagerie_cnf/") ;
	exit() ;
}

require_once("inc_guillemets.php") ;
require_once("inc_mysqli.php") ;
$cnx = connecter() ;

require_once("inc_html.php") ;

$script = $htmlJquery172 . '
<script language="javascript">
function copie() {
	document.forms[0].cc.value="'. $_SESSION["courriel"] .'" ;
}
//<![CDATA[ 
$(window).load(function(){
	$("input[type=\'checkbox\']").change(function(){
		if($(this).is(":checked")){
			$(this).parent().parent().addClass("aex"); 
		}else{
			$(this).parent().parent().removeClass("aex");  
		}
	});
});//]]>
</script>
' ;


include("inc_promotions.php") ;
$promo = idpromotion2nom($_GET["promotion"], $cnx) ;
$titre = $promo["intitule"]." - ".$promo["intit_ses"] ;

$haut_page_1 = $dtd1 . "<title>$titre</title>\n" . $script .$dtd2 ;
$haut_page_2 = $debut_chemin
	. "<a href='/bienvenue.php'>Accueil</a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/messagerie_cnf/index.php'>Messagerie <span>(CNF)</span></a>"
	. " <span class='arr'>&rarr;</span> " ;
$haut_page_2 .= "<a href='/messagerie_cnf/promotion.php?promotion=".$_GET["promotion"]."'>"
	. $titre
	. "</a>"
	. " <span class='arr'>&rarr;</span> "
	. "Nouveau message"
	. $fin_chemin ;


$url_promotion = "/messagerie_cnf/promotion.php?promotion=".$_GET["promotion"] ;


require_once("inc_messagerie_cnf.php") ;


if ( isset($_POST["submit"]) )
{
	$erreurs = controle_message($_POST) ;

 	if ( $erreurs == "" )
	{
		$req = "INSERT INTO messages
			(ref_session, `date`, `from`, cc, subject, body, commentaire, secret)
			VALUES('".$_GET["promotion"]."'
			, CURDATE()
			, '".$_SESSION["courriel"]."'
			, '".mysqli_real_escape_string($cnx, trim($_POST["cc"]))."'
			, '".mysqli_real_escape_string($cnx, trim($_POST["subject"]))."'
			, '".mysqli_real_escape_string($cnx, trim($_POST["body"]))."'
			, '".mysqli_real_escape_string($cnx, trim($_POST["commentaire"]))."'
			, '".$_POST["secret"]."')" ;
		//echo "$req <br />" ;
		$res = mysqli_query($cnx, $req) ;
		$req = "SELECT LAST_INSERT_ID() AS N" ;
		$res = mysqli_query($cnx, $req) ;
		$enr = mysqli_fetch_assoc($res) ;
		$id_message = $enr["N"] ;

		require_once("inc_aufPhpmailer.php") ;
		$mail = new aufPhpmailer() ;
		$mail->FromName = "MOOC" ;
		if ( EMAIL_FROM_TOUJOURS ) {
			$mail->From = EMAIL_FROM ;
		}
		else {
			$mail->From = $_SESSION["courriel"] ;
		}
		if ( EMAIL_SENDER_TOUJOURS ) {
			$mail->Sender = EMAIL_SENDER ;
		}
		else {
			$mail->Sender = $_SESSION["courriel"] ;
		}
		$mail->AddReplyTo($_SESSION["courriel"], "") ;
		$mail->Subject = trim($_POST["subject"]) ;
		$mail->Body = trim($_POST["body"]) ;
		$mail->WordWrap = 70 ;

		$cc = trim($_POST["cc"]) ;
		if ( $cc != "" ) {
//			$mail->AddCC($cc, "") ;
			$mail->AddAddress($cc) ;
		}

		$liste = "" ;
		foreach ($_POST["destin"] as $des) {
			$liste .= $des ."," ;
		}
		$liste = substr($liste, 0, -1) ;
		$req = "SELECT courriel FROM individus
			WHERE id_individu IN ($liste)
			ORDER BY nom, prenom" ;
		$res = mysqli_query($cnx, $req) ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
			$mail->AddAddress($enr["courriel"]) ;
		}

		if ( $mail->Send() )
		{
			if ( count($_POST["promos"]) != 0 ) {
				$req = "INSERT INTO messages_sessions(ref_message, ref_session) VALUES" ;
				foreach ($_POST["promos"] as $promo) {
					$req .= "(".$id_message.", ".$promo.")," ;
				}
				$req = substr($req, 0, -1) ;
				$res = mysqli_query($cnx, $req) ;
				//echo "$req <br />" ;
			}
			if ( count($_POST["destin"]) != 0 ) {
				$req = "INSERT INTO messages_individus(ref_message, ref_individu) VALUES" ;
				reset($_POST["destin"]) ;
				foreach ($_POST["destin"] as $des) {
					$req .= "(".$id_message.", ".$des.")," ;
				}
				$req = substr($req, 0, -1) ;
				$res = mysqli_query($cnx, $req) ;
				//echo "$req <br />" ;
			}
		}

		//header("Location: /messagerie_cnf/message.php?id_message=".$id_message) ;
		header("Location: $url_promotion") ;
	}
	else
	{
		echo $haut_page_1 ;
		include("inc_menu.php") ;
		echo $haut_page_2 ;
		echo $erreurs ;
		formulaire_message_nouveau($cnx, $_GET["promotion"], $_POST) ;
		echo $end ;
	}
}
else
{
	echo $haut_page_1 ;
	include("inc_menu.php") ;
	echo $haut_page_2 ;
	$T = array() ;
	$T["destin"] = ids_individus_destinataires($cnx, $_GET["promotion"]) ;
	formulaire_message_nouveau($cnx, $_GET["promotion"], $T) ;
	echo $end ;
}

//diagnostic() ;
deconnecter($cnx) ;
/* "NgaoundÃ©rÃ©", */
?>
