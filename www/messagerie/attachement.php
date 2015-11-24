<?php
include("inc_session.php") ;

if ( empty($_SESSION["messagerie"]["promotion"]) ) {
	header("Location: index.php") ;
	exit ;
}
$promotion = $_SESSION["messagerie"]["promotion"] ;

include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_promotions.php") ;
$promo = idpromotion2nom($promotion, $cnx) ;
$titrePromo = $promo["intitule"]." (".$promo["intit_ses"].")" ;
$titre = "Joindre un fichier" ;
$etat = $_POST["etat"] ;
if ( !empty($etat) ) {
	$titre .= " ($etat)" ;
}

include("inc_html.php") ;
echo $dtd1 ;
echo "<title>$titre  - Nouveau courriel $titrePromo</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie/index.php'>Messagerie</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie/promotion.php?promotion=$promotion'>" ;
echo "$titrePromo</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie/session.php'>Nouveau courriel</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

// Affichage erreur
if ( isset($_SESSION["messagerie"]["uploadErreur"]) ) {
	echo "<p class='c erreur'>" ;
	echo $_SESSION["messagerie"]["uploadErreur"]."</p>\n" ;
	unset($_SESSION["messagerie"]["uploadErreur"]) ;
}

echo "<p class='c'>Renommez éventuellement votre fichier avant<br />" ;
echo "de le joindre pour que son nom soit significatif.</p>\n" ;

echo "<form enctype='multipart/form-data' method='post' " ;
echo "action='/messagerie/upload.php' />\n" ;
echo "<input type='hidden' name='MAX_FILE_SIZE' value='2097152' />\n" ;
echo "<table class='formulaire'>\n" ;
echo "<tr>\n" ;
echo "<th>Fichier&nbsp;:</th>\n" ;
echo "<td><input class='upload' type='file' name='fichier' size='60' /></td>\n" ;
echo "</tr>\n" ;
/*
echo "<tr>\n" ;
echo "<th><label for='nom'>Renommer ce fichier&nbsp;:</label></th>\n" ;
	echo "<td>Laissez ce champ vide pour ne pas renommer votre fichier.<br />" ;
echo "<br />" ;
echo "<input type='text' name='nom' size='70' /></td>\n" ;
	echo "</tr>" ;
*/
echo "</table>\n" ;
echo "<p class='c'><input type='submit' style='font-weight:bold;' " ;
echo "value='Joindre ce fichier' /></p>\n" ;
echo "</form>\n" ;


deconnecter($cnx) ;
?>
