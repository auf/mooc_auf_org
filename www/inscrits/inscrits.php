<?php
include("inc_session.php") ;
include("inc_etat_dossier.php") ;
include("inc_etat_inscrit.php") ;

include("inc_mysqli.php") ;
$cnx = connecter() ;

$id_session = $_GET["id_session"] ;

$requete = "SELECT intitule, session.*
	FROM session, atelier 
	WHERE id_session=$id_session
	AND atelier.id_atelier=session.ref_atelier" ;
$resultat = mysqli_query($cnx, $requete) ;
$formation = mysqli_fetch_assoc($resultat) ;

/*
if (($formation["evaluations"]=="Non") AND ($formation["imputations"]=="Non")) {
	deconnecter($cnx) ;
	header("Location: /inscrits/index.php") ;
	exit ;
}
*/
if ( ( intval($_SESSION["id"]) > 3 )
	AND ( !in_array($_GET["id_session"], $_SESSION["tableau_toutes_promotions"]) ) 
	)
{
	deconnecter($cnx) ;
	header("Location: /inscrits/index.php") ;
	exit ;
}


include("inc_html.php") ;
$titre = "Gestion des inscrits - ".$formation["intitule"]." - ".$formation["intit_ses"] ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
?>
<script type="text/javascript" language="javascript">
<!--
function checkAll() {
	lg=document.forms[1].elements.length;
	for ( i=0;i<lg;i++) {
		if (document.forms[1].elements[i].type=="checkbox") {
			document.forms[1].elements[i].checked=true;
		}
	}   
}	  
function unCheck() {
	lg=document.forms[1].elements.length;
	for ( i=0;i<lg;i++) {
		if (document.forms[1].elements[i].type=="checkbox") {
			document.forms[1].elements[i].checked=false; 
		}
	}
}
-->
</script>
<?php
echo $dtd2 ;
include("inc_menu.php") ;

echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/inscrits/index.php'>Gestion des inscrits</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $formation["intitule"] . " - ".$formation["intit_ses"]."" ;
echo $fin_chemin ;


echo "<form action='criteres.php?id_session=$id_session' method='post'>" ;
echo "<table class='formulaire'>\n" ;
echo "<tr>\n" ;
if ( $formation["idmooc"] == "1" ) {
	echo "<th rowspan='6'>Limiter l'affichage&nbsp;:" ;
}
else {
	echo "<th rowspan='5'>Limiter l'affichage&nbsp;:" ;
}
echo "<br /><div style='font-size: smaller; font-weight: normal; text-align: center;'>Un champ vide signifie&nbsp;:<br />aucune limite.</div>" ;
echo "</th>\n" ;
// Pays
/*
include("inc_pays.php") ;
echo "<th>Pays (de résidence)&nbsp;:</th>\n" ;
echo "<td>" ;
$req = "SELECT DISTINCT dossier.pays_residence, ref_pays.code AS code, ref_pays.nom AS nom
	FROM dossier
	LEFT JOIN ref_pays ON dossier.pays_residence=ref_pays.code
	WHERE ref_session=".$_GET["id_session"]."
	ORDER BY nom" ;
echo selectPays($cnx, "c_pays",
	( isset($_SESSION["filtres"]["inscrits"]["pays"]) ? $_SESSION["filtres"]["inscrits"]["pays"] : "" ),
	$req) ;
echo "</td>\n" ;
echo "</tr>\n" ;
*/
// Nom
/*
echo "<tr>\n" ;
*/
echo "<th><label for='c_nom'>Nom de famille&nbsp;:</label></th>\n" ;
echo "<td><input type='text' name='c_nom' id='c_nom' size='20' maxlength='40' value=\"" ;
if ( isset($_SESSION["filtres"]["inscrits"]["nom"]) ) {
	echo $_SESSION["filtres"]["inscrits"]["nom"] ;
}
echo "\" /></td>\n" ;
echo "<td class='s'>Recherche d'une partie du nom,<br />
insensible à la casse <span class='aide' title='Majuscules et minuscules sont équivalentes'>?</span>
et aux caractères diacritiques <span class='aide' title='Lettres accentuées et ç cédille'>?</span>.</td>\n" ;
echo "</tr>\n" ;
//
if ( $formation["idmooc"] == "1" ) {
	echo "<tr>\n" ;
	echo "<th><label for='c_id_mooc'>Identifiant MOOC&nbsp;:</label></th>\n" ;
	echo "<td><input type='text' name='c_id_mooc' id='c_id_mooc' size='20' maxlength='40' value=\"" ;
	if ( isset($_SESSION["filtres"]["inscrits"]["id_mooc"]) ) {
		echo $_SESSION["filtres"]["inscrits"]["id_mooc"] ;
	}
	echo "\" /></td>\n" ;
	echo "<td class='s'>Idem</td>\n" ;
	echo "</tr>\n" ;
}
//
echo "<tr>\n" ;
echo "<th><label for='c_etat'>État :</label></th>\n" ;
echo "<td colspan='2'>" ;
liste_etat_inscrit("c_etat",
	( isset($_SESSION["filtres"]["inscrits"]["etat"]) ? $_SESSION["filtres"]["inscrits"]["etat"] : "" ),
	TRUE) ;
