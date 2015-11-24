<?php
include("inc_session.php") ;
if ( intval($_SESSION["id"]) > 1 ) {
	header("Location: /bienvenue.php") ;
	exit() ;
}

include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_html.php") ;
$titre = "Individus <span>(destinataires)</span>" ;
echo $dtd1 ;
echo "<title>".strip_tags($titre)."</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie_cnf/index.php'>Messagerie <span>(CNF)</span></a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;


$droits_edition = FALSE ;
if ( intval($_SESSION["id"]) == 0 ) {
	$droits_edition = TRUE ;
}


if ( $droits_edition ) {
?>
<p class='c'><strong><a href='individu.php'>Nouvel individu</a></strong></p>
<?php
}


echo "<form method='post' action='criteres.php'>\n" ;
echo "<table class='formulaire'>\n<tbody>\n" ;

echo "<tr>\n" ;
echo "<th rowspan='4'>Limiter à&nbsp;: </th>\n" ;
echo "<th>Région :</th>\n" ;
echo "<td>" ;
echo "<select name='individus_region'>\n" ;
echo "<option value=''></option>\n" ;
$req = "SELECT DISTINCT(ref_region.nom) AS nom_region, ref_region.id AS code_region
	FROM individus
	LEFT JOIN ref_pays ON individus.pays=ref_pays.code
	LEFT JOIN ref_region ON ref_region.id=ref_pays.region
	ORDER BY nom_region" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	echo "<option value=\"".$enr["code_region"]."\"" ;
	if ( isset($_SESSION["filtres"]["individus"]["region"]) AND ($_SESSION["filtres"]["individus"]["region"] == $enr["code_region"]) ) {
		echo " selected='selected'" ;
	}
	echo ">".$enr["nom_region"]."</option>" ;
}
echo "</select>" ;
echo "</td>\n" ;
echo "<td rowspan='3' class='s'>Un seul choix.<br />Priorité :<br />- CNF<br />- Pays<br />- Région</td>" ;
echo "</tr>\n" ;

require_once("inc_pays.php") ;
echo "<th>Pays :</th>\n" ;
echo "<td>" ;
$req = "SELECT DISTINCT(pays), ref_pays.code AS code, ref_pays.nom AS nom FROM individus
	LEFT JOIN ref_pays ON individus.pays=ref_pays.code ORDER BY nom" ;
echo selectPays($cnx, "individus_pays",
	( isset($_SESSION["filtres"]["individus"]["pays"]) ? $_SESSION["filtres"]["individus"]["pays"] : "" ),
	$req) ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<th>CNF :</th>\n" ;
echo "<td>" ;
echo "<select name='individus_cnf'>\n" ;
echo "<option value=''></option>\n" ;
$req = "SELECT DISTINCT(cnf) FROM individus ORDER BY cnf" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	echo "<option value=\"".$enr["cnf"]."\"" ;
	if ( isset($_SESSION["filtres"]["individus"]["cnf"]) AND ($_SESSION["filtres"]["individus"]["cnf"] == $enr["cnf"]) ) {
		echo " selected='selected'" ;
	}
	echo ">".$enr["cnf"]."</option>" ;
}
echo "</select>" ;
echo "</td>\n" ;
echo "</tr>\n" ;

require_once("inc_bool.php") ;
echo "<tr>\n" ;
echo "<th>Actif / inactif&nbsp;: </th>\n" ;
echo "<td colspan='2'>" ;
if ( isset($_SESSION["filtres"]["individus"]["actif"]) ) {
	liste_actif_inactif("individus_actif", $_SESSION["filtres"]["individus"]["actif"]) ;
}
else {
	liste_actif_inactif("individus_actif", "") ;
}
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Tri par&nbsp;: </th>\n" ;
echo "<td colspan='3'>" ;
echo "<select name='individus_tri'>\n" ;
$individus_tri = array(
	"nom" => "Nom",
	"prenom" => "Prénom",
	"pays" => "Pays (puis par CNF)",
	"cnf" => "CNF",
) ;
while ( list($key, $val) = each($individus_tri) ) {
	
	echo "<option value='".$key."'" ;
	if ( isset($_SESSION["filtres"]["individus"]["tri"]) AND ($_SESSION["filtres"]["individus"]["tri"] == $key) ) {
		echo " selected='selected'" ;
	}
	echo ">".$val."</option>" ;
}
echo "</select>" ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n<td colspan='4'>" . FILTRE_BOUTON_LIEN . "</td>\n</tr>\n" ;
echo "</tbody>\n</table>\n" ;
echo "</form>" ;

/*
	(SELECT COUNT(*) AS N FROM messages_individus WHERE ref_individu=id_individu) AS Nm
*/
$req = "SELECT individus.*,
	ref_pays.nom AS nom_pays, ref_pays.code AS code_pays,
	(SELECT COUNT(*) AS N FROM messages_individus WHERE ref_individu=id_individu) AS Nm,
	ref_region.id AS code_region
	FROM individus
	LEFT JOIN ref_pays ON individus.pays=ref_pays.code
	LEFT JOIN ref_region ON ref_pays.region=ref_region.id
	WHERE TRUE " ;
