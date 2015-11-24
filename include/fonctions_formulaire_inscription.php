<?php
function section_candidature($str)
{
	global $SECTION_CANDIDATURE ;
	echo "\n\n<h2 class='formulaire'>" ;
	/*
	if ( $str != "signature") {
		echo $str . ". " ;
	}
	*/
	echo $SECTION_CANDIDATURE[$str] ;
	echo "</h2>\n\n";
}

function table_candidature($str, $float="")
{
	if ( $float != "") {
		echo "<table class='formulaire' style='float: $float;' id='section".$str."'>\n" ;
	}
	else {
		echo "<table class='formulaire' style='width:100%; clear: left;' id='section".$str."'>\n" ;
	}
}

function affiche_si_non_vide($erreurs, $colspan=2)
{
	if ( $erreurs != "" )
	{
		echo "<tr><td colspan='$colspan'>" ;
		echo "<ul class='erreur'>\n" ;
		echo $erreurs ;
		echo "</ul>" ;
		echo "</td>" ;
	}
}

function libelle($champ, $colon=TRUE)
{
	global $CANDIDATURE ;
	echo "<label for='$champ'>" ;
	echo $CANDIDATURE[$champ][0] ;
	if ( $colon ) {
		echo "&nbsp;: " ;
	}
	echo "</label>" ;
}

function radio($tab, $nom_rd, $checked)
{
	while ( list($champ, $valeur) = each($tab) )
	{
		echo "<label>" ;
		echo "<input type='radio' name='$nom_rd' value='$valeur'" ;
		if ( $valeur == $checked ) {
			echo " checked='checked'" ;
		}
		echo " /> $valeur"."</label><br />" ;
	} 
}

function inputtxt($name, $vlr, $size, $maxsize, $type="text")
{
	echo "<input type=\"$type\" id=\"$name\" name=\"$name\" size=\"$size\" maxlength=\"$maxsize\" value=\"$vlr\" " ;
	echo " />" ;
}

function liste_der1($tab, $nom_ld, $selected)
{
	echo "<select name=\"$nom_ld\" id=\"$nom_ld\">\n" ;
	while ( list($champ,$valeur) = each($tab) )
	{ 
		echo "<option value=\"$valeur\"" ;
		if ( $valeur == $selected ) {
			echo " selected='selected'" ;
		}
		echo ">$valeur</option>\n" ;
	}
	echo "</select>" ;
}

function liste_der2($tab, $nom_ld, $selected)
{
	reset($tab) ;
	echo "<select name=\"$nom_ld\">\n" ;
	while ( list($key, $val) = each($tab) )
	{
		echo "<option value=\"".$key."\"" ;
		if ( $key == $selected ) {
			echo " selected='selected'" ;
		}
		echo ">".$val."</option>\n" ;
	}
	echo "</select>" ;
}

function textarea($name, $value, $cols='40', $rows='4')
{
	if ( $name =='cv' ) {
		echo "<textarea name='$name' id='$name' cols='$cols' rows='$rows' class='cv'>" ;
	}
	else {
		echo "<textarea name='$name' id='$name' cols='$cols' rows='$rows'>" ;
	}
	echo $value ;
	echo "</textarea>" ;
}

function obligatoire($champ, $autre_erreur="")
{
	global $CANDIDATURE ;
	$message  = "\t<li>Le champ «&nbsp;<span class='erreur_champ'>" ;
	$message .= $CANDIDATURE[$champ][0] ;
	$message .= "</span>&nbsp;» " ;
	if ( $autre_erreur == "" ) {
		$message .= "est obligatoire" ;
	}
	else {
		$message .= $autre_erreur ;
	}
	$message .= ".</li>\n" ;
	return $message ;
}

function reponse($champ)
{
	global $CANDIDATURE ;
	$message  = "\t<li>La réponse à la question " ;
	$message .= "«&nbsp;<span class='erreur_champ'>" ;
	$message .= $CANDIDATURE[$champ][0] ;
	$message .= "</span>&nbsp;» " ;
	$message .= "est obligatoire" ;
	$message .= ".</li>\n" ;
	return $message ;
}

function affiche_champ($champ, $t)
{
	global $CANDIDATURE ;
	echo "<div><span class='champ'>" ;
	if ( $CANDIDATURE[$champ][1] == "" ) {
		echo $CANDIDATURE[$champ][0] ;
	}
	else {
		echo $CANDIDATURE[$champ][1] ;
	}
	echo "&nbsp;:</span> " ;
	echo nl2br($t[$champ]) ;
	echo "</div>\n" ;
}

function key_generator($size)
{
	$key_g = "";
	$lettre = array('a','b','c','d','e','f','g','h','i','J','k','l','m',
					'n','o','p','q','r','s','t','u','v','W','X','y','z',
					'A','B','C','D','E','F','G','H','I','J','K','L','M',
					'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
					'0','1','2','3','4','5','6','7','8','9') ;
	srand((double)microtime()*date("YmdGis")) ;
	for ( $cnt = 0 ; $cnt < $size ; $cnt++ )
	{   
		$key_g .= $lettre[rand(0, 61)];
	}
	return $key_g;
}
?>
