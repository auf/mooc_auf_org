<?php
include("inc_session.php") ;
include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_html.php") ;
include("inc_date.php") ;
$titre = "Formations" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

?>
<p class='c'><strong><a href='formation.php?action=ajout'>Nouvelle formation</a></strong></p>
<?php

echo "<form method='post' action='criteres.php'>\n" ;
echo "<table class='formulaire'>\n<tbody>\n" ;
echo "<tr>\n" ;
echo "<th rowspan='4'>Limiter à&nbsp;: </th>\n" ;
echo "<th>Année(s)&nbsp;: </th>\n" ;
echo "<td><select name='formation_annee'>\n" ;
echo "<option value=''></option>\n" ;
$req = "SELECT DISTINCT(annee) FROM session ORDER BY annee DESC" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
    echo "<option value=' = ".$enr["annee"]."'" ;
    if ( isset($_SESSION["filtres"]["formations"]["annee"]) AND ($_SESSION["filtres"]["formations"]["annee"] == " = " . $enr["annee"]) ) {
        echo " selected='selected'" ;
    }
    echo "> = ".$enr["annee"]."</option>" ;
    echo "<option value=' >= ".$enr["annee"]."'" ;
    if ( isset($_SESSION["filtres"]["formations"]["annee"]) AND ($_SESSION["filtres"]["formations"]["annee"] == " >= " . $enr["annee"]) ) {
        echo " selected='selected'" ;
    }
    echo "> &ge; ".$enr["annee"]."</option>" ;
}
echo "</select> <span style='font-size: smaller'>(Limiter aux formations pour lesquelles il existe au moins une inscription)</span></td>\n" ;
echo "</tr>\n" ;

require_once("inc_institutions.php") ;
echo "<tr>\n" ;
echo "<th>Institution principale&nbsp;: </th>\n" ;
echo "<td>" ;
echo listeInstitutions($cnx, "formation_ref_institution",
    ( isset($_SESSION["filtres"]["formations"]["ref_institution"]) ? $_SESSION["filtres"]["formations"]["ref_institution"] : "" ),
	"formations",
    TRUE) ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Domaine&nbsp;: </th>\n" ;
echo "<td><select name='formation_groupe'>\n" ;
echo "<option value=''></option>\n" ;
$req = "SELECT DISTINCT(groupe) FROM atelier ORDER BY groupe" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
    echo "<option value=\"".$enr["groupe"]."\"" ;
    if ( isset($_SESSION["filtres"]["formations"]["groupe"]) AND ($_SESSION["filtres"]["formations"]["groupe"] == $enr["groupe"]) ) {
        echo " selected='selected'" ;
    }
    echo ">".$enr["groupe"]."</option>" ;
}
echo "</select></td>\n" ;
echo "</tr>\n" ;

require_once("inc_disciplines.php") ;
echo "<tr>\n" ;
echo "<th>Discipline principale&nbsp;: </th>\n" ;
echo "<td>" ;
echo selectDiscipline($cnx, "formation_ref_discipline",
    ( isset($_SESSION["filtres"]["formations"]["ref_discipline"]) ? $_SESSION["filtres"]["formations"]["ref_discipline"] : "" ),
	"formations"
	) ;
echo "</td>\n" ;
echo "</tr>\n" ;

/*
echo "<tr>\n" ;
echo "<th>Niveau&nbsp;: </th>\n" ;
echo "<td><select name='formation_niveau'>\n" ;
echo "<option value=''></option>\n" ;
$req = "SELECT DISTINCT(niveau) FROM atelier ORDER BY niveau" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
    echo "<option value=\"".$enr["niveau"]."\"" ;
    if ( isset($_SESSION["filtres"]["formations"]["niveau"]) AND ($_SESSION["filtres"]["formations"]["niveau"] == $enr["niveau"]) ) {
        echo " selected='selected'" ;
    }
    echo ">".$enr["niveau"]."</option>" ;
}
echo "</select></td>\n" ;
echo "</tr>\n" ;
*/


echo "<tr>\n" ;
echo "<th>Rechercher&nbsp;: </th>\n" ;
echo "<th>Intitulé de la formation&nbsp;: </th>\n" ;
echo "<td><input type='text' size='30' name='formation_intitule'" ;
if ( isset($_SESSION["filtres"]["formations"]["intitule"]) ) {
	echo " value=\"".$_SESSION["filtres"]["formations"]["intitule"]."\"" ;
}
echo " />\n" ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n<td colspan='3'>" . FILTRE_BOUTON_LIEN . "</td>\n</tr>\n" ;
echo "</tbody>\n</table>\n" ;
echo "</form>" ;

