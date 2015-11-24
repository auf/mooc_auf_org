<?php
include_once("inc_session.php") ;
require_once("inc_html.php");
echo $dtd1 ;
echo "<title>Examens</title>" ;
echo $htmlJquery ;
echo $htmlDatePick ;
?>
<script type="text/javascript">
$(function() {
$('#dateDebut,#dateFin').datepick({beforeShow: customRange, 
	showOn: 'both',showBigPrevNext: true, firstDay: 0}); 
function customRange(input) {  
	return {
		minDate: (input.id == 'dateFin' ? $('#dateDebut').datepick('getDate') : null),  
	    maxDate: (input.id == 'dateDebut' ? $('#dateFin').datepick('getDate') : null)
	};  
}
});
</script>
<?php
echo $dtd2 ;
require_once("inc_menu.php");
echo "<div class='noprint'>" ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "Examens" ;
echo "</div>" ;
echo $fin_chemin ;

require_once("inc_mysqli.php");
$cnx = connecter() ;

require_once("inc_mysqli_foad.php");
$cnx_foad = connecter_foad() ;

require_once("inc_cnf.php") ;
require_once("inc_form_select.php") ;
require_once("inc_date.php") ;
require_once("inc_pays.php") ;
require_once("inc_examens.php") ;
require_once("inc_dateExamenMooc.php") ;
require_once("inc_dateExamenFoad.php") ;

echo "<p class='c noprint'><strong><a target='_blank' href='https://foad.refer.org/cnf/'>Espace CNF</a></strong></p>\n" ;

if ( intval($_SESSION["id"]) == 0 )
{
	echo "<p class='c noprint'><strong><a href='/examens/ajout.php'>Ajouter une date d'examen</a></strong></p>\n" ;
}

// Initialisation des dates si necessaire
if ( !isset($_SESSION["filtres"]["examens"]["debut"]) OR !isset($_SESSION["filtres"]["examens"]["fin"]) )
{
	$_SESSION["filtres"]["examens"]["debut"] = date("Y-m-d", time()) ;
	$_SESSION["filtres"]["examens"]["fin"] = date("Y-m-d", time()+3600*24*30) ;
}

echo "<form method='post' action='criteres.php' class='noprint'>" ;
echo "<table class='formulaire'>\n" ;
echo "<tr><td>\n" ;
echo "Afficher tous les examens du " ;
echo "<input type='text' size='10' id='dateDebut' name='debut'";
echo " value='".mysql2date($_SESSION["filtres"]["examens"]["debut"])."'" ;
echo " />" ;
echo " au " ;
echo "<input type='text' size='10' id='dateFin' name='fin'" ;
echo " value='".mysql2date($_SESSION["filtres"]["examens"]["fin"])."'" ;
echo " />" ;
echo "</td></tr>\n" ;

echo "<tr>\n<td>" ;
echo "<label for='lieu'>Limiter l'affichage du nombre d'inscrits (imputés), N, à un lieu d'examen : </label>" ;
if ( isset($_SESSION["filtres"]["examens"]["lieu"]) ) {
	form_select_1($CNF, "lieu", $_SESSION["filtres"]["examens"]["lieu"]) ;
}
else {
	form_select_1($CNF, "lieu", "") ;
}
echo "</td></tr>\n" ;

echo "<tr>\n<td>" . FILTRE_BOUTON_LIEN . "</td>\n</tr>\n" ;
echo "</table>\n" ;
echo "</form>\n" ;

//
// Dates d'examens
// MOOC puis FOAD
//
$dates_examens = array() ;

$req = "SELECT DISTINCT date_examen
	FROM examens
	WHERE date_examen>='".$_SESSION["filtres"]["examens"]["debut"]."'
	AND date_examen<='".$_SESSION["filtres"]["examens"]["fin"]."'
	ORDER BY date_examen" ;
// MOOC
$res = mysqli_query($cnx, $req) ;
while ( $row = @mysqli_fetch_assoc($res) ) {
	$dates_examens[] =  $row["date_examen"] ;
}
// FOAD
$res = mysqli_query($cnx_foad, $req) ;
while ( $row = @mysqli_fetch_assoc($res) ) {
	if ( !in_array($row["date_examen"], $dates_examens) ) {
		$dates_examens[] =  $row["date_examen"] ;
	}
}
// Dédoublonnage et tri
sort($dates_examens) ;




// 
// Agenda des CNF
// 
$req = "SELECT indispos.*,
	GROUP_CONCAT(cnf ORDER BY cnf SEPARATOR ', ') AS liste
	FROM indispos LEFT JOIN indispos_cnf ON id_indispo=ref_indispo
	WHERE date_indispo >= '".$_SESSION["filtres"]["examens"]["debut"]."'
	AND date_indispo < '".$_SESSION["filtres"]["examens"]["fin"]."'
	GROUP BY id_indispo
	ORDER BY date_indispo, id_indispo" ;
//echo $req ;
$res = mysqli_query($cnx_foad, $req) ;
$agenda = array() ;
while ( $row = mysqli_fetch_assoc($res) ) {
    $agenda[$row["date_indispo"]][] = array(
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


// La période choisie
$periode = " du " .dateComplete($_SESSION["filtres"]["examens"]["debut"])
	. " au " . dateComplete($_SESSION["filtres"]["examens"]["fin"]) ;

$req = "SELECT examens.*, intitule FROM examens, session, atelier
	WHERE atelier.id_atelier=session.ref_atelier
	AND examens.date_examen>='".$_SESSION["filtres"]["examens"]["debut"]."'
	AND examens.date_examen<='".$_SESSION["filtres"]["examens"]["fin"]."'
	AND examens.ref_session=session.id_session
	ORDER BY examens.date_examen, groupe, intitule" ;
//echo $req ;
$res = mysqli_query($cnx, $req) ;
if ( mysqli_num_rows($res) == 0 ) {
	echo "<p class='c'>Aucun examen $periode.</p>\n" ;
}
else {
	echo "<h3 class='c'>Examens $periode</h3>\n" ;
}

echo "<div id='examens'>\n" ;
reset($dates_examens) ;
foreach ( $dates_examens as $date )
{
	echo "<div class='dateExamen'>\n" ;
	echo "<h4 id=''>"
		. "<span class='J'>" . delai($date) . "&nbsp;: </span>"
		. "<span class='date'>" . datecomplete($date) ."</span>"
//		. "<span class='nbExams'>".$nombre_dates[$date]."</span>"
		. "<div style='clear: both;'> </div>"
		. "</h4>\n"	;

	if ( isset($agenda[$date]) AND is_array($agenda[$date]) )
	{
		echo "<div class='evs'>" ;
		foreach ( $agenda[$date] as $indispo )
		{
			echo "<div class='ev'>" ;
			echo "<div class='co'>" ;
			echo nl2br($indispo["commentaire"]) ;
			echo "</div>" ;
			echo "<div class='li'>" ;
			echo $indispo["liste"] ;
			echo "</div>" ;
			echo "<div style='clear: both;'> </div>" ;
			echo "</div>" ;
		}
		echo "</div>\n" ;
	}

	dateExamenMooc($cnx, $date) ;
	dateExamenFoad($cnx_foad, $date, FALSE) ;
	echo "<div style='clear: both;'> </div>" ;

	echo "</div>\n" ;
}
echo "</div>\n" ;

/*
echo "<pre>" ;
print_r($nombre_dates) ;
print_r($inscrits) ;
print_r($promos_inscrits) ;
print_r($promos_lieu) ;
echo "</pre>" ;
*/

deconnecter($cnx) ;
deconnecter_foad($cnx_foad) ;
echo $end ;
?>	
