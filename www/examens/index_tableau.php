<?php
include_once("inc_session.php") ;

function date2timestamp($date)
{
	list($a, $m, $j) = explode("-", $date) ;
	$timestamp = mktime(0, 0, 0, $m, $j, $a) ;
	return $timestamp ;
}
function delai($date)
{
	$delai = (int) ( ( date2timestamp($date) - time() ) / (60*60*24) + 1 ) ;
	if ( $delai == 0 ) {
		$reponse = "<span class='help' title=\"Aujourd'hui\">J</span>" ;
	}
	else if ( $delai == 1 ) {
		$reponse = "<span class='help' title=\"Demain\">J-". $delai ."</span>" ;
	}
	else if ( $delai < 0 ) {
		$reponse = "<span class='help' title=\"Il y a ". -1*$delai . " jours\">J+". -1*$delai ."</span>" ;
	}
	else {
		$reponse = "<span class='help' title=\"Dans $delai jours\">J-". $delai ."</span>" ;
	}
	return $reponse ;
}
function am_pm($id_session, $intitule, $N, $NPE)
{
	if ( ( intval($_SESSION["id"]) > 3 ) 
		AND in_array($id_session, $_SESSION["tableau_toutes_promotions"]) )
	{
		echo "<td class='c'><strong>$intitule</strong></td>" ;
	}
	else {
		echo "<td class='c'>$intitule</td>" ;
	}

	if ( (intval($_SESSION["id"]) < 3) OR in_array($id_session, $_SESSION["tableau_toutes_promotions"]) ) {
		echo "<td class='r'><a href='/imputations/promotion.php?promotion=$id_session'>$N</a></td>\n" ;
	}
	else {
		echo "<td class='r'><span class='nombre'>$N</span></td>\n" ;
	}

	echo "<td class='r'>" ;
	echo "<a href='/candidatures/pe.php?id_session=".$id_session ;
	if ( isset($_SESSION["filtres"]["examens"]["pays"]) AND ($_SESSION["filtres"]["examens"]["pays"]!="") )
	{
		echo "&pays=".urlencode($_SESSION["filtres"]["examens"]["pays"]) ;
	}
	echo "'>$NPE</a></td>\n" ;

}
function examensEntete()
{
	echo "\n" ;
	echo "<table class='tableau examens'>\n" ;
	echo "<thead class='noprint'>\n" ;
	echo "<tr>\n" ;
	echo "<th colspan='2'>Date d'examen</th>\n" ;
	echo "<th rowspan='2'>Agenda<br />des CNF</th>\n" ;
	echo "<th rowspan='2'>Promotion</th>\n" ;
	echo "<th rowspan='2' class='help' title=\"Nombre d'inscrits (imputés)\">N</th>\n" ;
	echo "<th rowspan='2' class='help' title=\"Nombre de « Payant établissement »\">PE</th>\n" ;
	echo "<th rowspan='2'>Commentaire</th>\n" ;
	echo "</tr>\n" ;
	echo "<tr>\n" ;
	echo "<th class='help' title=\"Nombre de jours avant la date d'examen\">J</th>\n" ;
	echo "<th>Date</th>\n" ;
//	echo "<th class='help' title='Matin' style='font-size: smaller;'>M</th>\n" ;
//	echo "<th class='help' title='Après-midi' style='font-size: smaller; padding: 1px;'>AM</th>\n" ;
	echo "</tr>\n" ;
	echo "</thead>\n" ;
}


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
	return {minDate: (input.id == 'dateFin' ? 
	    $('#dateDebut').datepick('getDate') : null),  
	    maxDate: (input.id == 'dateDebut' ? 
	    $('#dateFin').datepick('getDate') : null)};  
}
});
</script>
<?php
echo $dtd2 ;
require_once("inc_menu.php");
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "Examens" ;
echo $fin_chemin ;

require_once("inc_mysqli.php");
$cnx = connecter() ;

require_once("inc_cnf.php") ;
require_once("inc_form_select.php") ;
require_once("inc_date.php") ;
require_once("inc_pays.php") ;

//echo "<table style='margin: 0 auto 1em auto; padding: 0;'><tr><td>" ;

echo "<p class='c'><strong><a target='_blank' href='https://foad.refer.org/cnf/'>Espace CNF</a></strong></p>\n" ;

//echo "</td><td>" ;

/*
echo "
<table class='pwd'>
<tr>
<th>Utilisateur : </th>
<td><code>espaceExamensCNF</code></td>
</tr>
<tr>
<th>Mot de passe : </th>
<td><code>S7J8CvJNCez2U</code></td>
</tr>
</table>
" ;
*/

