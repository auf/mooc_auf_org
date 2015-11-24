<?php
include("inc_session.php") ;

include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_date.php") ;

include("inc_html.php") ;
$titre = "Imputations (statistiques)" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo "<div class='noprint'>" ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo "</div>" ;
echo $fin_chemin ;


if ( !isset($_SESSION["filtres"]["imputations"]["annee"]) )
{
	$req ="SELECT MAX(session.annee) FROM session, dossier, imputations
		WHERE imputations.ref_dossier=dossier.id_dossier
		AND dossier.ref_session=session.id_session" ;
	$res = mysqli_query($cnx, $req) ;
	$row = mysqli_fetch_row($res) ;
	$_SESSION["filtres"]["imputations"]["annee"] = $row[0] ;
}


echo "<form action='criteres.php' method='post'>" ;
echo "<input type='hidden' name='redirect' value='".$_SERVER["SCRIPT_NAME"]."' />\n" ;
echo "<table class='formulaire'>\n" ;
echo "<tbody>\n" ;
include("inc_promotions.php") ;
include("inc_form_select.php") ;
include("inc_cnf.php") ;


$req = "SELECT DISTINCT annee FROM session
	WHERE annee>2005
	ORDER BY annee DESC" ;
$res = mysqli_query($cnx, $req) ;

echo "<tr>\n" ;
echo "<th rowspan='2'>Limiter&nbsp;:</th>\n" ;
echo "<th>Année&nbsp;:</th>\n" ;
echo "<td><select name='i_annee'>\n" ;
echo "<option value=''></option>\n" ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	echo "<option value='".$enr["annee"]."'" ;
	if ( isset($_SESSION["filtres"]["imputations"]["annee"]) AND ($_SESSION["filtres"]["imputations"]["annee"] == $enr["annee"]) ) {
	    echo " selected='selected'" ;
	}
	echo ">".$enr["annee"]."</option>" ;
}
echo "</select></td>\n" ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th class='help' title=\"Lieu d'enregistrement\">Lieu&nbsp;:</th>\n" ;
echo "<td>" ;
form_select_1($CNF, "i_lieu", 
	( isset($_SESSION["filtres"]["imputations"]["lieu"]) ? $_SESSION["filtres"]["imputations"]["lieu"] : "" )
	) ;
echo "</td>\n" ;
echo "</tr>\n" ;
echo "<tr>\n<td colspan='3'><div class='c'>"
	. "<a class='reinitialiser' href='reinitialiser.php?redirect=".urlencode($_SERVER["SCRIPT_NAME"])."'>".LABEL_REINITIALISER."</a>"
	. BOUTON_ACTUALISER
	. "</div></td>\n</tr>\n" ;
echo "</tbody>\n" ;
echo "</table>\n" ;
echo "</form>" ;
echo "<br />" ;




$req_debut = "SELECT COUNT(id_imputation) AS N,
	session.id_session, annee, groupe, intitule, intit_ses
	FROM imputations, dossier, session, atelier
	WHERE imputations.ref_dossier=dossier.id_dossier
	AND dossier.ref_session=session.id_session
	AND atelier.id_atelier=session.ref_atelier " ;
if ( !empty($_SESSION["filtres"]["imputations"]["lieu"]) ) {
	$req_debut .= "AND lieu='".$_SESSION["filtres"]["imputations"]["lieu"]."' " ;
}
if ( !empty($_SESSION["filtres"]["imputations"]["annee"]) ) {
	$req_debut .= "AND annee='".$_SESSION["filtres"]["imputations"]["annee"]."' " ;
}
if ( intval($_SESSION["id"]) > 3 ) {
	$req_debut .= " AND session.id_session IN (".$_SESSION["liste_toutes_promotions"].") " ;
}
$req_fin = " GROUP BY session.id_session
	ORDER BY annee DESC, groupe, niveau, intitule" ;


