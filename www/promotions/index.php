<?php
include("inc_session.php") ;
if ( intval($_SESSION["id"]) != 0 ) {
	header("Location: /bienvenue.php") ;
	exit() ;
}

$titre = "Inscriptions" ;
include("inc_html.php");
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
include("inc_menu.php");
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

?>
<p class='c'><strong><a
	href="promotion.php?operation=add">Nouvelle inscription</a></strong></p>

<?php

include_once("inc_date.php");
include_once("inc_guillemets.php");
require_once("inc_groupe.php");

include_once("inc_mysqli.php");
$cnx = connecter() ;

if ( !isset($_SESSION["filtres"]["promotions"]["annee"]) ) {
	$_SESSION["filtres"]["promotions"]["annee"] = $_SESSION["derniere_annee"] ;
}



echo "<form method='post' action='criteres.php'>\n" ;
echo "<table class='formulaire'>\n<tbody>\n" ;
// On ne peut pas compter sur $_SESSION["derniere_annee"], car quand on ajoute
// une nouvelle promotion pour une année ultérieure, on ne peut pas y accéder
// sans se déconnecter et se reconnecter.
echo "<tr>\n" ;
echo "<th rowspan='2'>Limiter à&nbsp;: </th>\n" ;
echo "<th>Année&nbsp;: </th>\n" ;
echo "<td><select name='p_annee'>\n" ;
$req = "SELECT DISTINCT(annee) FROM session ORDER BY annee DESC" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	echo "<option value='".$enr["annee"]."'" ;
	if ( $_SESSION["filtres"]["promotions"]["annee"] == $enr["annee"] ) {
		echo " selected='selected'" ;
	}
	echo ">".$enr["annee"]."</option>" ;
}
echo "</select></td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Domaine : </th>\n" ;
echo "<td>" ;
echo select_groupe(
	( isset($_SESSION["filtres"]["promotions"]["groupe"]) ? $_SESSION["filtres"]["promotions"]["groupe"] : "" )
	) ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Afficher : </th>\n" ;
echo "<td colspan='2'><label><input name='p_exam' type='checkbox' value='p_exam' " ;
if ( isset($_SESSION["filtres"]["promotions"]["exam"]) AND ($_SESSION["filtres"]["promotions"]["exam"] == "p_exam") )
{
	echo " checked='checked'" ;
}
echo " /> Dates d'examen</label></td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n<td colspan='3'>" . FILTRE_BOUTON_LIEN . "</td>\n</tr>\n" ;
echo "</tbody>\n</table>\n" ;
echo "</form>" ;
echo "<br />" ;

$req = "SELECT COUNT(*) FROM session
	WHERE annee=".$_SESSION["filtres"]["promotions"]["annee"] ;
$res = mysqli_query($cnx, $req);
$enr = mysqli_fetch_row($res) ;
$nombre_annee = $enr[0] ;


$req = "SELECT intitule, groupe, session.*
	FROM session, atelier
	WHERE atelier.id_atelier=session.ref_atelier
	AND annee=".$_SESSION["filtres"]["promotions"]["annee"]." " ;
if ( isset($_SESSION["filtres"]["promotions"]["groupe"]) AND ($_SESSION["filtres"]["promotions"]["groupe"] != "") ) {
	$req .= " AND groupe='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["promotions"]["groupe"])."' " ;
}
$req .= " ORDER BY  groupe, niveau, intitule" ;
//echo $req ;
$res = mysqli_query($cnx, $req);
$nombre_groupe = mysqli_num_rows($res) ;
$promos = array() ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	$promos[] = $enr ;
}


echo "<p class='c'><strong>" ;
echo "$nombre_annee inscriptions en ".$_SESSION["filtres"]["promotions"]["annee"] ;
if ( isset($_SESSION["filtres"]["promotions"]["groupe"]) AND ($_SESSION["filtres"]["promotions"]["groupe"] != "") ) {
	echo "<br />\n" ;
	echo "$nombre_groupe inscriptions en ".$_SESSION["filtres"]["promotions"]["groupe"] ;
}
echo "</strong></p>\n\n" ;



echo "<table class='tableau'>\n";
echo "<thead>\n" ;
echo "<tr style='height: 8.5em;'>\n" ;
echo "\t<th class='invisible'></th>\n";
if ( isset($_SESSION["filtres"]["promotions"]["exam"]) AND ($_SESSION["filtres"]["promotions"]["exam"] == "p_exam") )
{
	echo "<th>Dates d'examens</th>" ;
}

