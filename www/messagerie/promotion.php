<?php
include("inc_session.php") ;

if ( empty($_GET["promotion"]) ) {
	header("Location: index.php") ;
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
echo "<a href='/messagerie/index.php'>Messagerie <span>(inscrits)</span></a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

include_once("inc_etat_dossier.php") ;
include_once("inc_etat_inscrit.php") ;
include_once("inc_cnf.php") ;

if ( !empty($_SESSION["messagerie"]["promotion"]) 
	AND ( $_SESSION["messagerie"]["promotion"] != $_GET["promotion"] )
	AND count($_SESSION["messagerie"] > 2 ) )
{
    echo "<p class='c erreur' style='font-size: 100%'><strong>" ;
    echo "<a href='courriel.php'>Reprendre le message en cours de préparation</a>" ;
    echo "<br />pour une autre promotion. (Il sera effacé sinon)" ;
    echo "</strong></p>" ;
}

//if ( ($promo["evaluations"] == "Oui") OR ($promo["imputations"] == "Oui") )
//{
	echo "<h3 class='c'>Nouveau courriel</h3>\n" ;
	echo "<form action='session.php' method='post'>\n" ;
	echo "<table class='formulaire'>\n" ;
	echo "<tr>\n" ;
	echo "<th colspan='3'>Limiter la liste des destinataires potentiels&nbsp;:</th>\n" ;
	echo "</tr>\n" ;

	echo "<tr>\n<th>État&nbsp;:</th>\n<td colspan='2'>" ;
	liste_etat_inscrit("etat",
		( isset($_SESSION["messagerie"]["etat"]) ? $_SESSION["messagerie"]["etat"] : "" ),
		TRUE) ;
	echo "</td>\n</tr>\n" ;

	/*
	echo "<tr>\n<th>Lieu d'examen&nbsp;:</th>\n<td>" ;
	echo listeCnf("lieu_examen",
		( isset($_SESSION["messagerie"]["lieu_examen"]) ? $_SESSION["messagerie"]["lieu_examen"] : "" ),
		TRUE) ;
	echo "</td>\n</tr>\n" ;
	*/

	echo "<tr>\n" ;
	echo "<th><label for='lieu_examen'>Lieu d'examen&nbsp;:</label></th>\n" ;
	echo "<td><select name='lieu_examen'>\n" ;
	echo "<option value=''></option>\n" ;
	$req = "SELECT DISTINCT lieu_examen FROM dossier, imputations
	    WHERE ref_dossier=id_dossier
	    AND ref_session=".$_GET["promotion"]."
	    ORDER BY lieu_examen" ;
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
	    echo "<option value=\"".$enr["lieu_examen"]."\"" ;
	    if  (
	            isset($_SESSION["messagerie"]["lieu_examen"])
	            AND ($_SESSION["messagerie"]["lieu_examen"] == $enr["lieu_examen"])
	        )
	    {
	        echo " selected='selected'" ;
	    }
	    echo ">".$enr["lieu_examen"]."</option>\n" ;
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
	echo "</td>\n</tr>\n" ;

	echo "<tr><td colspan='3'>" ;
	echo "<input type='hidden' name='promotion' " ;
	echo "value='".$_GET["promotion"]."' />\n" ;
	echo "<p class='c' style='margin: 0.5em 0'><input type='submit' " ;
	echo "style='font-weight: bold; font-size: 0.9em;' " ;
	echo "value='Rédiger un nouveau courriel' /></p>\n" ;
	echo "</td></tr>\n" ;
	echo "</table>\n" ;
	echo "</form>\n" ;

	echo "\n<br /><hr />\n\n" ;
//}

echo "<h3 class='c'>Courriels envoyés</h3>\n" ;

/*
$req = "SELECT id_courriel, date, etat, expediteur, subject, commentaire
	FROM courriels
	WHERE ref_session=".$_GET["promotion"]."
	ORDER BY date DESC, id_courriel DESC" ;
*/

$req = "SELECT id_courriel, date, etat, lieu_examen, etat_dossier,
	expediteur, subject, commentaire,
	COUNT(ref_courriel) AS N
	FROM courriels LEFT JOIN destinataires
	ON courriels.id_courriel=destinataires.ref_courriel
	WHERE ref_session=".$_GET["promotion"]."
	GROUP BY ref_courriel
	ORDER BY date DESC, id_courriel DESC" ;

$res = mysqli_query($cnx, $req) ;
if ( mysqli_num_rows($res) != 0 )
{
	include("inc_date.php") ;
	echo "<table class='tableau'>\n" ;
	echo "<thead>\n" ;
	echo "<tr>\n" ;
	echo "<th rowspan='2'>Date</th>\n" ;
	echo "<th rowspan='2'>Expéditeur</th>\n" ;
	echo "<th rowspan='2'>Sujet<br /><span class='normal'>Commentaire</span></th>\n" ;
	echo "<th colspan='4'>Destinataires</th>\n" ;
	echo "</tr>\n" ;
	echo "<tr>\n" ;
	echo "<th><span class='help' title='Nombre'>N</span></th>\n" ;
	echo "<th>&Eacute;tat</th>\n" ;
	echo "<th title=\"Lieu d'examen\">Examen</th>\n" ;
	echo "<th>Résultat</th>\n" ;
	echo "</tr>\n" ;
	echo "</thead>\n" ;
	echo "<tbody>\n" ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		echo "<tr>\n" ;
		echo "<td>".mysql2datenum($enr["date"])."</td>\n" ;
		echo "<td class='c'>".$enr["expediteur"]."</td>\n" ;
		echo "<td><strong><a href='courriel_envoye.php?id_courriel=" ;
		echo $enr["id_courriel"]."'>".$enr["subject"]."</a></strong><br />" ;
		echo $enr["commentaire"]."</td>\n" ;
		echo "<td class='r'>" . $enr["N"] . "</td>\n" ;
		echo "<td class='c'>".$ETAT_INSCRIT[$enr["etat"]]."</td>\n" ;
		echo "<td class='c'>" . $enr["lieu_examen"] . "</td>\n" ;
		echo "<td class='c ".$ETAT_DOSSIER_IMG_CLASS[$enr["etat_dossier"]]."'>"
			. $ETAT_DOSSIER[$enr["etat_dossier"]]."</td>\n" ;
		echo "</tr>\n" ;
	}
	echo "</tbody>\n" ;
	echo "</table>\n" ;
}
else {
	echo "<p class='c'>Aucun courriel n'a été envoyé.</p>\n" ;
}

deconnecter($cnx) ;
echo $end ;
?>