// Total
$totalTotal = 0 ;
$req = $req_debut . $req_fin ;
//echo $req ;
$res = mysqli_query($cnx, $req) ;
$tabSession = array() ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]] = array(
		"intitule" => $enr["intitule"],
		"intit_ses" => $enr["intit_ses"],
		"annee" => $enr["annee"],
		"groupe" => $enr["groupe"],
		"total" => $enr["N"],
	) ;
	$totalTotal += $enr["N"] ;
}
/*
// Admis
$totalAdmis = 0 ;
$req = $req_debut . " AND etat='Admis' " . $req_fin ;
echo $req ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["Admis"] = $enr["N"] ;
	$totalAdmis += $enr["N"] ;
}
// Ajourné
$totalAjourne = 0 ;
$req = $req_debut . " AND etat='Ajourné' " . $req_fin ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["Ajourne"] = $enr["N"] ;
	$totalAjourne += $enr["N"] ;
}
// Payant
$totalPayant = 0 ;
$req = $req_debut . " AND etat='Payant' " . $req_fin ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["Payant"] = $enr["N"] ;
	$totalPayant += $enr["N"] ;
}
*/
// Femmes
$totalFemme = 0 ;
$req = $req_debut .
	" AND (genre='Femme') "
	. $req_fin ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["femme"] = $enr["N"] ;
	$totalFemme += $enr["N"] ;
}
/*
// Femmes admis
$totalFemmeAdmis = 0 ;
$req = $req_debut .
	" AND (civilite='Madame' OR civilite='Mademoiselle')
	AND etat='Admis' "
	. $req_fin ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["femmeAdmis"] = $enr["N"] ;
	$totalFemmeAdmis += $enr["N"] ;
}
// Femmes admis SCAC
$totalFemmeAjourne = 0 ;
$req = $req_debut .
	" AND (civilite='Madame' OR civilite='Mademoiselle')
	AND etat='Ajourné' "
	. $req_fin ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["femmeAjourne"] = $enr["N"] ;
	$totalFemmeAjourne += $enr["N"] ;
}
*/

// Jeunes
$totalJeune = 0 ;
$req = $req_debut .
	" AND ( (DATEDIFF(date_examen, naissance) DIV 365.25 ) < 35 ) "
	. $req_fin ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["jeune"] = $enr["N"] ;
	$totalJeune += $enr["N"] ;
}
/*
// Jeunes Admis
$totalJeuneAdmis = 0 ;
$req = $req_debut .
	" AND ( (DATEDIFF(date_examen, naissance) DIV 365.25 ) < 35 )
	AND etat='Admis' "
	. $req_fin ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["jeuneAdmis"] = $enr["N"] ;
	$totalJeuneAdmis += $enr["N"] ;
}
// Jeunes Ajourné
$totalJeuneAjourne = 0 ;
$req = $req_debut .
	" AND ( (DATEDIFF(date_examen, naissance) DIV 365.25 ) < 35 )
	AND etat='Ajourné' "
	. $req_fin ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$tabSession[$enr["id_session"]]["jeuneAjourne"] = $enr["N"] ;
	$totalJeuneAjourne += $enr["N"] ;
}
*/

require_once("inc_etat_dossier.php") ;

