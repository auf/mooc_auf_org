<?php
echo "\n<form method='post' action='examen.php'>\n" ;
echo "<table class='formulaire'>\n" ;
echo "<tr>\n<th>Promotion&nbsp;:</th>\n<td style='width: 50em;'>" ;
/*
$formPromo = chaine_liste_promotions("promotion_examen", $promotion_examen, "", $cnx) ;
echo $formPromo["form"] ;
echo $formPromo["script"] ;
*/
echo liste_promotions("promotion_examen", "$promotion_examen", $cnx, TRUE) ;
echo "</td>\n</tr>\n" ;
echo "<tr>\n<th>Date d'examen</th>\n<td>" ;
selectJour("jour_examen", $jour_examen) ;
selectMoisAlpha("mois_examen", $mois_examen) ;
selectAnneeExam("annee_examen", $annee_examen) ;
echo "</td>\n</tr>\n" ;
/*
echo "<tr>\n<th>Durée&nbsp;:</th>\n<td>" ;
echo "<div><label class='bl' for='am'><input type='checkbox' name='am' id='am' value='Oui' " ;
if ( $am == "Oui" ) { echo " checked='checked' " ; }
echo "/> Matin</label></div>" ;
echo "<div><label class='bl' for='pm'><input type='checkbox' name='pm' id='pm' value='Oui' " ;
if ( $pm == "Oui" ) { echo " checked='checked' " ; }
echo "/> Après-midi</label></div>" ;
echo "</td>\n</tr>\n" ;
*/
echo "<tr>\n<th>Commentaire&nbsp;:</th>\n<td>" ;
echo "<textarea name='commentaire' rows='3' cols='70'>" ;
echo $commentaire ;
echo "</textarea>" ;
echo "</td>\n</tr>\n" ;

echo "<tr><td colspan='2' class='invisible'>" ;
	echo "<input type='hidden' name='redirect' value='$redirect' />\n" ;
	echo "<input type='hidden' name='action' value='$action' />\n" ;
	echo "<input type='hidden' name='id_examen' value='$id_examen' />\n" ;
	echo "<p class='c'><input class='b' type='submit' value='$submit_libelle' /></p>\n" ;
if ( ($_GET["action"] == "maj") AND isset($_GET["id_examen"]) )
	echo "<p class='r'><a href='examen.php?action=delete&amp;id_examen=$id_examen&amp;ref_session=$promotion_examen'>Supprimer</a></p>" ;
echo "</td>\n</tr>\n" ;

echo "</table>\n" ;
echo "</form>" ;

