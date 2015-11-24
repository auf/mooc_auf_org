<?php
function erreurs_string($erreurs)
{
	$string = "" ;
	if ( count($erreurs) != 0 ) {
		$string = "<ul class='erreur c'>\n" ;
		foreach ($erreurs as $erreur) {
			$string .= "<li>$erreur</li>\n" ;
		}
		$string .= "</ul>\n" ;
	}
	return $string ;
}

?>
