<?php

$erreur_signature = "" ;

if ( strlen(trim($T["signature"])) < 4 ) {
	$erreur_signature .= "<li>Vous devez signer votre candidature
	<span class='erreur_champ'>(nom et prénom)</span>.</li>\n" ;
}
if ( strlen(trim($T["ville_res"])) < 3 ) {
	$erreur_signature .= "<li>Vous devez signer votre candidature
	<span class='erreur_champ'>(ville de résidence)</span>.</li>\n" ;
}
if ( strlen(trim($T["date_sign"])) < 8 ) {
	$erreur_signature .= "<li>Vous devez signer votre candidature
	<span class='erreur_champ'>(date de candidature)</span>.</li>" ;
}

?>