//"SELECT DISTINCT atelier.* FROM atelier LEFT JOIN session ON atelier.id_atelier=session.ref_atelier WHERE annee>2007 ORDER BY groupe, niveau, intitule" ;

$req  = "SELECT atelier.*,
	ref_discipline.nom AS nom_discipline,
	ref_etablissement.nom AS institution, ref_etablissement.id AS id, ref_etablissement.sigle AS sigle,
	(SELECT COUNT(ref_atelier) FROM session WHERE ref_atelier=id_atelier) AS N
	FROM atelier
	LEFT JOIN ref_etablissement ON ref_institution=ref_etablissement.id
	LEFT JOIN ref_discipline ON ref_discipline.code=atelier.ref_discipline " ;
if ( isset($_SESSION["filtres"]["formations"]["annee"]) AND ($_SESSION["filtres"]["formations"]["annee"] != "") ) {
	$req .= " LEFT JOIN session ON atelier.id_atelier=session.ref_atelier " ;
}
$req .= " WHERE TRUE " ;
if ( isset($_SESSION["filtres"]["formations"]["annee"]) AND ($_SESSION["filtres"]["formations"]["annee"] != "") ) {
	$req .= " AND annee" . $_SESSION["filtres"]["formations"]["annee"] ;
}
if ( isset($_SESSION["filtres"]["formations"]["groupe"]) AND ($_SESSION["filtres"]["formations"]["groupe"] != "") ) {
	$req .= " AND groupe='" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["formations"]["groupe"]) . "' " ;
}
/*
if ( isset($_SESSION["filtres"]["formations"]["niveau"]) AND ($_SESSION["filtres"]["formations"]["niveau"] != "") ) {
	$req .= " AND niveau='" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["formations"]["niveau"]) ."' " ;
}
*/

if ( isset($_SESSION["filtres"]["formations"]["ref_institution"]) AND ($_SESSION["filtres"]["formations"]["ref_institution"] == "-1") ) {
	$req .= " AND ref_institution='0' " ;
}
else if ( isset($_SESSION["filtres"]["formations"]["ref_institution"]) AND ($_SESSION["filtres"]["formations"]["ref_institution"] != "0") ) {
	$req .= " AND ref_institution = '" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["formations"]["ref_institution"]) . "' " ;
}

if ( isset($_SESSION["filtres"]["formations"]["ref_discipline"]) AND ($_SESSION["filtres"]["formations"]["ref_discipline"] == "-1") ) {
	$req .= " AND ref_discipline='' " ;
}
else if ( isset($_SESSION["filtres"]["formations"]["ref_discipline"]) AND ($_SESSION["filtres"]["formations"]["ref_discipline"] != "") ) {
	$req .= " AND ref_discipline = '" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["formations"]["ref_discipline"]) . "' " ;
}


if ( isset($_SESSION["filtres"]["formations"]["intitule"]) AND (trim($_SESSION["filtres"]["formations"]["intitule"]) != "") ) {
	$req .= " AND intitule LIKE'%" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["formations"]["intitule"]) ."%' " ;
}

$req .= " ORDER BY groupe, intitule" ;
//echo $req ;
$res = mysqli_query($cnx, $req);
$nb_formations = mysqli_num_rows($res) ;

echo "<p class='c'><strong>".$nb_formations." formation" ;
if ( $nb_formations > 1 ) { echo "s" ; }
echo "</strong></p>\n" ;

function debut_tableau()
{
	$debut_tableau  = "" ;
	$debut_tableau .= "<table class='tableau'>\n";
	$debut_tableau .= "<thead>\n";
	$debut_tableau .= "<tr>\n";
	$debut_tableau .= "<th class='invisible'></th>\n";
	$debut_tableau .= "<th>Promotions</th>\n";
	$debut_tableau .= "<th>Responsables</th>\n";
	$debut_tableau .= "<th>Institution principale</th>\n";
	$debut_tableau .= "<th><span style='font-weight: normal'>Institution(s) <span style='font-size: smaller;'>(affichée(s) dans les formulaires de candidature</span>)</span><br />Intitulé de la formation<br /><span style='font-weight: normal'><span class='s'>&nbsp; Discipline principale &nbsp; - &nbsp; Responsable (email) &nbsp; - &nbsp; Commentaire</span></span></th>\n";;
	$debut_tableau .= "</tr>\n";
	$debut_tableau .= "</thead>\n";
	$debut_tableau .= "<tbody>\n";
	echo $debut_tableau ;
}
$fin_tableau  = "</tbody>\n</table>\n";