echo "</td>" ;
echo "</tr>\n" ;
//
echo "<tr>\n" ;
echo "<th><label for='c_lieu_paiement'>Lieu de paiement&nbsp;:</label></th>\n" ;
echo "<td><select name='c_lieu_paiement'>\n" ;
echo "<option value=''></option>\n" ;
$req = "SELECT DISTINCT lieu_paiement FROM dossier, imputations
	WHERE ref_dossier=id_dossier
	AND ref_session=".$_GET["id_session"]."
	ORDER BY lieu_paiement" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	echo "<option value=\"".$enr["lieu_paiement"]."\"" ;
	if	(
			isset($_SESSION["filtres"]["inscrits"]["lieu_paiement"])
			AND ($_SESSION["filtres"]["inscrits"]["lieu_paiement"] == $enr["lieu_paiement"])
		) 
	{
		echo " selected='selected'" ;
	}
	echo ">".$enr["lieu_paiement"]."</option>\n" ;
}
echo "</td>\n" ;
echo "<td rowspan='3' class='s'>\n" ;
echo "<p>Ne concerne que les inscrits.</p>
<p>(Implique État = Inscrit.)</p>" ;
echo "</td>\n" ;
echo "</tr>\n" ;

//
echo "<tr>\n" ;
echo "<th><label for='c_lieu_examen'>Lieu d'examen&nbsp;:</label></th>\n" ;
echo "<td><select name='c_lieu_examen'>\n" ;
echo "<option value=''></option>\n" ;
$req = "SELECT DISTINCT lieu_examen FROM dossier, imputations
	WHERE ref_dossier=id_dossier
	AND ref_session=".$_GET["id_session"]."
	ORDER BY lieu_examen" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	echo "<option value=\"".$enr["lieu_examen"]."\"" ;
	if	(
			isset($_SESSION["filtres"]["inscrits"]["lieu_examen"])
			AND ($_SESSION["filtres"]["inscrits"]["lieu_examen"] == $enr["lieu_examen"])
		) 
	{
		echo " selected='selected'" ;
	}
	echo ">".$enr["lieu_examen"]."</option>\n" ;
}
echo "</td>\n" ;
echo "</tr>\n" ;


// Etat
echo "<tr>\n" ;
echo "<th>Résultat&nbsp;:</th>\n<td>" ;
if ( isset($_SESSION["filtres"]["inscrits"]["etat"]) ) {
	liste_etats("c_etat_dossier", $_SESSION["filtres"]["inscrits"]["etat_dossier"], TRUE) ;
}
else {
	liste_etats("c_etat_dossier", "", TRUE) ;
}
echo "</td>\n" ;
echo "</tr>\n" ;

// Tri
function liste_tri($name, $value)
{
	$TRI = array(
		"genre" => "Genre",
		"nom" => "Nom",
		"id_mooc" => "Identifiant MOOC",
		"age" => "Age (âge le jour de l'examen)",
		"nom_pays" => "Pays de résidence",
		"etat_dossier" => "Résultat",
		"date_maj" => "Date de pré-inscription ou de mise à jour du dossier d'inscription",
		"id_etat_hist" => "Date d'édition ou de mise à jour du résultat",
	) ;

	echo "<select name='$name'>\n" ;
	while ( list($key, $val) = each($TRI) )
	{
		echo "<option value='$key'" ;
		if ( $value == $key ) {
			echo " selected='selected'" ;
		}
		echo ">$val</option>\n" ;
	}
	echo "</select>" ;
}

