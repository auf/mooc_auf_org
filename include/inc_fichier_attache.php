<?php
function formulaire_fichier_attache()
{
	echo "<form enctype='multipart/form-data' action='candidature.php' method='post'>\n" ;
	echo "<table class='formulaire' style='margin-bottom: 1em;'>\n" ;
	echo "<tr>\n" ;
	echo "<th>Fichier</th>\n" ;
	echo "<td><input type='file' name='$name' size='$size' /></td>\n" ;
	echo "</tr>\n" ;
	echo "<tr>\n" ;
	echo "<th>Description</th>\n" ;
	echo "<td></td>\n" ;
	echo "</tr>\n" ;
	echo "</table>\n" ;
	echo "</form>\n" ;
}
?>
