<?php
// 
function selectAnneeExam($name, $value)
{
	// Date courante
	$liste  = "<select name='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;
	for ( $i=(date("Y", time())+1) ; $i>=2004 ; $i-- )
	{
		$liste .= "<option value='$i'" ;
		if ( $value == $i ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">$i</option>\n" ;
	}
	$liste .= "</select>" ;
	echo $liste ;
}

function verifier_examen($post)
{
	$erreurs = "" ;
	if  ($_POST["promotion_examen"] == "" )
	{
		$erreurs .= "<li>Le choix d'une promotion est obligatoire.</li>\n" ;
	}
	if ( ($_POST["annee_examen"] == "" ) OR ($_POST["mois_examen"] == "" )
		OR ($_POST["jour_examen"] == "" ) )
	{
		$erreurs .= "<li>La date est obligatoire.</li>\n" ;
	}
/*
	if ( ($post["am"] != "Oui") AND ($post["pm"] != "Oui") )
	{
		$erreurs .= "<li>Une des deux cases au moins doit être cochée.</li>\n" ;
	}
*/

	if ( $erreurs != "" )
	{
		$erreurs = "<ul class='erreur c'>\n" . $erreurs . "</ul>\n" ;
	}
	return $erreurs ;
}


// Fonctions utilisées dans index.php

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
?>