if ( isset($_SESSION["filtres"]["individus"]["cnf"]) AND ($_SESSION["filtres"]["individus"]["cnf"] != "") ) {
	$req .= " AND cnf='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["individus"]["cnf"])."' " ;
}
else if ( isset($_SESSION["filtres"]["individus"]["pays"]) AND ($_SESSION["filtres"]["individus"]["pays"] != "") ) {
	$req .= " AND pays='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["individus"]["pays"])."' " ;
}
else if ( isset($_SESSION["filtres"]["individus"]["region"]) AND ($_SESSION["filtres"]["individus"]["region"] != "") ) {
	$req .= " AND ref_region.id='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["individus"]["region"])."' " ;
}
if ( isset($_SESSION["filtres"]["individus"]["actif"]) AND ($_SESSION["filtres"]["individus"]["actif"] != "") ) {
	$req .= " AND individus.actif='".$_SESSION["filtres"]["individus"]["actif"]."' " ;
}

if ( isset($_SESSION["filtres"]["individus"]["tri"]) ) {
	if ($_SESSION["filtres"]["individus"]["tri"] == "pays" ) {
		$req .= " ORDER BY pays, cnf, nom" ;
	}
	else if ($_SESSION["filtres"]["individus"]["tri"] == "cnf" ) {
		$req .= " ORDER BY cnf, nom" ;
	} 
	else if ($_SESSION["filtres"]["individus"]["tri"] == "nom" ) {
		$req .= " ORDER BY nom, prenom" ;
	} 
	else if ($_SESSION["filtres"]["individus"]["tri"] == "prenom" ) {
		$req .= " ORDER BY prenom, nom" ;
	} 
	else {
		$req .= " ORDER BY nom, prenom" ;
	}
}
else {
	$req .= " ORDER BY nom, prenom" ;
}
/*
if ( in_array($_GET["tri"], $tri_individus)
{
	$req .= " ORDER BY " . $_GET["tri"] ;
}
*/
//echo $req ;
$res = mysqli_query($cnx, $req) ;
$N = mysqli_num_rows($res) ;
if ( $N == 0 )
{
	echo "<p class='c'>Aucun individu</p>" ;
}
else
{
	if ( $N > 1 ) { $s = "s" ; } else { $s = "" ; }
	echo "<p class='c'><strong>$N individu".$s."</strong></p>\n" ;
	echo "<table class='tableau'>\n" ;
	echo "<thead>\n" ;
	echo "<tr>\n" ;
	if ( $droits_edition ) {
		echo "<th class='invisible'></th>\n" ;
	}
	
	$thead_individus = array(
		"Pays",
		"CNF",
		"Civilité",
		"Prénom",
		"Nom",
		"Courriel",
		"Actif",
	) ;

	foreach ( $thead_individus as $thead)
	{
		echo "<th>" ;
		echo $thead ;
		echo "</th>\n" ;
	}
	echo "<th class='help' title=\"Nombre de messages dont l'individu a été destinataire\">M</th>" ;
	echo "</tr>\n" ;
	echo "</thead>\n" ;
	echo "<tbody>\n" ;
	while ( $row = mysqli_fetch_assoc($res) ) {
		echo "<tr id='i".$row["id_individu"]."'>\n" ;
		if ( $droits_edition ) {
			if	(
				($row["Nm"]==0)
				//AND ($row["actif"]==0)
				)
			{
				// FIXME
				//echo "<td class='s'>Supprimer</td>\n" ;
				echo "<td class='invisible'></td>\n" ;
			}
			else {
				echo "<td class='invisible'></td>\n" ;
			}
		}
		echo "<td>".$row["nom_pays"]."</td>\n" ;
		echo "<td>".$row["cnf"]."</td>\n" ;
		echo "<td>".$row["civilite"]."</td>\n" ;
		echo "<td>".$row["prenom"]."</td>\n" ;
		echo "<td>".$row["nom"]."</td>\n" ;
		echo "<td>".$row["courriel"]."</td>\n" ;
		echo "<td class='c actif" . $row["actif"] . "'>" ;
		if ( $row["actif"] == "1" ) {
			echo "actif" ;
		}
		else {
			echo "inactif" ;
		}
		echo "</td>\n" ;
		echo "<td class='r'>".$row["Nm"]."</td>\n" ;
		if ( $droits_edition ) {
			echo "<td><a href='individu.php?id=".$row["id_individu"]."'>Modifier</a></td>\n" ;
			echo "</tr>\n" ;
		}
	}
	echo "</tbody>\n" ;
	echo "</table>\n" ;
}

//diagnostic() ;

deconnecter($cnx) ;
echo $end ;
?>
