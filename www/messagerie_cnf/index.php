<?php
include("inc_session.php") ;
if ( intval($_SESSION["id"]) > 2 ) {
    header("Location: /bienvenue.php") ;
    exit() ;
}

include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_html.php") ;
$titre = "Messagerie <span>(CNF)</span>" ;
echo $dtd1 ;
echo "<title>".strip_tags($titre)."</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;



require_once("inc_groupe.php");

$req = "SELECT MAX(annee) FROM session" ;
$res = mysqli_query($cnx, $req) ;
$row = mysqli_fetch_row($res) ;
$derniere_annee = $row[0] ;

if ( !isset($_SESSION["filtres"]["messagerieCNF"]["annee"]) ) {
	$_SESSION["filtres"]["messagerieCNF"]["annee"] = $derniere_annee ;
}

echo "<form method='post' action='criteres.php'>\n" ;
echo "<table class='formulaire'>\n<tbody>\n" ;
echo "<tr>\n" ;
echo "<th>Année&nbsp;: </th>\n" ;
echo "<td><select name='annee'>\n" ;
$req = "SELECT DISTINCT(annee) FROM session ORDER BY annee DESC" ;
$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
    echo "<option value='".$enr["annee"]."'" ;
    if ( $_SESSION["filtres"]["messagerieCNF"]["annee"] == $enr["annee"] ) {
        echo " selected='selected'" ;
    }
    echo ">".$enr["annee"]."</option>" ;
}
echo "</select></td>\n" ;
echo "</tr>\n" ;
echo "<tr>\n" ;
echo "<th>Domaine : </th>\n" ;
echo "<td>" ;
echo select_groupe(
	( isset($_SESSION["filtres"]["messagerieCNF"]["groupe"]) ? $_SESSION["filtres"]["messagerieCNF"]["groupe"] : "" )
	) ;
echo "</td>\n" ;
echo "</tr>\n" ;
echo "<tr>\n" ;
echo "<td colspan='2'><div class='c'>" ;
echo "<input class='b' type='submit' value='Actualiser' /></div></td>\n" ;
echo "</tr>\n" ;
echo "</tbody>\n</table>\n" ;
echo "</form>" ;
echo "<br />" ;


if ( intval($_SESSION["id"]) < 3 )
{
	$requete = "SELECT id_session, groupe, intitule, intit_ses, annee
		FROM session, atelier WHERE
		atelier.id_atelier=session.ref_atelier
		AND session.annee=".$_SESSION["filtres"]["messagerieCNF"]["annee"]." ";
	if ( isset($_SESSION["filtres"]["messagerieCNF"]["groupe"]) AND ($_SESSION["filtres"]["messagerieCNF"]["groupe"] != "") ) {
		$requete .= " AND groupe='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["messagerieCNF"]["groupe"])."' " ;
	}
	$requete .= " ORDER BY annee DESC, groupe, niveau, intitule" ;
}
else
{
	$requete = "SELECT id_session, groupe, intitule, intit_ses, annee
		FROM session , atelier , atxsel
		WHERE session.ref_atelier=atelier.id_atelier
		AND session.annee >= 2006
		AND atelier.id_atelier=atxsel.id_atelier
		AND atxsel.id_sel='".$_SESSION["id"]."'
		ORDER BY annee DESC, groupe, niveau, intitule" ;
}
$resultat = mysqli_query($cnx, $requete) ;

if ( mysqli_num_rows($resultat) == 0 )
	echo "<p class='erreur'>Pas d'inscription active actuellement !</p>" ;
else
{
	echo "<table class='tableau'>\n" ;
	echo "<thead>\n<tr>\n" ;
	echo "\t<th>Courriels<br />envoyés</th>\n" ;
	echo "\t<th>Formation - inscription" ;
	echo "<p class='normal' style='color: #000;'>Cliquez sur une inscription pour<br />" ;
	echo "afficher la liste des messages envoyés<br />" ;
	echo "ou pour envoyer un nouveau message.</p>" ;
	echo "</th>\n" ;
	echo "</tr>\n" ;
	echo "</thead>\n<tbody>\n" ;

	$i = 1 ;
	$groupe = "" ;
	$annee = "" ;
	while ( $ligne = mysqli_fetch_assoc($resultat) )
	{
		if ( $annee != $ligne["annee"] ) {
			$annee = $ligne["annee"] ;
			echo "<tr><td colspan='2' class='r annee'>" ;
			echo "<b style='font-size: 100%;'>$annee</b></td></tr>" ;
		}
		if ( intval($_SESSION["id"]) < 3 ) {
			if ( $groupe != $ligne["groupe"] ) {
				$groupe = $ligne["groupe"] ;
				echo "<tr><td colspan='2' class='r' style='background: #ccc'>" ;
				echo "<b style='font-size: 100%;'>$groupe</b></td></tr>" ;
			}
		}
		echo "<tr>" ;

		$req = "SELECT COUNT(*) AS N FROM messages
			WHERE ref_session=".$ligne["id_session"] ;
		$res = mysqli_query($cnx, $req) ;
		$enr = mysqli_fetch_assoc($res) ;
		echo "<td class='r'><strong>".$enr["N"]."</strong></td>\n" ;

		echo "<td><a class='bl' href='promotion.php?promotion=".$ligne["id_session"]."'>" ;
		echo $ligne["intitule"] . " - " . $ligne["intit_ses"] ;
		echo "</a></td>\n" ;

//		echo "<td><a href='session.php?promotion=".$ligne["id_session"]."'>" ;

		echo "</tr>\n" ;
		$i++ ;
	}
	echo "</tbody>\n</table>\n" ;
}

echo $end ;

deconnecter($cnx) ;
?>
