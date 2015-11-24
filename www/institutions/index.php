<?php
include_once("inc_session.php") ;
require_once("inc_pays.php") ;
require_once("inc_date.php") ;
include_once("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_html.php") ;
$titre = "Institutions" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

$tabEtabQualite = array(
	"CIR" => "Centre ou institution de recherche",
	"ESR" => "Établissement d'enseignement supérieur et de recherche",
	"RES" => "Réseaux"
) ;
$tabEtabStatut = array(
	"A" => "Associé",
	"C" => "Candidat",
	"T" => "Titulaire"
) ;

function selectQualite($name, $value)
{
	global $tabEtabQualite ;
	reset($tabEtabQualite) ;
	$form = "" ;
	$form .= "<select name='$name'>\n" ;
	$form .= "<option value=''></option>\n" ;
	$form .= "<option value='-1'" ;
		if ( $value == "-1" ) {
			$form .= " selected='selected'" ;
		}
	$form .= ">Aucune</option>\n" ;
	while ( list($key, $val) = each($tabEtabQualite) ) {
		$form .= "<option value='$key'" ;
		if ( $key == $value ) {
			$form .= " selected='selected'" ;
		}
		$form .= ">$key : $val</option>\n" ;
	}
	$form .= "</select>\n" ;
	reset($tabEtabQualite) ;
	return $form ;
}

function selectStatut($name, $value)
{
	global $tabEtabStatut ;
	reset($tabEtabStatut) ;
	$form = "" ;
	$form .= "<select name='$name'>\n" ;
	$form .= "<option value=''></option>\n" ;
	$form .= "<option value='-1'" ;
		if ( $value == "-1" ) {
			$form .= " selected='selected'" ;
		}
	$form .= ">Aucun</option>\n" ;
	while ( list($key, $val) = each($tabEtabStatut) ) {
		$form .= "<option value='$key'" ;
		if ( $key == $value ) {
			$form .= " selected='selected'" ;
		}
		$form .= ">$key : $val</option>\n" ;
	}
	$form .= "</select>\n" ;
	reset($tabEtabStatut) ;
	return $form ;
}



if ( !isset($_SESSION["filtres"]["institutions"]["tri"]) ) {
	$_SESSION["filtres"]["institutions"]["tri"] = "nom_pays, ville, nom_etablissement" ;
}

echo "<form method='post' action='criteres.php' style='margin-left: 210px;'>\n" ;
echo "<table class='formulaire'>\n<tbody>\n" ;

