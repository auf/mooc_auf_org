<?
$verif_saisie = "" ;

if ( $_POST["ref_atelier"] == "" ) {
	$verif_saisie .= "<li>Vous devez sélectionner une formation.</li>" ;
}
if ( $_POST["intit_ses"] == "" ) {
	$verif_saisie .= "<li>Le champ Intitulé est obligatoire.</li>" ;
}


if ( $verif_saisie != "" )
{
	$verif_saisie = "<p class='erreur'>Erreur !</p><ul class='erreur'>\n".$verif_saisie."</ul>\n" ;
}
else {
	$verif_saisie = "ok" ;
}
?>
