<?php
function formulaire_upload($action, $hidden, $name, $size)
{
	echo "<form enctype='multipart/form-data' method='post' " ;
	echo "action='$action' />\n" ;
	echo "<table class='formulaire'><tr><td>\n" ;
	while ( list($key, $val)= each($hidden) ) {
		echo "<input type='hidden' name='$key' value='$val' />\n" ;
	}
	echo "<input type='file' name='$name' size='$size' />\n" ;
	echo "<input type='submit' style='font-weight:bold;' " ;
	echo "value='Charger ce fichier' />\n" ;
	echo "</td></tr></table>\n" ;
	echo "</form>\n" ;
}


?>

