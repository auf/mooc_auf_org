<?php
include("inc_session.php") ;
include("inc_html.php") ;
include("inc_date.php") ;
include("inc_mysqli.php") ;

$titre = "Inscriptions en cours" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;


echo "<p class='c'>Page de <strong> <a target='_blank' href='/inscription/'
>mise à jour d'un dossier d'inscription</a></strong>.</p>\n" ;

$cnx = connecter() ;



require_once("inc_groupe.php");
echo "<form action='criteres_en_cours.php' method='post'>\n" ;
echo "<table class='formulaire'>\n" ;
echo "<tr>\n" ;
echo "<th>Limiter à un domaine : </th>\n" ;
echo "<td>" ;
echo select_groupe(
	( isset($_SESSION["e_groupe"]) ? $_SESSION["e_groupe"] : "" )
	) ;
echo "</td>\n" ;
echo "<td>" ;
echo "<input class='b' type='submit' value='Actualiser' /></td>\n" ;
echo "</tr>\n" ;
echo "</table>\n" ;
echo "</form>" ;


$req = "SELECT COUNT(*) FROM session
	WHERE (inscriptions_deb<=CURDATE() AND inscriptions_fin>=CURDATE())
	OR (imputations_deb<=CURDATE() AND imputations_fin>=CURDATE())" ;
$res = mysqli_query($cnx, $req) ;
$row = mysqli_fetch_row($res) ;
$nombre = $row[0] ;


$req = "SELECT groupe, intitule, session.*
	FROM session, atelier 
	WHERE atelier.id_atelier=session.ref_atelier
	AND (
		( (session.inscriptions_deb<=CURDATE()) AND (CURDATE()<=session.inscriptions_fin) )
		OR ( (session.imputations_deb<=CURDATE()) AND (CURDATE()<=session.imputations_fin) )
		) " ;
if ( isset($_SESSION["e_groupe"]) AND ($_SESSION["e_groupe"] != "") ) {
	$req .= " AND groupe='".addslashes($_SESSION["e_groupe"])."' " ;
}
$req .= " ORDER BY annee DESC, groupe, niveau, intitule" ;
//echo $req ;
$res = mysqli_query($cnx, $req) ;
$N = mysqli_num_rows($res) ;

echo "<p class='c'>" ;
echo "<strong>$nombre promotions</strong>
pour lesquelles pré-inscriptions ou imputations (inscriptions) sont ouvertes.<br />
<span style='font-size: smaller'>(Chaque lien mène au formulaire d'inscription corrrespondant.)</span> " ;
if ( isset($_SESSION["e_groupe"]) AND ($_SESSION["e_groupe"] != "") ) {
	echo "<br /><strong>$N promotions</strong> en " . $_SESSION["e_groupe"] ;
}
echo "</p>" ;


if ( $nombre != "0" ) {
echo "<table class='tableau'>
<thead>
<tr>
	<th>Formation - Inscription</th>
	<th>Pré-inscriptions</th>
	<th>Imputations</th>
	<th>Formation</th>
</tr>
</thead>
<tbody>" ;
}

$i = 1 ;
$annee = "" ;
$groupe = "" ;
while ( $ligne = mysqli_fetch_assoc($res) ) 
{
	if ( $annee != $ligne["annee"] ) {
		$annee = $ligne["annee"] ;
		echo "<tr><td style='background: #333; color: #fff;' colspan='4' class='r'>" ;
		echo "<b style='font-size: 100%;'>$annee</b></td></tr>" ;
	}
	if ( $groupe != $ligne["groupe"] ) {
		$groupe = $ligne["groupe"] ;
		echo "<tr><td style='background: #ccc' colspan='4' class='r'>" ;
		echo "<b style='font-size: 100%;'>$groupe</b></td></tr>" ;
	}
	echo "<tr>" ;
	echo "<td><b>".$ligne["intitule"]."</b> - " ;
	echo "<b>".$ligne["intit_ses"]."</b>" ;
		echo "<div style='font-size: 70%; padding: 2px 1em;'>" ;
		echo "<a href='/inscription/inscription.php" ;
		echo "?id_session=".$ligne["id_session"]."' target='_blank'>" ;
		echo "&lt;a href='https://foad-mooc.auf.org/inscription/" ;
		echo "inscription.php?id_session=".$ligne["id_session"] ;
		echo "' target='_blank'&gt;Inscription&lt;/a&gt;" ;
		echo "</div>" ;
	echo "</td>" ;

	echo "\t<td class='r ". dateOuiNon($ligne["inscriptions_deb"], $ligne["inscriptions_fin"]) ."'>" ;
	echo "Du <span class='date'>".mysql2datenum($ligne["inscriptions_deb"])."</span><br />" ;
	echo "au <span class='date'>".mysql2datenum($ligne["inscriptions_fin"])."</span></td>\n" ;

	echo "\t<td class='r ". dateOuiNon($ligne["imputations_deb"], $ligne["imputations_fin"]) ."'>" ;
	echo "Du <span class='date'>".mysql2datenum($ligne["imputations_deb"])."</span><br />" ;
	echo "au <span class='date'>".mysql2datenum($ligne["imputations_fin"])."</span></td>\n" ;

	/*
	echo "\t<td class='r'>" ;
	echo "Du <span class='date'>".mysql2datenum($ligne["date_deb"])."</span><br />" ;
	echo "au <span class='date'>".mysql2datenum($ligne["date_fin"])."</span></td>\n" ;
	*/

    echo "\t<td class='r'>" ;
    echo "<span class='date'>".mysql2datenum($ligne["date_examen"])."</span></td>\n" ;

    echo "</tr>\n" ;
}
$i++ ;
if ( $nombre != "0" ) {
echo "</tbody></table>";
}

deconnecter($cnx) ;
echo $end;
?>
