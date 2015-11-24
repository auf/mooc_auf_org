<?php

function moisAlpha($m)
{
	$m = intval($m) ;
	$mois = array(
		"0" => "",
		"1" => "janvier",
		"2" => "février",
		"3" => "mars",
		"4" => "avril",
		"5" => "mai",
		"6" => "juin",
		"7" => "juillet",
		"8" => "août",
		"9" => "septembre",
		"10" => "octobre",
		"11" => "novembre",
		"12" => "décembre",
	) ;
	return $mois[$m] ;
}

function mysql2date($date)
{
	if ( strlen($date) == 10 ) {
		$tab = explode("-", $date) ;
		if ( ($tab["0"]=="0000") ) {
			return "" ;
		}
		else {
			return $tab[2]."/".$tab[1]."/".$tab[0] ;
		}
	}
	else {
		return "" ;
	}
}
function date2mysql($date)
{
	if ( strlen($date) == 10 ) {
		$tab = explode("/", $date) ;
		return $tab[2]."-".$tab[1]."-".$tab[0] ;
	}
	else {
		return "" ;
	}
}
function mysql2datenum($date)
{
	if ( strlen($date) != 10 ) {
		return "" ;
	}
	$tab = explode("-", $date) ;
	if ( $date != "0000-00-00" ) {
		return $tab[2]
			."<span class='espace'>/</span>"
			.$tab[1]
			."<span class='espace'>/</span>"
			.$tab[0] ;
	}
	else {
		return "" ;
	}
}
function mysql2datealpha($date)
{
	$tab = explode("-", $date) ;
	if ( $date != "0000-00-00" ) {
		return $tab[2]."&nbsp;".moisAlpha($tab[1])."&nbsp;".$tab[0] ;
	}
	else {
		return "" ;
	}
}
function mysql2datealphajour($date)
{
	$tab = explode("-", $date) ;
	$timestamp = mktime (12, 0, 0, $tab[1], $tab[2], $tab[0]) ;
	$valeur = strftime("%A %e %B %Y", $timestamp) ;
	return $valeur ;
}
function mysql2datejour($date)
{
	$tab = explode("-", $date) ;
	$timestamp = @mktime(12, 0, 0, $tab[1], $tab[2], $tab[0]) ;
	$valeur = strftime("%A", $timestamp) ;
	return $valeur ;
}

function selectJour($name, $value)
{
	$liste  = "<select name='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;
	for ( $i=1 ; $i<=31 ; $i++ )
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
function selectMoisAlpha($name, $value)
{
	$mois = array(
		"01" => "janvier",
		"02" => "février",
		"03" => "mars",
		"04" => "avril",
		"05" => "mai",
		"06" => "juin",
		"07" => "juillet",
		"08" => "août",
		"09" => "septembre",
		"10" => "octobre",
		"11" => "novembre",
		"12" => "décembre",
	) ;
	$liste  = "<select name='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;
	while ( list($key, $val) = each($mois) )
	{
		$liste .= "<option value='$key'" ;
		if ( $value == $key ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">$val</option>\n" ;
	}
	$liste .= "</select>" ;
	echo $liste ;
}
function selectMoisNum($name, $value)
{
	$mois = array(
		"01" => "01",
		"02" => "02",
		"03" => "03",
		"04" => "04",
		"05" => "05",
		"06" => "06",
		"07" => "07",
		"08" => "08",
		"09" => "09",
		"10" => "10",
		"11" => "11",
		"12" => "12",
	) ;
	$liste  = "<select name='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;
	while ( list($key, $val) = each($mois) )
	{
		$liste .= "<option value='$key'" ;
		if ( $value == $key ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">$val</option>\n" ;
	}
	$liste .= "</select>" ;
	echo $liste ;
}

function selectAnneeN($name, $value)
{
	$liste  = "<select name='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;
	for ( $i=date("Y", time()) ; $i>=1930 ; $i-- )
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
function selectAnneeAgenda($name, $value)
{
	$liste  = "<select name='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;
	for ( $i=date("Y", time())+3 ; $i>=2010 ; $i-- )
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
function dateComplete($date)
{
	$valeur  = mysql2datejour($date) . "&nbsp;" ;
	$valeur .= "".mysql2datealpha($date)."" ;
	return $valeur ;
}

function dateOuiNon($deb, $fin)
{
	$dateCourante = date("Y-m-d", time()) ;
	if ( ($deb <= $dateCourante) AND ($dateCourante <= $fin) ) {
		return "Oui" ;
	}
	else {
		return "Non" ;
	}
}
?>
