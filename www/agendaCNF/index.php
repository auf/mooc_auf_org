<?php
include_once("inc_session.php") ;
require_once("inc_html.php");
require_once("inc_indispos.php");
echo $dtd1 ;
echo "<title>" . $titreIndispos . "</title>" ;
//echo $htmlJquery ;
echo '<link href="/css/calendar.css" rel="stylesheet" type="text/css" media="screen, print" />' ;
echo $dtd2 ;
require_once("inc_menu.php");
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titreIndispos ;
echo $fin_chemin ;

require_once("inc_mysqli_foad.php");
$cnx_foad = connecter_foad() ;

require_once("inc_calendar.php") ;
require_once("inc_cnf.php") ;
require_once("inc_form_select.php") ;
require_once("inc_date.php") ;
require_once("inc_pays.php") ;

if ( intval($_SESSION["id"]) == 0 )
{
	echo "<p class='c noprint' style='margin-top:0;'><strong>" ;
	echo "<a href='/agendaCNF/indispo.php'>Ajouter une date ...</a>" ;
	echo "</strong></p>\n" ;
}

//
// Initialisation des dates
//
$mois_courant = date("m", time()) ;
$annee_courante = date("Y", time()) ;
if ( !isset($_SESSION["filtres"]["agendaCNF"]["annee"])  OR ($_SESSION["filtres"]["agendaCNF"]["annee"] == "" ) )
{
	$_SESSION["filtres"]["agendaCNF"]["annee"] = $annee_courante ;
}
if ( !isset($_SESSION["filtres"]["agendaCNF"]["mois"]) )
{
	$_SESSION["filtres"]["agendaCNF"]["mois"]  = $mois_courant ;
}
$A = $_SESSION["filtres"]["agendaCNF"]["annee"] ;
$M = $_SESSION["filtres"]["agendaCNF"]["mois"] ;

// mois (entre 0 et 13) et annee suivant et precedent
$sA = $A + 1 ;
$pA = $A - 1 ;
$sM = sprintf("%02s", $M + 1) ;
$pM = sprintf("%02s", $M - 1) ;

$date_debut = $A . "-" . $M  . "-01" ;
if ( $M == "01" ) {
	$moisPrecedent = "12" ;
	$anneePrecedent = $pA ;
	$moisSuivant = $sM ;
	$anneeSuivant = $A ;
	$date_fin = $sA . "-" . "01-01" ;
}
else if ( $M == "12" ) {
	$moisPrecedent = $pM ;
	$anneePrecedent = $A ;
	$moisSuivant = "01" ;
	$anneeSuivant = $sA ;
	$date_fin = $sA . "-" . "01-01" ;
}
else if ( $M == "" ) {
	$moisPrecedent = "" ;
	$anneePrecedent = $pA ;
	$moisSuivant = "" ;
	$anneeSuivant = $sA ;
	$date_debut = $A . "-" . "01"  . "-01" ;
	$date_fin = $sA . "-" . "01-01" ;
}
else {
	$moisPrecedent = $pM ;
	$anneePrecedent = $A ;
	$moisSuivant = $sM ;
	$anneeSuivant = $A ;
	$date_fin = $A . "-" . $sM . "-01" ;
}
$periode = ucfirst(moisAlpha($M)) . " " . $A ;
$periodeSuivant = ucfirst(moisAlpha($moisSuivant)) . " " . $anneeSuivant ;
$periodePrecedent = ucfirst(moisAlpha($moisPrecedent)) . " " . $anneePrecedent ;

// echo "$a - $m - $date_debut - $date_fin " ;
//print_r($_SESSION["indispos"]) ;

echo "<form method='post' action='criteres.php' class='noprint'>" ;
echo "<table class='formulaire'>\n" ;
echo "<tr>\n" ;
echo "<th>Afficher l'agenda de : </th>\n" ;
echo "<td>" ;
selectMoisAlpha("mois", $_SESSION["filtres"]["agendaCNF"]["mois"]) ;
selectAnneeAgenda("annee", $_SESSION["filtres"]["agendaCNF"]["annee"]) ;
echo " (choisir au moins une année)" ;
echo "<br /><a href='criteres.php?mois=".$mois_courant."&annee=".$annee_courante."'>" ;
echo "Réinitialiser (mois courant)</a>" ;
echo "</td></tr>\n" ;

echo "<tr>\n<th>" ;
echo "<label for='lieu'>Limiter à un CNF : </label></th>\n<td>" ;
if ( isset($_SESSION["filtres"]["agendaCNF"]["lieu"]) ) {
	form_select_1($CNF, "lieu", $_SESSION["filtres"]["agendaCNF"]["lieu"]) ;
}
else {
	form_select_1($CNF, "lieu", "") ;
}
echo "</td></tr>\n" ;

