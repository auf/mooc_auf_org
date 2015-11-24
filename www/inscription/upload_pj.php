<?php
echo "<h1>Joindre un fichier</h1>\n" ;
echo "<form enctype='multipart/form-data' method='post' action='inscription.php' />\n";
echo "<input type='hidden' name='MAX_FILE_SIZE' value='2097152' />\n" ;
echo "<input type='hidden' name='formulaire' value='maj' />\n" ;
echo "<input type='hidden' name='id_dossier' value='$id_dossier' />\n" ;
echo "<input type='hidden' name='pwd' value='$pwd' />\n" ;
echo "<table class='formulaire'>\n" ;
echo "<tr>\n<td colspan='2'>\n<p>" ;
echo nl2br($tabInscription["consignes_pj"]) ;
echo "</p></td>\n</tr>\n" ;
echo "<tr>\n<td colspan='2'>\n" ;
	echo "<p>\n" ;
	echo "- Renommez éventuellement votre fichier avant de le joindre pour que son nom soit significatif.<br />\n" ;
	echo "- Types (formats) de fichiers autorisés : Image au format JPEG ou PNG (en priorité), ou (éventuellement) PDF.\n" ;
	echo "</p>\n" ;
echo "</td>\n</tr>\n" ;
echo "<tr>\n" ;
	echo "<th>Fichier&nbsp;:</th>\n" ;
	echo "<td><input class='upload' type='file' name='fichier' size='60' /></td>\n" ;
echo "</tr>\n" ;
/*
echo "<tr>\n" ;
	echo "<th>Titre&nbsp;:</th>\n" ;
	echo "<td><input class='upload' type='text' name='titre' size='60' /></td>\n" ;
echo "</tr>\n" ;
*/
echo "<tr>\n" ;
	echo "<th></th>\n" ;
	echo "<td><input type='checkbox' id='confirmation_pj' name='confirmation_pj' value='oui' />\n" ;
	echo "<label class='case' for='confirmation_pj'>Je certifie sur l'honneur l'authenticité de ce fichier.</label></td>\n" ;
echo "</tr>\n" ;
echo "<tr>\n<td colspan='2'>\n" ;
	echo "<p class='c'><strong>" ;
	echo "<span style='float: left;'><input type='submit' value=\"Annuler (Retour)\" title=\"Retour à la page précédente\" /></span>" ;
	echo "<input type='submit' name='submit' value='Joindre ce fichier' />" ;
	echo "</p>\n" ;
echo "</td>\n</tr>\n" ;
echo "</table>\n" ;
echo "</form>\n" ;
?>
