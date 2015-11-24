<?php
require_once("inc_config.php") ;
include_once("inc_html.php");

$titre = "Mise à jour d'un dossier d'inscription" ;

if ( SITE_EN_LECTURE_SEULE )
{
    echo $dtd1
        . "<title>".$titre."</title>"
        . $dtd2Public
		. enteteAufMooc($titre)
        . "<h2><p class='c erreur'>" . EN_MAINTENANCE . "</p></h2>\n"
        . $endPublic ;
}
else
{
	echo $dtd1
		. "<title>$titre</title>\n"
		. $dtd2Public
		. enteteAufMooc($titre) ;
	
	if ( $_GET["erreur"] == "1" ) {
		echo "<br /><p class='c erreur' style='font-size: larger;'>Erreur</p>\n" ;
		echo "<p class='c'>Majuscules et minuscules sont significatives dans le champ «&nbsp;Mot de passe&nbsp;».<br />
	Attention aussi à ne pas confondre «&nbsp;0&nbsp;» (zéro) et «&nbsp;O&nbsp;» (o majuscule), ...</p>" ;
	}
	
	?>
	<form action="inscription.php" method="post" class="identification">
	<input type="hidden" name="formulaire" value="maj" />
	<table class='formulaire'>
	<tr>
		<th>Numéro de dossier :</th>
		<td><input type='text' name='id_dossier' value=''/></td>
	</tr><tr>
		<th>Mot de passe :</th>
		<td><input type='password' name='pwd' /></td>
	</tr>
	<tr><td colspan='2'>
		<p class='c'><input type="submit" value="Entrer"></p>
	</td></tr>
	</table>
	</form>
	
	<? 
	echo $endPublic ;
}
?>
