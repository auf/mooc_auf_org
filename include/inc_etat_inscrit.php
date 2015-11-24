<?php
$ETAT_INSCRIT = array(
	"" => "",
	"P" => "Pré-inscrit",
	"I" => "Inscrit",
) ;

function liste_etat_inscrit($nom, $selected, $empty=FALSE)
{
	global $ETAT_INSCRIT ;
	echo "<select name=\"$nom\">\n" ;

	/*
	if ( $empty ) {
		echo "<option value=''></option>\n" ;
	}
	*/

	while ( list($key, $val) = each($ETAT_INSCRIT) )
	{
		echo "<option value='$key' " ;
		if ( strval($key) === strval($selected) ) {
			echo " selected='selected'" ;
		}
		echo ">$val</option>\n" ;
	}
	echo "</select>" ;
	reset($ETAT_INSCRIT) ;
}
?>