echo "<tr><td colspan='2'>\n" ;
echo "<p class='c'><input class='b' type='submit' value='Actualiser' /></p>\n" ;
echo "</td></tr>\n" ;

echo "</table>\n" ;
echo "</form>\n" ;

$req = "SELECT indispos.*, GROUP_CONCAT(cnf ORDER BY cnf SEPARATOR ', ') AS liste
	FROM indispos LEFT JOIN indispos_cnf ON id_indispo=ref_indispo
	WHERE date_indispo >= '".$date_debut."'
	AND date_indispo < '".$date_fin."' " ;
if ( isset($_SESSION["filtres"]["agendaCNF"]["lieu"]) AND ($_SESSION["filtres"]["agendaCNF"]["lieu"] != "") ) {
	$req .= " AND cnf='".$_SESSION["filtres"]["agendaCNF"]["lieu"]."' " ;
}
$req .= " GROUP BY id_indispo
	ORDER BY date_indispo, id_indispo" ;
//echo $req ;
$res = mysqli_query($cnx_foad, $req) ;
$agenda = array() ;
while ( $row = mysqli_fetch_assoc($res) ) {
	$tab = explode("-", $row["date_indispo"]) ;
	$jour = intval($tab[2]) ;
	$mois = intval($tab[1]) ;
	$agenda[$mois][$jour][] = array(
		"id_indispo" => $row["id_indispo"],
		"commentaire" => $row["commentaire"],
		"liste" => $row["liste"]
	) ;
}
/*
echo "<pre>" ;
print_r($agenda) ;
echo "</pre>" ;
*/

if ( $_SESSION["filtres"]["agendaCNF"]["mois"] == "" )
{
	echo "<div style='width: 990px; margin: 0.5em auto;'>\n" ;
	echo "<h2 id='courant' class='c' style='margin: 0; padding: 0;'>$A</h2>\n" ;
	for ( $i=1 ; $i<=12 ; $i++ )
	{
		echo "<h4 style='float: left; width: 15em; margin: 0; padding: 0;'>" ;
		echo "<a href='criteres.php?mois=".$moisPrecedent."&annee=".$anneePrecedent."'>" ;
		echo $periodePrecedent ;
		echo "</a></h4>\n" ;
		echo "<h4 style='float: right; width: 15em; text-align: right; margin: 0; padding: 0;'>" ;
		echo "<a href='criteres.php?mois=".$moisSuivant."&annee=".$anneeSuivant."'>" ;
		echo $periodeSuivant ;
		echo "</a></h4>\n" ;
		$periode = ucfirst(moisAlpha($i)) . " " . $A ;
		echo "<h2 id='courant' class='c' style='clear: both; margin: 0; padding: 0;'>$periode</h2>\n" ;

		echo draw_calendar($i,
			( isset($_SESSION["filtres"]["agendaCNF"]["annee"]) ? $_SESSION["filtres"]["agendaCNF"]["annee"] : "" ),
			( isset($agenda["$i"]) ? $agenda["$i"] : "" )
			) ;
	}
	echo "</div>\n" ;
}
else
{
	echo "<div style='width: 990px; margin: 0.5em auto;'>\n" ;
	echo "<h4 style='float: left; width: 15em; margin: 0; padding: 0;'>" ;
	echo "<a href='criteres.php?mois=".$moisPrecedent."&annee=".$anneePrecedent."'>" ;
	echo $periodePrecedent ;
	echo "</a></h4>\n" ;
	echo "<h4 style='float: right; width: 15em; text-align: right; margin: 0; padding: 0;'>" ;
	echo "<a href='criteres.php?mois=".$moisSuivant."&annee=".$anneeSuivant."'>" ;
	echo $periodeSuivant ;
	echo "</a></h4>\n" ;
	echo "<h2 id='courant' class='c' style='margin: 0; padding: 0;'>$periode</h2>\n" ;
	echo "</div>\n" ;

	echo draw_calendar($_SESSION["filtres"]["agendaCNF"]["mois"],
		$_SESSION["filtres"]["agendaCNF"]["annee"],
		$agenda[intval($_SESSION["filtres"]["agendaCNF"]["mois"])]) ;
}


/*
if ( mysqli_num_rows($res) == 0 ) {
}
else {
}

echo "<pre>" ;
print_r($nombre_dates) ;
print_r($inscrits) ;
print_r($promos_inscrits) ;
print_r($promos_lieu) ;
echo "</pre>" ;
*/


deconnecter_foad($cnx_foad) ;
echo $end ;
?>	
