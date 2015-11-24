<?php
$ETAT_DOSSIER = array(
	"0" => "Inconnu",
	"1" => "Certifié",
	"2" => "Échoué",
) ;

$ETAT_DOSSIER_IMG_CLASS = array(
	"0" => "",
	"1" => "admis",
	"2" => "ajourne",
) ;

function liste_etats($nom, $selected, $empty=FALSE)
{
	global $ETAT_DOSSIER ;
	global $ETAT_DOSSIER_IMG_CLASS ;
	echo "<select name=\"$nom\">\n" ;
	if ( $empty ) {
		echo "<option value=''></option>\n" ;
	}

	while ( list($key, $val) = each($ETAT_DOSSIER) )
	{
		echo "<option value='$key' " ;
		echo "class='".$ETAT_DOSSIER_IMG_CLASS[$key]."'" ;
		if ( strval($key) === strval($selected) ) {
			echo " selected='selected'" ;
		}
		echo ">$val</option>\n" ;
	}
	echo "</select>" ;
	reset($ETAT_DOSSIER) ;
}
?>