//echo "</td></tr</table>\n" ;

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
echo "<label for='lieu'>Limiter l'affichage du nombre théorique d'inscrits (imputés), N, à un lieu : </label>" ;
if ( isset($_SESSION["filtres"]["examens"]["lieu"]) ) {
	form_select_1($CNF, "lieu", $_SESSION["filtres"]["examens"]["lieu"]) ;
}
else {
	form_select_1($CNF, "lieu", "") ;
}
echo "</td></tr>\n" ;

echo "<tr>\n<td>" ;
echo "<label for='pays'>Limiter l'affichage du nombre de « <span class='payantetab'>Payant établissement</span> », PE, à un pays de résidence : </label>" ;
echo "<select name='pays'>\n" ;
foreach($PAYS as $pays) {
	echo "<option value=\"$pays\"" ;
	if ( isset($_SESSION["filtres"]["examens"]["pays"]) AND ($_SESSION["filtres"]["examens"]["pays"] == $pays) ) {
	    echo " selected='selected'" ;
	}
	echo ">$pays</option>\n" ;
}
echo "</select>\n" ;
echo "</td></tr>\n" ;

echo "<tr><td>\n" ;
echo "<p class='c'><input class='b' type='submit' value='Actualiser' /></p>\n" ;
echo "</td></tr>\n" ;

echo "</table>\n" ;
echo "</form>\n" ;





//
// Nombre d'inscrits (imputés, éventuellement pour un lieu, pour chaque promo
//
$inscrits = array() ;
$req = "SELECT id_session, COUNT(id_imputation) AS N
	FROM dossier, imputations
	WHERE dossier.id_dossier=imputations.ref_dossier " ;
if ( isset($_SESSION["filtres"]["examens"]["lieu"]) AND ($_SESSION["filtres"]["examens"]["lieu"]!="") )
{
	$req .= "AND  imputations.lieu='".$_SESSION["filtres"]["examens"]["lieu"]."' " ;
}
$req .= " GROUP BY dossier.id_session" ;
$res = mysqli_query($cnx, $req) ;
while ( $row = @mysqli_fetch_assoc($res) ) {
	$inscrits[$row["id_session"]] =  $row["N"] ;
}

//
// Nombre de payant établissement
//
$pe = array() ;
$req = "SELECT id_session, COUNT(id_dossier) AS N FROM dossier" ;
if ( isset($_SESSION["filtres"]["examens"]["pays"]) AND ($_SESSION["filtres"]["examens"]["pays"]!="") )
{
	$req .= " JOIN candidat ON dossier.id_candidat=candidat.id_candidat" ;
}
$req .= " WHERE etat_dossier='Payant établissement' " ;
if ( isset($_SESSION["filtres"]["examens"]["pays"]) AND ($_SESSION["filtres"]["examens"]["pays"]!="") )
{
	$req .= "AND  candidat.pays='".$_SESSION["filtres"]["examens"]["pays"]."' " ;
}
$req .= " GROUP BY dossier.id_session" ;
$res = mysqli_query($cnx, $req) ;
while ( $row = @mysqli_fetch_assoc($res) ) {
	$pe[$row["id_session"]] =  $row["N"] ;
}


//
// Dates distinctes et le nombre d'examens (de promos) pour chaque
// Pour les attributs rowspan.
//
$nombre_dates = array() ;
$req = "select date_examen, count(date_examen) AS n
	FROM examens
	WHERE date_examen>='".$_SESSION["filtres"]["examens"]["debut"]."'
	AND date_examen<='".$_SESSION["filtres"]["examens"]["fin"]."'
	GROUP BY date_examen" ;
$res = mysqli_query($cnx, $req) ;
while ( $row = @mysqli_fetch_assoc($res) ) {
	$nombre_dates[$row["date_examen"]] =  $row["n"] ;
}


// La période choisie
$periode = " du " .dateComplete($_SESSION["filtres"]["examens"]["debut"])
	. " au " . dateComplete($_SESSION["filtres"]["examens"]["fin"]) ;


$req = "SELECT indispos.*,
	GROUP_CONCAT(cnf ORDER BY cnf SEPARATOR ', ') AS liste
	FROM indispos LEFT JOIN indispos_cnf ON id_indispo=ref_indispo
	WHERE date_indispo >= '".$_SESSION["filtres"]["examens"]["debut"]."'
	AND date_indispo < '".$_SESSION["filtres"]["examens"]["fin"]."'
	GROUP BY id_indispo
	ORDER BY date_indispo, id_indispo" ;
