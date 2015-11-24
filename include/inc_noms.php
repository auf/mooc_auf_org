<?php

function nom($string)
{
	/*
	FIXME : interdire &, # et ; revient à interdire les entités HTML
	auquel cas unmessaged'avertissmeent serait judicieux.
	et risque d'interférer avec le traitement des guillemets
	*/

	// Caractères qui n'ont rien à faire dans un nom
	$string = strtr($string,
		"./()_",
		"     ") ;

	// Espaces avant et après
	$string = trim($string) ;

	// Espaces multiples
	while ( substr_count($string, "  ") > 0 ) {
		$string = str_replace("  ", " ", $string) ;
	}

	// Tirets multiples
	while ( substr_count($string, "--") > 0 ) {
		$string = str_replace("--", "-", $string) ;
	}

	// Espaces avant et après les -
	while ( substr_count($string, "- ") > 0 ) {
		$string = str_replace("- ", "-", $string) ;
	}
	while ( substr_count($string, " -") > 0 ) {
		$string = str_replace(" -", "-", $string) ;
	}

	// Espaces avant et après les apostrophes
	while ( substr_count($string, "' ") > 0 ) {
		$string = str_replace("' ", "'", $string) ;
	}
	while ( substr_count($string, " '") > 0 ) {
		$string = str_replace(" '", "'", $string) ;
	}

	return strtoupper($string) ;
}

function prenom($string)
{
	/*
	FIXME : interdire &, # et ; revient à interdire les entités HTML
	auquel cas unmessaged'avertissmeent serait judicieux.
	et risque d'interférer avec le traitement des guillemets
	*/

	// Caractères qui n'ont rien à faire dans un nom
	$string = strtr($string,
		"./()_",
		"     ") ;

	// Espaces avant et après
	$string = trim($string) ;

	// Espaces multiples
	while ( substr_count($string, "  ") > 0 ) {
		$string = str_replace("  ", " ", $string) ;
	}

	// Tirets multiples
	while ( substr_count($string, "--") > 0 ) {
		$string = str_replace("--", "-", $string) ;
	}

	// Espaces avant et après les -
	while ( substr_count($string, "- ") > 0 ) {
		$string = str_replace("- ", "-", $string) ;
	}
	while ( substr_count($string, " -") > 0 ) {
		$string = str_replace(" -", "-", $string) ;
	}

	// Espaces avant et après les apostrophes
	while ( substr_count($string, "' ") > 0 ) {
		$string = str_replace("' ", "'", $string) ;
	}
	while ( substr_count($string, " '") > 0 ) {
		$string = str_replace(" '", "'", $string) ;
	}

	return $string ;
}

?>