if ( isset($tabSession) AND (count($tabSession) == 0) )
{
	echo "<p class='c'>Aucune imputation pour ces critères</p>\n" ;
}
else
{
	$entete  = "" ;
	$entete .= "<tr>\n" ;
	$entete .= "<th colspan='3'>Femmes</th>" ;
	$entete .= "<th colspan='3' class='aide' title='Moins de 35 ans au moment du début de la formation'>Jeunes</th>" ;
	$entete .= "<th colspan='3'>Tous</th>" ;
	$entete .= "<th rowspan='2'>Promotion</th>" ;
	$entete .= "</tr>\n" ;
	$entete .= "<tr>\n" ;
	$entete .= "<th class='aide' title='Ajourné'>Aj</th>" ;
	$entete .= "<th class='aide' title='Certifié'>Ce</th>" ;
	$entete .= "<th></th>" ;
	$entete .= "<th class='aide' title='Ajourné'>Aj</th>" ;
	$entete .= "<th class='aide' title='Certifié'>Ce</th>" ;
	$entete .= "<th></th>" ;
	$entete .= "<th class='aide' title='Ajourné'>Aj</th>" ;
	$entete .= "<th class='aide' title='Certifié'>Ce</th>" ;
	$entete .= "<th></th>" ;
	$entete .= "</tr>\n" ;

	$totaux  = "<tr>\n" ;
	$totaux .= "<td class='r ajourne'><strong>$totalFemmeAjourne</strong></td>\n" ;
	$totaux .= "<td class='r admis'><strong>$totalFemmeAdmis</strong></td>\n" ;
	$totaux .= "<td class='r'><strong>$totalFemme</strong></td>\n" ;
	$totaux .= "<td class='r ajourne'><strong>$totalJeuneAjourne</strong></td>\n" ;
	$totaux .= "<td class='r admis'><strong>$totalJeuneAdmis</strong></td>\n" ;
	$totaux .= "<td class='r'><strong>$totalJeune</strong></td>\n" ;
	$totaux .= "<td class='r ajourne'><strong>$totalAjourne</strong></td>\n" ;
	$totaux .= "<td class='r admis'><strong>$totalAdmis</strong></td>\n" ;
	$totaux .= "<td class='r'><strong>$totalTotal</strong></td>\n" ;
	$totaux .= "<td class='c'><strong>Totaux</strong></td>\n" ;
	$totaux .= "</tr>\n" ;

	$groupe = "" ;
	$annee = "" ;
	echo "<table class='tableau'>\n" ;
	echo "<thead>\n" ;
	echo $entete ;
	echo "</thead>\n" ;
	echo "<tbody>\n" ;
	echo $totaux ;
	while ( list($promo, $val) = each($tabSession) )
	{
		if ( $annee != $val["annee_absolue"] ) {
			$annee = $val["annee_absolue"] ;
			echo "<tr><th class='annee r' colspan='10'>" ;
			echo "<b>$annee</b></th></tr>" ;
		}
		if ( $groupe != $val["groupe"] ) {
			$groupe = $val["groupe"] ;
			echo "<tr><td style='background: #ccc' class='r' colspan='10'>" ;
			echo "<b>$groupe</b></td></tr>" ;
		}
		echo "<tr>\n" ;
		echo "<td class='r ajourne'>"
			. ( isset($val["femmeAjourne"]) ? $val["femmeAjourne"] : "" )
			. "</td>\n" ;
		echo "<td class='r admis'>"
			. ( isset($val["femmeAdmis"]) ? $val["femmeAdmis"] : "" )
			. "</td>\n" ;
		echo "<td class='r'>"
			. ( isset($val["femme"]) ? $val["femme"] : "" )
			. "</td>\n" ;
		echo "<td class='r ajourne'>"
			. ( isset($val["jeuneAjourne"]) ? $val["jeuneAjourne"] : "" )
			. "</td>\n" ;
		echo "<td class='r admis'>"
			. ( isset($val["jeuneAdmis"]) ? $val["jeuneAdmis"] : "" )
			. "</td>\n" ;
		echo "<td class='r'>"
			. ( isset($val["jeune"]) ? $val["jeune"] : "" )
			. "</td>\n" ;
		echo "<td class='r ajourne'><strong>"
			. ( isset($val["Ajourne"]) ? $val["Ajourne"] : "" )
			. "</strong></td>\n" ;
		echo "<td class='r admis'><strong>"
			. ( isset($val["Admis"]) ? $val["Admis"] : "" )
			. "</strong></td>\n" ;
		echo "<td class='r'><strong>"
			. ( isset($val["total"]) ? $val["total"] : "" )
			. "</strong></td>\n" ;
		echo "<td><a class='bl' id='p$promo' href='promotion.php?promotion=$promo'>" ;
		echo "<strong>".$val["intitule"]."</strong>" ;
		echo " (".$val["intit_ses"].")" ;
		echo "</a></td>\n" ;
		echo "</tr>\n" ;
	}
	echo "</tbody>\n" ;
	echo "</table>\n" ;
}


deconnecter($cnx) ;
echo $end ;
?>