/*
echo "\t<th><div style='font-size: 1.5em'>" ;
echo "</div>" ;
echo "</th>\n";
*/
echo "\t<th class='vertical'><div>ID MOOC</div></th>\n";
echo "\t<th class='vertical'><div>Pièce d'identité</div></th>\n";
echo "\t<th class='vertical'><div>Pièces jointes</div></th>\n";
echo "\t<th>Code imputation<br />Tarifs</th>\n";
echo "\t<th class='vertical help' title=\"ECTS : Système européen de transfert et d’accumulation de crédits
(European Credits Transfer System)\"><div>ECTS</div></th>\n";
echo "\t<th>Formation<br />Inscription</th>\n";
echo "\t<th>Pré-inscriptions</th>\n";
echo "\t<th>Imputations</th>\n";
//echo "\t<th>Dates de la<br />formation</th>\n";
echo "\t<th>Examen</th>\n";
echo "</tr>\n" ;
echo "</thead>\n" ;
echo "<tbody>\n" ;


if ( isset($_SESSION["filtres"]["promotions"]["exam"]) AND ($_SESSION["filtres"]["promotions"]["exam"] == "p_exam") )
{
	$colspan = "10" ;
}
else {
	$colspan = "9" ;
}

$i=0 ;
$annee_precedente = "aucune" ;
$groupe = "" ;
foreach ($promos as $promo)
{
	$lien = "?session=".$promo["id_session"] ;

    if ( $groupe != $promo["groupe"] ) {
        $groupe = $promo["groupe"] ;
        echo "<tr class='groupe'>" ;
		echo "<td class='invisible'></td>" ;
		echo "<td style='background: #333; color: #fff; font-size: larger;' colspan='$colspan'>" ;
        echo "<strong>$groupe</strong></td>" ;
		echo "</tr>" ;
    }

	echo "<tr id='p".$promo["id_session"]."'>\n" ;

	// Suppression
	$req = "SELECT COUNT(id_dossier) FROM dossier
		WHERE ref_session=".$promo["id_session"] ;
	$res = mysqli_query($cnx, $req) ;
	$ligne = mysqli_fetch_row($res) ;
	$N = $ligne[0] ;
	if ( intval($N) == 0 ) 
	{
		echo "<td><a href='supprimer_promotion.php".$lien."'>Supprimer</a></td>\n" ;
	}
	else {
		echo "<td class='invisible'></td>\n" ;
	}

	// Examens
	if ( isset($_SESSION["filtres"]["promotions"]["exam"]) AND ($_SESSION["filtres"]["promotions"]["exam"] == "p_exam") )
	{
		echo "\t<td class='r'>" ;
/*
		echo "<div style='font-size: smaller;'>" ;
		echo "<a href='/statistiques/promotion.php".$lien."'>Statistiques</a>" ;
		echo "</div>\n" ;
		if ( $promo["evaluations"] == "Oui" ) {
			echo "<a href='/candidatures/candidatures.php?id_session=" ;
			echo $promo["id_session"] . "'>Candidatures</a></div>\n" ;
		}
*/
		//echo "<span class='addexam'>" ;
		echo "<div style='text-align: left'>" ;
		echo "<a href='/examens/ajout.php" ;
		echo  "?promotion=" ;
		echo $promo["id_session"] ;
		echo "'>Ajouter</a>" ;
		echo "</div>" ;

		$req = "SELECT * FROM examens WHERE ref_session=".$promo["id_session"]
			. " ORDER BY date_examen" ;
		$res = mysqli_query($cnx, $req);
		while ( $row = mysqli_fetch_assoc($res) ) {
			echo "<div><a id='e".$row["id_examen"]."' href='/examens/examen.php?action=maj&amp;id_examen="
				. $row["id_examen"]
				."' title='".enleve_guillemets($row["commentaire"])."'>" ;
			echo dateComplete($row["date_examen"]) . "</a></div>" ;
		}

		echo "</td>\n" ;
	}

	if ( $promo["idmooc"] == "1" ) {
		if ( trim($promo["consignes_idmooc"]) != "" ) {
			echo "<td class='help' title=\"".$promo["consignes_idmooc"]."\">Oui</td>\n" ;
		}
		else {
			echo "<td class='s'>Oui</td>\n" ;
		}
	}
	else {
		echo "<td></td>\n" ;
	}

	if ( $promo["identite"] == "1" ) {
		if ( trim($promo["consignes_identite"]) != "" ) {
			echo "<td class='help' title=\"".$promo["consignes_identite"]."\">Oui</td>\n" ;
		}
		else {
			echo "<td class='s'>Oui</td>\n" ;
		}
	}
	else {
		echo "<td></td>\n" ;
	}

	if ( $promo["pj"] == "1" ) {
		if ( trim($promo["consignes_pj"]) != "" ) {
			echo "<td class='help' title=\"".$promo["consignes_pj"]."\">Oui</td>\n" ;
		}
		else {
			echo "<td class='s'>Oui</td>\n" ;
		}
	}
	else {
		echo "<td></td>\n" ;
	}

	// Imputation, Tarifs
	echo "<td>" ;
	echo $promo["code_imputation"] ;
	echo " <span class='sep'>-</span> " ;
	echo $promo["tarif"] . "&euro;" ;
	echo "<br />" ;
	echo $promo["tarif1"] . "&euro;" ;
	echo " <span class='sep'>-</span> " ;
	echo $promo["tarif2"] . "&euro;" ;
	echo " <span class='sep'>-</span> " ;
	echo $promo["tarif3"] . "&euro;" ;
	echo "</td>\n" ;

	// ECTS
	echo "\t<td class='r'>" . $promo["ects"] . "</td>\n" ;

	// Inscription
	echo "\t<td class='l" ;
	if ( trim($promo["chapeau"]) != "" ) {
		echo " help' title=\"". trim($promo["chapeau"]) ."\"" ;
	} 
	else {
		echo "'" ;
	}
	echo ">" ;
	echo "<div style='font-size: 1em;'>" ;
		echo "<strong>" . $promo["intitule"] . "</strong>\n" ;
		echo "<a class='bl' href='promotion.php".$lien."&amp;operation=modif'>" ;
		echo "<strong>" . $promo["intit_ses"] . "</strong>" ;
		echo "</a>" ;
	echo "</div>" ;

	/*
	echo "<div style='font-size: smaller; padding-top: 2px;'>" ;
	echo "\t<div class='fright' style='margin-left: 2em;'>" . $promo["ects"] . " ECTS</div>\n" ;
	echo $promo["code_imputation"] ;
	echo " <span class='sep'>-</span> " ;
	echo $promo["tarif"] . "&euro;" ;
	echo " <span class='sep'>-</span> " ;
	echo $promo["tarif1"] . "&euro;" ;
	echo " <span class='sep'>-</span> " ;
	echo $promo["tarif2"] . "&euro;" ;
	echo " <span class='sep'>-</span> " ;
	echo $promo["tarif3"] . "&euro;" ;
	echo "</div>\n" ;
	*/

	echo "</td>\n" ;

	echo "\t<td class='r ". dateOuiNon($promo["inscriptions_deb"], $promo["inscriptions_fin"]) ."'>" ;
	echo "Du <span class='date'>".mysql2datenum($promo["inscriptions_deb"])."</span><br />" ;
	echo "au <span class='date'>".mysql2datenum($promo["inscriptions_fin"])."</span></td>\n" ;

	echo "\t<td class='r ". dateOuiNon($promo["imputations_deb"], $promo["imputations_fin"]) ."'>" ;
	echo "Du <span class='date'>".mysql2datenum($promo["imputations_deb"])."</span><br />" ;
	echo "au <span class='date'>".mysql2datenum($promo["imputations_fin"])."</span></td>\n" ;

	/*
	echo "\t<td class='r'>" ;
	echo "Du <span class='date'>".mysql2datenum($promo["date_deb"])."</span><br />" ;
	echo "au <span class='date'>".mysql2datenum($promo["date_fin"])."</span></td>\n" ;
	*/

	echo "\t<td class='r'>" ;
	echo "<span class='date'>".mysql2datenum($promo["date_examen"])."</span></td>\n" ;

	echo "</tr>\n" ;
}
echo "</tbody>\n" ;
echo "</table>\n" ;

echo "<br /><br />" ;

deconnecter($cnx) ;
echo $end ;

