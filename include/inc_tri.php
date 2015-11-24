<?php
function liste_tri($name, $value)
{
	$TRI = array(
	    "id_dossier" => "Ordre d'arrivée des candidatures",
	    "date_maj" => "Date de mise à jour par le candidat (ordre chronologique inverse)",
	    "civilite" => "Civilité",
	    "nom" => "Nom",
	    "age" => "Age (âge au début de la formation)",
	    "nom_pays" => "Pays de résidence",
	    "etat_dossier" => "&Eacute;tat du dossier",
	    "id_etat_hist" => "Date de mise à jour de l'état du dossier (ordre chronologique inverse)",
		"classement" => "Ordre de classement des candidatures en attente"
	) ;

	echo "<select name='$name'>\n" ;
	while ( list($key, $val) = each($TRI) )
	{
		echo "<option value='$key'" ;
		if ( $value == $key ) {
			echo " selected='selected'" ;
		}
		echo ">$val</option>\n" ;
	}
	echo "</select>" ;
}

function liste_tri2($name, $value)
{
	$TRI = array(
	    "id_dossier" => "Ordre d'arrivée des candidatures",
	    "date_maj" => "Date de mise à jour par le candidat (ordre chronologique inverse)",
	    "civilite" => "Civilité",
	    "nom" => "Nom",
	    "age" => "Age (âge au début de la formation)",
	    "pays" => "Pays de résidence",
	    "etat_dossier" => "&Eacute;tat du dossier",
	    "id_etat_hist" => "Date de mise à jour de l'état du dossier (ordre chronologique inverse)",
		"classement" => "Ordre de classement des candidatures en attente"
	) ;

	echo "<select name='$name'>\n" ;
	while ( list($key, $val) = each($TRI) )
	{
		echo "<option value='$key'" ;
		if ( $value == $key ) {
			echo " selected='selected'" ;
		}
		echo ">$val</option>\n" ;
	}
	echo "</select>" ;
}

?>
