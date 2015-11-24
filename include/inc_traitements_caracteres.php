<?php
// Remplacement des caractères diacritiques ISO-8859-1 en ASCII
function sansDiacritiques($chaine)
{
	/*
	// ISO-8859-1 en ASCII + apostrophe codepage
	// Ne fonctionne pas en utf8
	$chaine = strtr($chaine,
		"àáâäÀÁÂÄéèêëÈÉËÊìíîïÌÍÎÏòóôöÒÓÔÖùúûüÙÚÛÜçÇ",
		"aaaaAAAAeeeeEEEEiiiiIIIIooooOOOOuuuuUUUUcC'") ;
	// En utf8, il faut utiliser la version avec tableau.
	*/
	$chaine = strtr($chaine, array(
		"á" => "a",   "à" => "a",   "â" => "a",   "ä" => "a",
		"Á" => "A",   "À" => "A",   "Â" => "A",   "Ä" => "A",
		"é" => "e",   "è" => "e",   "ê" => "e",   "ë" => "e",
		"É" => "E",   "È" => "E",   "Ê" => "E",   "Ë" => "E",
		"í" => "i",   "ì" => "i",   "î" => "i",   "ï" => "i",
		"Í" => "I",   "Ì" => "I",   "Î" => "I",   "Ï" => "I",
		"ó" => "o",   "ò" => "o",   "ô" => "o",   "ö" => "o",
		"Ó" => "O",   "Ò" => "O",   "Ô" => "O",   "Ö" => "O",
		"ú" => "u",   "ù" => "u",   "û" => "u",   "ü" => "u",
		"Ú" => "U",   "Ù" => "U",   "Û" => "U",   "Ü" => "U",
		"ç" => "c",   "Ç" => "C",
		"<92>" => "'",
	) ) ;
	return $chaine ;
}

// Remplacement des non alphanumeriques par des _
// _ fait partie des alphanumerique
function speciauxToUnderscores($chaine)
{
	// Pb : remplace aussi les .
	// $chaine = preg_replace("/\W/", "_", $chaine) ;
	$chaine = preg_replace("/[^a-zA-Z0-9\.]/", "_", $chaine) ;
	// Version unicode
	//$chaine = preg_replace("/[^a-zA-Z0-9\.]/u", "_", $chaine) ;
	return $chaine ;
}

// Suppression des espaces multiples dans une chaine
function sansUnderscoresMultiples($chaine)
{
	$chaine = str_replace("\r", "_", $chaine) ;
	$chaine = str_replace("\n", "_", $chaine) ;
	$chaine = @ereg_replace("[_]+", "_", $chaine) ;
	$chaine = trim($chaine) ;
	return $chaine ;
}

// On conserve . et / pour les URL
function traitementNomFichier($chaine)
{
	$chaine = sansDiacritiques($chaine) ;
	$chaine = speciauxToUnderscores($chaine) ;
	$chaine = sansUnderscoresMultiples($chaine) ;
	return $chaine ;
}

?>