if ( empty($_SESSION["filtres"]["inscrits"]["tri"]) ) {
	$_SESSION["filtres"]["inscrits"]["tri"] = "date_maj" ;
}
echo "<tr>\n" ;
echo "<th>Trier par :</th>\n" ;
echo "<td colspan='3'>" ;
liste_tri("c_tri", $_SESSION["filtres"]["inscrits"]["tri"]) ;
echo "</td>\n" ;
echo "</tr>\n" ;

// Maximum
echo "<tr>\n" ;
	echo "<th><label for='c_max'>Nombre maximum&nbsp;:</label></th>\n" ;
	echo "<td colspan='3'><select name='c_max'>\n" ;
	echo "<option value=''>50</option>\n" ;
	echo "<option value='100'" ;
	if ( isset($_SESSION["filtres"]["inscrits"]["max"]) AND ($_SESSION["filtres"]["inscrits"]["max"] == "100") ) {
		echo " selected='selected'" ;
	}
	echo ">100</option>\n" ;
	echo "<option value='toutes'" ;
	if ( isset($_SESSION["filtres"]["inscrits"]["max"]) AND ($_SESSION["filtres"]["inscrits"]["max"] == "toutes") ) {
		echo " selected='selected'" ;
	}
	echo ">Toutes</option>\n" ;
	echo "</select></td>\n" ;
	echo "</tr>\n" ;

echo "<tr>\n<td colspan='4'><p class='c'>" ;
echo "<a class='reinitialiser' href='reinitialiser.php?id_session=$id_session'>".LABEL_REINITIALISER."</a>" ;
echo "<input type='submit' value=\"Appliquer ces critères d'affichage\"/>" ;
echo "</td>\n</tr>\n" ;
echo "</table>\n" ;
echo "</form>" ;

echo "<hr />" ;

//FIXME champs
$requeteSELECT = "SELECT
	id_dossier, id_mooc, etat_dossier, date_inscrip, dossier.date_maj, date_maj_etat,
	session.*,
	dossier.id_dossier, dossier.genre, dossier.nom, dossier.prenom,
	(DATEDIFF(date_examen, naissance) DIV 365.25 ) AS age,
	dossier.pays_residence,
	(SELECT nom FROM ref_pays WHERE code=dossier.pays_residence) AS nom_pays,
	id_imputation, lieu_paiement, lieu_examen, imputation, date_imput, date_maj_imput,
	(SELECT MAX(id_etat_hist) FROM etat_hist WHERE ref_dossier=id_dossier) AS id_etat_hist
" ;

$requeteCOUNT = "SELECT COUNT(id_dossier) AS N " ;

$requete = " FROM session, dossier
	LEFT JOIN imputations ON imputations.ref_dossier=dossier.id_dossier
	WHERE dossier.ref_session=$id_session
	AND session.id_session=dossier.ref_session " ;
if ( !empty($_SESSION["filtres"]["inscrits"]["nom"]) ) {
	$requete .= " AND dossier.nom LIKE '%".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["inscrits"]["nom"])."%'" ;
}
if ( !empty($_SESSION["filtres"]["inscrits"]["id_mooc"]) ) {
	$requete .= " AND dossier.id_mooc LIKE '%".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["inscrits"]["id_mooc"])."%'" ;
}
if ( !empty($_SESSION["filtres"]["inscrits"]["etat"]) ) {
	if ( $_SESSION["filtres"]["inscrits"]["etat"] == "P" ) {
		$requete .= " AND id_imputation IS NULL" ;
	}
	else if ( $_SESSION["filtres"]["inscrits"]["etat"] == "I" ) {
		$requete .= " AND id_imputation IS NOT NULL" ;
	}
}
if ( !empty($_SESSION["filtres"]["inscrits"]["lieu_paiement"]) ) {
	$requete .= " AND lieu_paiement='".$_SESSION["filtres"]["inscrits"]["lieu_paiement"]."'" ;
}
if ( !empty($_SESSION["filtres"]["inscrits"]["lieu_examen"]) ) {
	$requete .= " AND lieu_examen='".$_SESSION["filtres"]["inscrits"]["lieu_examen"]."'" ;
}
if ( isset($_SESSION["filtres"]["inscrits"]["etat_dossier"]) AND ($_SESSION["filtres"]["inscrits"]["etat_dossier"] != "") ) {
	$requete .= " AND id_imputation IS NOT NULL" ;
	$requete .= " AND etat_dossier='".$_SESSION["filtres"]["inscrits"]["etat_dossier"]."'" ;
}
if ( !empty($_SESSION["filtres"]["inscrits"]["pays"]) ) {
	$requete .= " AND pays_residence='".$_SESSION["filtres"]["inscrits"]["pays"]."'" ;
}
if ( !empty($_SESSION["filtres"]["inscrits"]["lieu_paiement"]) ) {
	$requete .= " AND lieu_paiement LIKE '".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["inscrits"]["lieu_paiement"])."'" ;
}
if ( !empty($_SESSION["filtres"]["inscrits"]["lieu_examen"]) ) {
	$requete .= " AND lieu_examen LIKE '".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["inscrits"]["lieu_examen"])."'" ;
}

