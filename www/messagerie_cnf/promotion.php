<?php
include("inc_session.php") ;

if ( empty($_GET["promotion"]) ) {
	header("Location: /messagerie_cnf/") ;
	exit ;
}

$_SESSION["messagerie"]["action"] = "" ;

include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_promotions.php") ;
$promo = idpromotion2nom($_GET["promotion"], $cnx) ;
$titre = $promo["intitule"]." - ".$promo["intit_ses"] ;

include("inc_html.php") ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie_cnf/index.php'>Messagerie <span>(CNF)</span></a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

if ( intval($_SESSION["id"]) < 2 )
{
	echo "<p class='c'><strong>" ;
	echo "<a href='message_nouveau.php?promotion=".$_GET["promotion"]."'>Nouveau message</a>" ;
	echo "</strong></p>\n" ;
	echo "<br />\n" ;
}

$req = "SELECT id_message, date, `from`, cc, subject, commentaire, secret,
	COUNT(ref_message) AS N
	FROM messages LEFT JOIN messages_individus
	ON messages.id_message=messages_individus.ref_message
	WHERE ref_session=".$_GET["promotion"]."
	GROUP BY ref_message
	ORDER BY date DESC, id_message DESC" ;
//echo $req ;
$res = mysqli_query($cnx, $req) ;
if ( mysqli_num_rows($res) != 0 )
{
	include("inc_date.php") ;
	echo "<table class='tableau'>\n" ;
	echo "<caption>Messages envoyés</caption>" ;
	echo "<thead>\n" ;
	echo "<tr>\n" ;
	echo "<th>Date</th>\n" ;
	echo "<th>Expéditeur</th>\n" ;
	echo "<th>Sujet<br /><span class='normal'>Commentaire</span></th>\n" ;
	echo "<th>Destinataires</th>\n" ;
	echo "<th>Archive<br />consultable<br />par les CNF</th>\n" ;
	echo "</tr>\n" ;
	echo "</thead>\n" ;
	echo "<tbody>\n" ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		echo "<tr>\n" ;
		echo "<td>".mysql2datenum($enr["date"])."</td>\n" ;
		echo "<td class='c'>".$enr["from"]."</td>\n" ;
		echo "<td><strong><a href='message.php?id_message=" ;
		echo $enr["id_message"]."'>".$enr["subject"]."</a></strong><br />" ;
		echo $enr["commentaire"]."</td>\n" ;
		echo "<td class='r'>" . $enr["N"] . "</td>\n" ;
		if ( strval($enr["secret"]) == "0" ) {
			echo "<td class='c'>oui</td>\n" ;
		}
		else {
			echo "<td class='c Non'>non</td>\n" ;
		}
		echo "</tr>\n" ;
	}
	echo "</tbody>\n" ;
	echo "</table>\n" ;
}
else {
	echo "<p class='c'>Aucun message n'a été envoyé.</p>\n" ;
}

deconnecter($cnx) ;
echo $end ;
?>