$res = mysqli_query($cnx, $req) ;
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


$req = "select examens.*, intitule FROM examens, session, atelier
	WHERE atelier.id_atelier=session.id_atelier
	AND date_examen>='".$_SESSION["filtres"]["examens"]["debut"]."'
	AND date_examen<='".$_SESSION["filtres"]["examens"]["fin"]."'
	AND examens.ref_session=session.id_session
	ORDER BY date_examen, groupe, niveau, intitule" ;
$res = mysqli_query($cnx, $req) ;
if ( mysqli_num_rows($res) == 0 ) {
	echo "<p class='c'>Aucun examen $periode.</p>\n" ;
}
else {
	echo "<h3 class='c'>Examens $periode</h3>\n" ;


	// Tableau
	examensEntete() ;
	echo "<tbody>\n" ;
	$compteur = 0 ;
	$date_precedente = "" ;
	//reset($res) ;
	while ( $row = mysqli_fetch_assoc($res) )
	{
		if ( $row["date_examen"] != $date_precedente )
		{
			$compteur++ ;

			$class = $compteur % 2 ? "pair" : "impair" ;

			if ( intval($nombre_dates[$row["date_examen"]]) != 1 ) {
				$rowspan = " rowspan='".$nombre_dates[$row["date_examen"]]."'" ;
			}
			else {
				$rowspan = "" ;
			}

			echo "<tr class='$class'>\n" ;
			
			echo "<td class='c' style='' " . $rowspan . ">" ;
			echo delai($row["date_examen"]) ;
			echo "</td>\n" ;

			echo "<td class='r'" . $rowspan . ">" ;
			echo datecomplete($row["date_examen"]) ;
			echo  "</td>\n" ;

			echo "<td" . $rowspan . ">" ;
			if ( isset($agenda[$row["date_examen"]]) AND is_array($agenda[$row["date_examen"]]) )
			{
				foreach ( $agenda[$row["date_examen"]] as $indispo )
				{
					echo "<div class='ev'>" ;
					echo "<div class='co'>" ;
					echo nl2br($indispo["commentaire"]) ;
					echo "</div>" ;
					echo "<div class='li'>" ;
					echo $indispo["liste"] ;
					echo "</div>" ;
					echo "</div>" ;
				}
			}
			echo  "</td>\n" ;
		}
		else {
			echo "<tr class='$class'>\n" ;
		}
		$date_precedente = $row["date_examen"] ;
		am_pm($row["ref_session"],
			$row["intitule"],
			(isset($inscrits[$row["ref_session"]]) ? $inscrits[$row["ref_session"]] : 0),
			(isset($pe[$row["ref_session"]]) ? $pe[$row["ref_session"]] : 0)
		) ;
		echo "<td>".$row["commentaire"]."</td>\n" ;

		if ( ( intval($_SESSION["id"]) < 2 ) ) {
			echo "<td class='noprint'>" ;
			echo "<span style='font-size: smaller;'><a href='examen.php?action=maj&amp;id_examen=".$row["id_examen"]."'>" ;
			echo "Modifier</a></span>" ;
			echo "</td>\n" ;
		}

		echo "</tr>\n" ;
	}
	echo "</tbody>\n" ;
	echo "</table>\n" ;



	// Nouvel affichage
	$compteur = 0 ;
	$date_precedente = "" ;
	$res = mysqli_query($cnx, $req) ;
	echo "<div id='examens'>\n" ;
	while ( $row = mysqli_fetch_assoc($res) )
	{
		if ( $row["date_examen"] != $date_precedente )
		{
			echo "<div class='dateExamen'>\n" ;
			echo "<h4 id=''>"
				. "<span class='J'>" . delai($row["date_examen"]) . "&nbsp;: </span>"
				. "<span class='date'>" . datecomplete($row["date_examen"]) ."</span>"
				. "<span class='nbExams'>".$nombre_dates[$row["date_examen"]]."</span>"
				. "</h4>\n"	;

			if ( isset($agenda[$row["date_examen"]]) AND is_array($agenda[$row["date_examen"]]) )
			{
				echo "<div class='evs'>" ;
				foreach ( $agenda[$row["date_examen"]] as $indispo )
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
			echo "</div>\n" ;

			$compteur++ ;
			$date_precedente = $row["date_examen"] ;
		}

	}
	echo "</div>\n" ;


}

/*
echo "<pre>" ;
print_r($nombre_dates) ;
print_r($inscrits) ;
print_r($promos_inscrits) ;
print_r($promos_lieu) ;
echo "</pre>" ;
*/

deconnecter($cnx) ;
echo $end ;
?>	