echo "<tr>\n" ;
echo "<th rowspan='3'>Limiter à&nbsp;: </th>\n" ;
echo "<th>Pays&nbsp;:</th>\n" ;
echo "<td>" ;
echo selectPays($cnx, "institutions_pays",
	( isset($_SESSION["filtres"]["institutions"]["pays"]) ? $_SESSION["filtres"]["institutions"]["pays"] : "" ),
	"SELECT DISTINCT(ref_etablissement.pays), ref_pays.code AS code, ref_pays.nom AS nom
	FROM ref_etablissement LEFT JOIN ref_pays ON ref_etablissement.pays=ref_pays.code ORDER BY nom") ;
echo "</td>\n" ;
echo "<td rowspan='5' style='width: 210px; vertical-align: top; background: #fff; padding-left: 1em; font-size: 0.9em;'>
<p class=''>Cette page présente la référence des établissements de l'AUF (établissements \"actifs\" seulement).</p>
<p class=''>Elle permet notamment de <strong>rechercher</strong> une institution lorsque sa sélection
par (région, puis par) pays, puis par ville, puis par nom (ordre de tri par défaut) n'est pas adéquate ...</p>
</td>
" ;

echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Qualité&nbsp;:</th>\n" ;
echo "<td>" ;
echo selectQualite("institutions_qualite",
	( isset($_SESSION["filtres"]["institutions"]["qualite"]) ? $_SESSION["filtres"]["institutions"]["qualite"] : "" ) ) ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Statut&nbsp;:</th>\n" ;
echo "<td>" ;
echo selectStatut("institutions_statut",
	( isset($_SESSION["filtres"]["institutions"]["statut"]) ? $_SESSION["filtres"]["institutions"]["statut"] : "" ) ) ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Rechercher&nbsp;: </th>\n" ;
echo "<td colspan='2'>" ;
echo "<input type='text' name='institutions_recherche' value='" ;
echo ( isset($_SESSION["filtres"]["institutions"]["recherche"]) ? $_SESSION["filtres"]["institutions"]["recherche"] : "" ) ;
echo "' />" ;
echo " <span class='s fright'>Recherche d'un mot ou d'un groupe de mots dans&nbsp;:<br /> province, ville, nom, sigle ou URL.</span>" ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Tri par&nbsp;: </th>\n" ;
echo "<td colspan='2'>" ;
echo "<select name='institutions_tri'>\n" ;
$institutions_tri = array(
    "nom_pays, ville, nom_etablissement" => "Pays, Ville, Nom",
    "nom_pays, nom_etablissement" => "Pays, Nom",
    "nom_etablissement" => "Nom",
    "id_etablissement" => "Id",
    "date_modification DESC" => "Date de modification",
) ;
while ( list($key, $val) = each($institutions_tri) ) {

    echo "<option value='".$key."'" ;
    if ( isset($_SESSION["filtres"]["institutions"]["tri"]) AND ($_SESSION["filtres"]["institutions"]["tri"] == $key) ) {
        echo " selected='selected'" ;
    }
    echo ">".$val."</option>" ;
}
echo "</select>" ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n<td colspan='3'>" . FILTRE_BOUTON_LIEN . "</td>\n</tr>\n" ;
echo "</tbody>\n</table>\n" ;
echo "</form>" ;

/*
| id                   | int(11)          | NO   | PRI |
| nom                  | varchar(255)     | NO   |     |
| pays                 | varchar(2)       | NO   | MUL |
| region               | int(11)          | YES  | MUL |
| implantation         | int(11)          | YES  | MUL |
| membre               | tinyint(1)       | NO   |     |
| membre_adhesion_date | date             | YES  |     |
| qualite              | varchar(3)       | YES  |     |
| responsable_genre    | varchar(1)       | NO   |     |
| responsable_nom      | varchar(255)     | NO   |     |
| responsable_prenom   | varchar(255)     | NO   |     |
| adresse              | varchar(255)     | NO   |     |
| code_postal          | varchar(20)      | NO   |     |
| cedex                | varchar(20)      | NO   |     |
| ville                | varchar(255)     | NO   |     |
| province             | varchar(255)     | NO   |     |
| telephone            | varchar(255)     | NO   |     |
| fax                  | varchar(255)     | NO   |     |
| url                  | varchar(255)     | NO   |     |
| actif                | tinyint(1)       | NO   |     |
| statut               | varchar(1)       | YES  |     |
| date_modification    | date             | YES  |     |
| commentaire          | longtext         | NO   |     |
| code_implantation    | int(11)          | YES  |     |
| responsable_fonction | varchar(255)     | NO   |     |
| description          | longtext         | NO   |     |
| historique           | longtext         | NO   |     |
| sigle                | varchar(16)      | NO   |     |
| responsable_courriel | varchar(75)      | NO   |     |
| nombre_etudiants     | int(10) unsigned | YES  |     |
| nombre_chercheurs    | int(10) unsigned | YES  |     |
| nombre_enseignants   | int(10) unsigned | YES  |     |
| nombre_membres       | int(10) unsigned | YES  |     |

*/

$req = "SELECT ref_etablissement.id AS id_etablissement,
	ref_etablissement.nom AS nom_etablissement,
	sigle, url, province, ville, qualite, statut, membre, date_modification,
	ref_pays.nom AS nom_pays, ref_pays.code AS code_pays
	FROM ref_etablissement
	LEFT JOIN ref_pays ON ref_etablissement.pays=ref_pays.code
	WHERE TRUE
	AND ref_etablissement.actif=1 " ;
if ( isset($_SESSION["filtres"]["institutions"]["pays"]) AND ($_SESSION["filtres"]["institutions"]["pays"] != "" ) ) {
    $req .= " AND pays='".$_SESSION["filtres"]["institutions"]["pays"]."'" ;
}
if ( isset($_SESSION["filtres"]["institutions"]["qualite"]) AND ($_SESSION["filtres"]["institutions"]["qualite"] == "-1" ) ) {
    $req .= " AND qualite=''" ;
}
else if ( isset($_SESSION["filtres"]["institutions"]["qualite"]) AND ($_SESSION["filtres"]["institutions"]["qualite"] != "" ) ) {
    $req .= " AND qualite='".$_SESSION["filtres"]["institutions"]["qualite"]."'" ;
}
if ( isset($_SESSION["filtres"]["institutions"]["statut"]) AND ($_SESSION["filtres"]["institutions"]["statut"] == "-1" ) ) {
    $req .= " AND statut=''" ;
}
else if ( isset($_SESSION["filtres"]["institutions"]["statut"]) AND ($_SESSION["filtres"]["institutions"]["statut"] != "" ) ) {
    $req .= " AND statut='".$_SESSION["filtres"]["institutions"]["statut"]."'" ;
}
if ( isset($_SESSION["filtres"]["institutions"]["recherche"]) AND ($_SESSION["filtres"]["institutions"]["recherche"] != "" ) ) {
	$like = "LIKE '%".$_SESSION["filtres"]["institutions"]["recherche"]."%'" ;
    $req .= " AND ( ref_etablissement.nom $like OR sigle $like OR province $like OR ville $like OR url $like) " ;
}
if ( isset($_SESSION["filtres"]["institutions"]["tri"]) AND ($_SESSION["filtres"]["institutions"]["tri"] != "" ) ) {
    $req .= " ORDER BY " . $_SESSION["filtres"]["institutions"]["tri"] ;
}
//echo $req ;
$res = mysqli_query($cnx, $req) ;
$N = mysqli_num_rows($res) ;
if ( $N == 0 )
{
	echo "<p class='c'>Aucune institution</p>" ;
}
else
{
	if ( $N > 1 ) { $s = "s" ; } else { $s = "" ; }
	echo "<p class='c'><strong>$N institution".$s."</strong></p>\n" ;
	echo "<table class='tableau'>\n" ;
	echo "<thead>\n" ;
	echo "<tr>\n" ;
	echo "<th>Id</th>\n" ;
	echo "<th>Pays</th>\n" ;
	echo "<th>Province</th>\n" ;
	echo "<th>Ville</th>\n" ;
	echo "<th>Institution (Sigle)" ;
		echo "<div class='s'>URL</div>" ;
		echo "</th>\n" ;
	echo "<th>Qualité</th>\n" ;
	echo "<th title='Membre (0 = Non ; 1 = Oui)'>M</th>\n" ;
	echo "<th>Statut</th>\n" ;
	echo "<th title='date_modification'>Date</th>\n" ;
	echo "</tr>\n" ;
	echo "</thead>\n" ;
	echo "<tbody>\n" ;
	while ( $row = mysqli_fetch_assoc($res) ) {
		echo "<tr>\n" ;
		echo "<td class='r'>".$row["id_etablissement"]."</td>\n" ;
		echo "<td>".$row["nom_pays"]."</td>\n" ;
		echo "<td>".$row["province"]."</td>\n" ;
		echo "<td>".$row["ville"]."</td>\n" ;
		echo "<td>" ;
		echo "<strong>" . $row["nom_etablissement"] . "</strong>" ;
		if ( $row["sigle"] != "" ) {
			//echo "<span style='float: right; margin-left: 1em;'>(".$row["sigle"].")</span>" ;
			echo " &nbsp; (<strong>".$row["sigle"]."</strong>)" ;
		}
		echo "<div class='s'><a target='_blank' href='".$row["url"]."'>".$row["url"]."</a></div>" ;
		echo "</td>\n" ;
		echo "<td class='c'>".$row["qualite"]."</td>\n" ;
		echo "<td class='r'>".$row["membre"]."</td>\n" ;
		echo "<td class='c'>".$row["statut"]."</td>\n" ;
		echo "<td>".mysql2date($row["date_modification"])."</td>\n" ;
		//echo "<td>".$row["nom_etablissement"]."</td>\n" ;
		echo "</tr>\n" ;
	}
	echo "</tbody>\n" ;
	echo "</table>\n" ;
}

/*
echo "<p class='c s'>Chaque formation est censée être liée à une institution principale (dans la gestion des <a href='/formations/index.php'>formations</a>).
<br />L'institution principale n'est pas affichée dans les formulaires de candidature.
<br />Elle sert à regrouper les formations pour la messagerie relative aux examens.
<br />Elle sert aussi à identifier les institutions membres de l'AUF dans les exports.</p>" ;
*/

deconnecter($cnx) ;
echo $end ;
?>