$requete_tri = "" ;
// On ajoute le tri par id_dossier pour pouvoir tenir compte des doublons
// du fait des doubles imputations
if ( !empty($_SESSION["filtres"]["inscrits"]["tri"]) ) {
	if ( $_SESSION["filtres"]["inscrits"]["tri"] == "classement" ) {
		$requete_tri .= " ORDER BY classement, nom, id_dossier" ;
	}
	else if ( $_SESSION["filtres"]["inscrits"]["tri"] == "age" ) {
		$requete_tri .= " ORDER BY naissance DESC, nom, id_dossier" ;
	}
	else {
		$requete_tri .= " ORDER BY ". $_SESSION["filtres"]["inscrits"]["tri"] ;
		if ( ( $_SESSION["filtres"]["inscrits"]["tri"] == "date_maj" ) OR ( $_SESSION["filtres"]["inscrits"]["tri"] == "id_etat_hist" ) ) {
			$requete_tri .= " DESC" ;
		}
		$requete_tri .= ", id_dossier" ;
	}
}

$req = $requeteCOUNT . $requete ;
$result = mysqli_query($cnx, $req) ;
$enr = mysqli_fetch_assoc($result) ;
$nombre_inscrits = $enr["N"] ;

if ( $nombre_inscrits == 0 ) {
	echo "<p class='c'>Aucun inscrit pour les critères sélectionnés.</p>\n" ;
}
else
{
	$req = $requeteSELECT . $requete . $requete_tri ;

	if ( !isset($_SESSION["filtres"]["inscrits"]["max"]) OR ($_SESSION["filtres"]["inscrits"]["max"] == "") ) {
		$req .= " LIMIT 50" ;
		$limite = 50 ;
	}
	else if ( $_SESSION["filtres"]["inscrits"]["max"] == "100" ) {
		$req .= " LIMIT 100" ;
		$limite = 100;
	}
	else {
		// Une limite infranchissable de nombre d'inscriptions
		$limite = 9999999 ;
	}
	//echo $req ;

	$result = mysqli_query($cnx, $req) ;

	$url = "inscrits.php?id_session=".$id_session ;

	include("inc_date.php") ;
	include("inc_identite.php") ;

	if ( isset($_GET["erreur"]) AND ($_GET["erreur"] == "zero") ) {
		echo "<p class='r erreur'>Vous devez sélectionner au moins un inscrit.</p>\n" ;
	}

	// Droit d'édition de l'etat d'une inscription FIXME
	$flag_edition = FALSE ;

	if (
			( $_SESSION["id"] == "00" )
			OR
			(
				(intval($_SESSION["id"]) > 9 )
				//AND ( dateOuiNon($enr["imputations_deb"], $enr["imputations_fin"]) == "Oui" )
 			)
		)
	{
		$flag_edition = TRUE ;
	}

	if ( $flag_edition )
	{
		echo "<form method='post' action='action.php'>\n" ;
		echo "<input type='hidden' name='promotion' value='".$id_session."' />\n" ;
		
		echo "<div style='width: 19em; float: right;'>\n" ;
		echo "<table class='formulaire' style='margin-bottom: 4px; margin: 0;'>\n" ;
		echo "<tr>\n" ;
		echo "<th colspan='2'>Éditer le résultat des inscrits cochés&nbsp;:</th>\n" ;
		echo "</tr>\n" ;

		echo "<tr>\n<th>Résultat&nbsp;:</th>\n<td> " ;
		liste_etats("nouvel_etat", "") ;
		echo "<input type='submit' style='font-weight:bold' name='changement_etat' " ;
		echo "value='OK' />" ;
		echo "</td>\n</tr>\n" ;

		echo "<tr>\n<th colspan='3' style='text-align: center;'>" ;
		echo "<a href='javascript:checkAll()'>Tout cocher</a> - " ;
		echo "<a href='javascript:unCheck()'>Tout décocher</a>\n" ;
		echo "</th>\n</tr>\n" ;
		echo "</table>\n" ;
		echo "</div>\n" ;
	}

	// Nombre d'inscrits
	if ( $nombre_inscrits > 1 ) { $s = "s" ; } else { $s = "" ; }
	if ( $flag_edition )
	{
		echo "<p class='c' style='margin-left: 19em; padding-top: 3em;'>" ;
	}
	else {
		echo "<p class='c'>" ;
	}
	echo "<strong>" ;
	if ( $nombre_inscrits > $limite ) {
		echo "$limite </strong>/<strong> " ;
	}

	echo "$nombre_inscrits</strong> inscrit$s" ;
	echo "<br />pour ces critères</p>" ;


	// clear
	echo "<div style='clear: both;'></div>\n" ;



	echo "<table class='tableau'>\n";
	echo "<thead>\n" ;
	echo "<tr>\n" ;
		/*
		// Administrateur : suppression
		if ( $_SESSION["id"] == "00" )
		{
			echo "<th colspan='1' rowspan='2'>Action</th>" ;
		}
		*/
		echo "<th colspan='4' style='background: #777;'>Pré-inscrit</th>";
		if ( $formation["idmooc"] == "1" ) {
			echo "\t<th rowspan='2'>ID MOOC</th>\n" ;
		}
		echo "<th rowspan='2' class='invisible'></th>\n" ;
		echo "<th colspan='4' style='background: #555;'>Inscrit</th>";
		echo "<th rowspan='2' class='invisible'></th>\n" ;
		echo "<th colspan='2' style='background: #333;'>Résultat</th>";
	
		// case à cocher
		if ( $flag_edition ) {
			echo "<th rowspan='2' style='border: 0px; background: transparent; color: #000; font-size: 2em; vertical-align: top;'>&darr;</th>" ;
		}


	echo "</tr>\n" ;
	echo "<tr>\n" ;
		echo "<th class='help' title=\"Date de pré-inscription ou de mise à jour du dossier d'inscription\">Date</th>\n" ;
		echo "<th class='help' title='Pays de résidence'>Pays</th>";
		echo "<th>Civilité &nbsp; NOM &nbsp; Prénoms</th>";
		echo "<th class='help' title='Age au début de la formation'>Age " ;


		echo "<th title=\"Date d'imputation ou de mise à jour de l'imputation\">Date</th>" ;
		echo "\t<th class='help' title=\"Imputation comptable\">Imputation</th>\n" ;
		echo "\t<th class='help' title=\"Lieu de paiement\">Paiement</th>\n" ;
		echo "\t<th class='help' title=\"Lieu d'examen\">Examen</th>\n" ;


		echo "<th title=\"Date d'édition ou de mise à jour du résultat\">Date</th>" ;
		echo "\t<th>Résultat</th>\n" ;

	echo "</th>";
	echo "</tr>\n" ; 
	echo "</thead>\n" ;
	echo "<tbody>\n" ;
	
	$i=0;
	while ( $enr = mysqli_fetch_assoc($result) )
	{
		// ligne
		$class = $i % 2 ? "pair" : "impair" ;
		$lien = "?id_dossier=".$enr["id_dossier"] ;
		echo "<tr class='$class' id='d".$enr["id_dossier"]."'>\n" ;
	
		echo "\t<td class='c s'>" . mysql2datenum($enr["date_maj"]) . "</td>\n" ;
	
		// Administrateur : suppression
		/*
		if ( $_SESSION["id"] == "00" )
		{
			echo "\t<td><span>" ;
			//echo intval($enr["ref_dossier"]) ;
			if ( intval($enr["id_imputation"])==0 )
			{
				echo "<a href='supprimer.php?id_dossier=".$enr["id_dossier"]
					."'>Supprimer</a>" ;
			}
			echo "</span></td>\n" ;
		}
		*/

		// Pays
		echo "\t<td class='c'>". $enr["nom_pays"] ."</td>\n" ;
		//echo "\t<td class='c help' title=\"".$enr["nom_pays"]."\">".$enr["pays"]."</td>\n" ;
	
		echo "\t<td><a class='bl' href='inscrit.php".$lien."'>" . identite($enr, TRUE) . "</td>\n" ;
	
		echo "\t<td class='c'>". $enr["age"] ."</td>\n" ;
	
		if ( $formation["idmooc"] == "1" ) {
			echo "\t<td>". $enr["id_mooc"] ."</td>\n" ;
		}

		echo "\t<td class='invisible'></td>\n" ;

		if ( $enr["id_imputation"] != "" ) {
			echo "\t<td class='c s'>". mysql2datenum($enr["date_maj_imput"]) ."</td>\n" ;
			echo "\t<td class='c'><strong><a class='bl' " ;
				echo "href='/imputations/attestation.php?id=" ;
				echo $enr["id_imputation"]."'>" ;
				echo $enr["imputation"] ."</a></strong></td>\n" ;
			echo "\t<td class='c'>". $enr["lieu_paiement"] ."</td>\n" ;
			echo "\t<td class='c'>". $enr["lieu_examen"] ."</td>\n" ;
			
		}
		else {
			echo "<td class='invisible2'></td><td class='invisible2'>" ;
			if	( 
					( intval($_SESSION["id"]) < 3 )
					AND ( dateOuiNon($enr["imputations_deb"], $enr["imputations_fin"]) == "Oui" )
				)
			{
				echo "<strong><a class='bl' href='/imputations/imputer.php"
				. "?id_dossier=" . $enr["id_dossier"]
				. "'>". LIEN_IMPUTER ."</a></strong>\n" ;
			}
			echo "</td><td class='invisible2'></td><td class='invisible2'></td>\n" ;
		}

		// Séparation (colonne vide)
		echo "\t<td class='invisible'></td>\n" ;

		// FIXME date_maj_etat
		if ( $enr["id_imputation"] != "" ) {
			 $maClasse = "" ;
		}
		else {
			 $maClasse = "invisible2" ;
		}
		require_once("inc_historique.php") ;
		echo "\t<td class='c s $maClasse'>" ;
		if ( $enr["date_maj_etat"] != "0000-00-00" )
		{
			if ( $_SESSION["id"] == "00" )
			{
				$hTitle = historiqueTitle($cnx, $enr["id_dossier"]) ;
				if ( $hTitle != "" ) {
					echo $hTitle ;
				}
			}
			echo mysql2date($enr["date_maj_etat"]) ;
			if ( $_SESSION["id"] == "00" )
			{
				if ( $hTitle != "" ) {
					echo "</span>" ;
				}
			}
		}
		echo "</td>\n" ;
		if ( $enr["id_imputation"] != "" ) {
			echo "\t<td class='c $maClasse ".$ETAT_DOSSIER_IMG_CLASS[$enr["etat_dossier"]]."'>". $ETAT_DOSSIER[$enr["etat_dossier"]] ."</td>\n" ;
		}
		else {
			echo "\t<td class='c $maClasse'></td>\n" ;
		}

		// Case à cocher pour changements
		if ( $flag_edition ) {
			// Pas de case pour les imputés ni les Externes
			if ( ($enr["id_imputation"] != "") ) {
				echo "\t<td><input type='checkbox' name='cinscrits[]' " ;
				echo "value='".$enr["id_dossier"]."' /></td>\n" ;
			}
			else {
				echo "\t<td class='$maClasse'></td>\n" ;
			}
		}

		echo "</tr>\n" ;
	
		$i++;
	}
	echo "</tbody></table>\n";
	
	if ( $flag_edition ) {
		echo "</form>\n" ;
	}
}

deconnecter($cnx) ;
//diagnostic() ;
echo $end ;
?>
