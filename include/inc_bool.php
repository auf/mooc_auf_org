<?php

function liste_actif_inactif($nom, $value)
{
	$tab = array(
		"" => " ",	
		"1" => "Actif",	
		"0" => "Inactif"	
	) ;
	$form = "" ;
	$form .= "<select name='$nom'>\n" ;
	while ( list($key, $val) = each($tab) ) {
		$form .= "<option value='$key'" ;
		if ( strval($value) == strval($key) ) {
			$form .= " selected='selected'\n" ;
		}
		$form .= ">$val</option>\n" ;
	}
	$form .= "</select>\n" ;
	echo $form ;
}

function radioActifInactif($nom, $value)
{
	$form = "" ;
	if ( $value == "" ) {
		$value = "1" ;
	}
	$form .= "<label class='bl' for='".$nom."1'>" ;
	$form .= "<input type='radio' name='$nom' id='".$nom."1' value='1' " ;
	if ( $value == "1" ) {
		$form .= " checked='checked'" ;
	}
	$form .= "/> Actif</label>" ;
	$form .= "<label class='bl' for='".$nom."0'>" ;
	$form .= "<input type='radio' name='$nom' id='".$nom."0' value='0' " ;
	if ( $value == "0" ) {
		$form .= " checked='checked'" ;
	}
	$form .= "/> Inactif</label>" ;
	return $form ;
}

function radioSecret($nom, $value)
{
	$form = "" ;
	if ( $value == "" ) {
		$value = "0" ;
	}
	$form .= "<label class='bl' for='".$nom."1'>" ;
	$form .= "<input type='radio' name='$nom' id='".$nom."1' value='1' " ;
	if ( $value == "1" ) {
		$form .= " checked='checked'" ;
	}
	$form .= "/> <span class='Non'>Non</span> : contenu du message archivé non consultable par les CNF (e.g. mots de passe...)</label>" ;
	$form .= "<label class='bl' for='".$nom."0'>" ;
	$form .= "<input type='radio' name='$nom' id='".$nom."0' value='0' " ;
	if ( $value == "0" ) {
		$form .= " checked='checked'" ;
	}
	$form .= "/> Oui : contenu du message archivé consultable par les CNF</label>" ;
	return $form ;
}

?>
