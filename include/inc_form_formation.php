<?php

function tr_formulaire($libelle, $champ)
{
	echo "<tr>\n" ;
	echo "\t<th>".$libelle."&nbsp;: </th>\n" ;
	echo "\t<td>".$champ."</td>\n" ;
	echo "</tr>\n" ;
}

function input($name, $value, $size, $maxlength)
{
	$champ  = "<input type='text' name='$name' " ;
	$champ .= "size='$size' maxlength='$maxlength' " ;
	$champ .= "value=\"".$value."\" />" ; 
	return $champ ;
}

function submit($value)
{
	$submit  = "<p class='c'>" ;
	$submit .= "<input type='submit' class='b' value='$value' />" ;
	$submit .= "</p>\n" ;
	echo $submit ;
}

function hidden($name, $value)
{
	$hidden = "<input type='hidden' name='$name' value='$value' />\n" ;
	echo $hidden ;
}

function erreur($texte) {
	echo "<p class='erreur'>" ;
	echo $texte ;
	echo "</p>\n" ;
}

?>
