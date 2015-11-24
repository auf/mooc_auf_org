<?php
require_once("inc_etat_dossier.php") ;
require_once("inc_dossier.php") ;
require_once("inc_date.php") ;

function civilite($T, $abreviation=FALSE)
{
	$civilite  = "" ;
	if ( $T["genre"] == "Homme" ) {
		if ( $abreviation ) {
			$civilite .= "M." ;
		}
		else {
			$civilite .= "Monsieur" ;
		}
	}
	else if ( $T["genre"] == "Femme" ) {
		if ( $abreviation ) {
			$civilite .= "Mme." ;
		}
		else {
			$civilite .= "Madame" ;
		}
	}
	return $civilite ;
}

function  identite($T, $abreviation=FALSE)
{
	$identite = "" ;
	$identite .= civilite($T, $abreviation) . " " ;
	$identite .= "<span class='nom'>" ;
	$identite .= strtoupper($T["nom"]) ;
	$identite .= "</span>" ;
	$identite .= " <span class='prenom'>" ;
	$identite .= ucwords(strtolower($T["prenom"])) ;
	$identite .= "</span>" ;
	return $identite ;
}
?>