$x = 0 ;
$groupe_precedent = "vide" ;
if ( $nb_formations  != 0 ) {
	debut_tableau() ;
}
while ( $enr = mysqli_fetch_assoc($res) )
{
	if ( $enr["groupe"] != $groupe_precedent ) {
		echo "<tr>\n" ;
		echo "<th class='invisible'></th>\n" ;
		echo "<th colspan='4' style='background: #333; color: #fff; font-size: larger; text-align: right;'>".$enr["groupe"]."</th>\n" ;
		//echo "<th class='invisible'></th>\n" ;
		echo "</tr>\n" ;
	}
	$groupe_precedent = $enr["groupe"] ;

	$id_atelier = $enr["id_atelier"] ;
	$intitule = $enr["intitule"] ;
	//$niveau = $enr["niveau"] ;
	$institution = $enr["institution"] ;
	$sigle = $enr["sigle"] ;
	$id = $enr["id"] ;
	$universite = $enr["universite"] ;
	$lien = "?id_atelier=$id_atelier" ;
	$class = $x % 2 ? "pair" : "impair" ;


	$req3 = "SELECT * FROM selecteurs, atxsel WHERE codesel=id_sel AND id_atelier=$id_atelier" ;
	$res3 = mysqli_query($cnx, $req3) ;
	$selectionneur = "" ;
	//$compteurSel = 0 ;
	while ($enr3 = mysqli_fetch_assoc($res3) ) {
		$selectionneur .= "<div><a href='mailto:"
			. $enr3["email"]
			. "'>" 
			. $enr3["prenomsel"]
			. " <span class='majuscules'>"
			. $enr3["nomsel"]
			. "</span>"
			. "</a></</div>" ;
	}

	echo "<tr class='$class' id='formation".$id_atelier."'>\n";
	if ( $enr["N"] == 0 ) {
	    echo "\t<td><a href='suppression_formation.php".$lien."'>Supprimer</a></td>\n" ;
	}
	else {
		echo "\t<td class='invisible'></td>\n" ;
	}



	echo "\t<td class='l' style='line-height: 1em;'>\n" ;
	$req2  = "SELECT * FROM session WHERE ref_atelier='".$id_atelier."'" ;
	/*
	if ( $_SESSION["filtres"]["formations"]["annee"] != "" ) {
		$requete2 .= " AND annee" . $_SESSION["filtres"]["formations"]["annee"] ;
	}
	*/
	$req2 .= " ORDER BY date_deb" ;
	$res2 = mysqli_query($cnx, $req2) ;

	while ( $ligne = mysqli_fetch_assoc($res2) ) {
		echo "<div>" ;
		echo "<code class='help'>" ;
		echo "<span class='".dateOuiNon($ligne["inscriptions_deb"], $ligne["inscriptions_fin"]) ."' " ;
		echo "title='Pré-inscriptions'>P</span>" ;
		echo "<span class='".dateOuiNon($ligne["imputations_deb"], $ligne["imputations_fin"]) ."' " ;
		echo "title='Imputations'>I</span>" ;
		echo "</code> " ;
		echo $ligne["annee"] . " - " ;
		echo str_replace(" ", "&nbsp;", $ligne["intit_ses"]) ;
		echo "</div>\n" ;
	}
	echo "</td>\n" ;
	echo "\t<td>$selectionneur</td>\n" ;
	echo "\t<td title='$id'>$institution" ;
	if ( $sigle != "" ) {
		echo " ($sigle)" ;
	}
	echo "</td>\n" ;
    echo "\t<td style='white-space:nowrap;'><div>$universite<br />" ;
	echo "<strong><a href='formation.php".$lien."&action=modification'>$intitule</a></strong></div>" ;
	//echo "$niveau &nbsp; " ;

	echo "<span class='s'>" ;
	if ( $enr["ref_discipline"] != "" ) {
		//echo " - &nbsp; " ;
		echo $enr["nom_discipline"] ;
	}

	if ( ($enr["responsable"] != "") OR ($enr["email_resp"] != "") ) {
		echo "&nbsp; - &nbsp; " ;
	}
	echo $enr["responsable"] ;
	if ( $enr["email_resp"] != "" ) {
		echo " (<a href='mailto:".$enr["email_resp"]."'>@</a>)" ;
	}
	echo "</span>" ;

	if ( $enr["commentaire"] != "" ) {
		echo "<div class='s'>".nl2br($enr["commentaire"])."</div>" ;
	}
	echo "</td>\n" ;
//	echo "\t<td><strong><a href='formation.php".$lien."&action=modification'>Modifier</a></strong></td>\n" ;
//	echo "\t<td><a href='formation.php".$lien."'>Afficher</a></td>\n" ;

	echo "</tr>\n" ;
	$x++;
}
if ( $nb_formations  != 0 ) {
	echo $fin_tableau ;
}

echo "<div style='height: 700px;'>&nbsp;</div>" ;

deconnecter($cnx) ;
echo $end ;
?>
