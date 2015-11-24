<?php
// Valeurs = tableau 1 colonne
function form_select_1($tab, $name, $selected, $option_vide=TRUE)
{
	echo "<select name=\"$name\">\n" ;
	if ( $option_vide ) {
		echo "<option value=''></option>\n" ;
	}
	while ( list($champ, $valeur) = each($tab) )
	{
		echo "<option value=\"$valeur\"" ;
		if ( $valeur == $selected ) {
			echo " selected='selected'" ;
		}
		echo ">$valeur</option>\n" ;
	}
	echo "</select>" ;
}

// Valeurs = tableau 2 colonnes : valeur, libellé
function form_select_2($tab, $name, $selected, $option_vide=TRUE)
{
	echo "<select name=\"$name\">\n" ;
	if ( $option_vide ) {
		echo "<option value=''></option>\n" ;
	}
	while ( list($champ, $valeur) = each($tab) )
	{
		echo "<option value='$champ'" ;
		if ( $champ == $selected ) {
			echo " selected='selected'" ;
		}
		echo ">$valeur</option>\n" ;
	}
	echo "</select>" ;
}

// Valeurs = tableau 1 colonne
function formSelect1($tab, $name, $selected, $option_vide=TRUE)
{
	$form = "<select name=\"$name\">\n" ;
	if ( $option_vide ) {
		$form .= "<option value=''></option>\n" ;
	}
	while ( list($champ, $valeur) = each($tab) )
	{
		$form .= "<option value=\"$valeur\"" ;
		if ( $valeur == $selected ) {
			$form .= " selected='selected'" ;
		}
		$form .= ">$valeur</option>\n" ;
	}
	$form .= "</select>" ;
	return $form ;
}

?>
