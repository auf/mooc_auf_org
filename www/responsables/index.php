<?php
include("inc_session.php") ;
if ( $_SESSION["id"] != "00" ) {
	header("Location: /logout.php") ;
}
include("inc_html.php") ;
//$titre = "Responsables" ;
$titre = "Responsables" ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;
 
// echo "<p class='c'><strong><a href='stat_acces.php'>Journal d'accès des responsables</a></strong></p>" ;

echo "<p class='c'><strong>" ;
//echo "<a href='ajout.php'>Nouveau responsable</a></strong></p>\n" ;
echo "<a href='ajout.php'>Nouveau responsable</a></strong></p>\n" ;

include("inc_mysqli.php") ;
$cnx = connecter() ;


echo "<form method='post' action='criteres.php'>\n" ;
echo "<table class='formulaire'>\n<tbody>\n" ;
// On ne peut pas compter sur $_SESSION["derniere_annee"], car quand on ajoute
// une nouvelle promotion pour une année ultérieure, on ne peut pas y accéder
// sans se déconnecter et se reconnecter.

/*
echo "<tr>\n" ;
echo "<th rowspan='2'>Limiter à&nbsp;: </th>\n" ;
echo "<th>Nombre de formations (N)&nbsp;: </th>\n" ;
echo "<td><select name='responsable_nombre'>" ;
echo "<option value=''></option>\n" ;
echo "<option value='=0'" ;
if ( isset($_SESSION["filtres"]["responsables"]["nombre"]) AND ($_SESSION["filtres"]["responsables"]["nombre"] == "=0") ) {
	echo " selected='selected'" ;
}
echo ">0 &nbsp; (responsable inactif)</option>\n" ;
echo "<option value='!=0'" ;
if ( isset($_SESSION["filtres"]["responsables"]["nombre"]) AND ($_SESSION["filtres"]["responsables"]["nombre"] == "!=0") ) {
	echo " selected='selected'" ;
}
echo ">&ne;0 (responsable actif)</option>\n" ;
echo "</select></td>\n" ;
echo "</tr>\n" ;
*/
require_once("inc_institutions.php") ;
echo "<tr>\n" ;
echo "<th rowspan='4'>Limiter à&nbsp;: </th>\n" ;
echo "<th>Institution : </th>\n" ;
echo "<td>" ;
echo listeInstitutions($cnx, "responsable_ref_institution",
	( isset($_SESSION["filtres"]["responsables"]["ref_institution"]) ? $_SESSION["filtres"]["responsables"]["ref_institution"] : "" ),
	"responsables",
	TRUE) ;
echo "</td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Identifiant : </th>\n" ;
echo "<td><label><input size='20' name='responsable_login' " ;
if ( isset($_SESSION["filtres"]["responsables"]["login"]) AND ($_SESSION["filtres"]["responsables"]["login"] != "") ) {
	echo " value=\"" . $_SESSION["filtres"]["responsables"]["login"] . "\"" ;
}
echo " />" ;
echo "</label></td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Nom : </th>\n" ;
echo "<td><label><input size='30' name='responsable_nom' " ;
if ( isset($_SESSION["filtres"]["responsables"]["nom"]) AND ($_SESSION["filtres"]["responsables"]["nom"] != "") ) {
	echo " value=\"" . $_SESSION["filtres"]["responsables"]["nom"] . "\"" ;
}
echo " />" ;
echo "</label></td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Email : </th>\n" ;
echo "<td><label><input size='40' name='responsable_' " ;
if ( isset($_SESSION["filtres"]["responsables"]["email"]) AND ($_SESSION["filtres"]["responsables"]["email"] != "") ) {
	echo " value=\"" . $_SESSION["filtres"]["responsables"]["email"] . "\"" ;
}
echo " />" ;
echo "</label></td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n" ;
echo "<th>Trier par&nbsp;: </th>\n" ;
echo "<td colspan='2'><select name='responsable_tri'>" ;
$Tri = array(
	"usersel" => "Identifiant",
	"nomsel" => "Nom",
	"ref_institution" => "Institution",
	"N DESC, usersel ASC" => "N (nombre de formations) décroissant  (puis Identifiant)",
	"N, usersel" => "N (nombre de formations) croissant  (puis Identifiant)",
) ;
while ( list($key, $val) = each($Tri) ) {
	echo "<option value='$key'" ;
	if ( isset($_SESSION["filtres"]["responsables"]["tri"]) AND ($_SESSION["filtres"]["responsables"]["tri"] == "$key") ) {
		echo " selected='selected'" ;
	}
	echo ">$val</option>\n" ;
}
echo "</select></td>\n" ;
echo "</tr>\n" ;

echo "<tr>\n<td colspan='3'>" . FILTRE_BOUTON_LIEN . "</td>\n</tr>\n" ;
echo "</tbody>\n</table>\n" ;
echo "</form>" ;

$req = "SELECT selecteurs.*,
	(SELECT COUNT(id_atelier) FROM atxsel WHERE id_sel=codesel) AS N,
	(SELECT COUNT(id_comment_sel) FROM comment_sel WHERE ref_selecteur=codesel) AS C,
	ref_etablissement.nom AS institution
	FROM selecteurs LEFT JOIN ref_etablissement ON ref_etablissement.id=ref_institution
	WHERE TRUE " ;
if ( isset($_SESSION["filtres"]["responsables"]["login"]) AND ($_SESSION["filtres"]["responsables"]["login"] != "") ) {
	$req .= " AND usersel LIKE '%" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["responsables"]["login"]) . "%' " ;
}
if ( isset($_SESSION["filtres"]["responsables"]["nom"]) AND ($_SESSION["filtres"]["responsables"]["nom"] != "") ) {
	$req .= " AND nomsel LIKE '%" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["responsables"]["nom"]) . "%' " ;
}
if ( isset($_SESSION["filtres"]["responsables"]["email"]) AND ($_SESSION["filtres"]["responsables"]["email"] != "") ) {
	$req .= " AND emailsel LIKE '%" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["responsables"]["email"]) . "%' " ;
}
if ( isset($_SESSION["filtres"]["responsables"]["ref_institution"]) AND ($_SESSION["filtres"]["responsables"]["ref_institution"] == "-1") ) {
	$req .= " AND ref_institution='0' " ;
}
else if ( isset($_SESSION["filtres"]["responsables"]["ref_institution"]) AND ($_SESSION["filtres"]["responsables"]["ref_institution"] != "0") ) {
	$req .= " AND ref_institution = '" . mysqli_real_escape_string($cnx, $_SESSION["filtres"]["responsables"]["ref_institution"]) . "' " ;
}
if ( isset($_SESSION["filtres"]["responsables"]["nombre"]) AND ($_SESSION["filtres"]["responsables"]["nombre"] != "") ) {
	$req .= " HAVING N" . $_SESSION["filtres"]["responsables"]["nombre"] . " " ;
}
if ( isset($_SESSION["filtres"]["responsables"]["tri"]) AND ($_SESSION["filtres"]["responsables"]["tri"] != "") ) {
	$req .= " ORDER BY " . $_SESSION["filtres"]["responsables"]["tri"] . " " ;
}
else {
	$req .= " ORDER BY usersel" ;
}
//echo $req . "<br />" ;
$res = mysqli_query($cnx, $req) ;
$nbResponsables = mysqli_num_rows($res) ;

//echo "<p class='c'><strong>$nbResponsables responsable"
echo "<p class='c'><strong>$nbResponsables responsable"
	. ( ($nbResponsables > 1) ? "s" : "" )
	. "</strong>" ;
echo "</p>\n" ;

if ( $nbResponsables != 0 )
{
	echo "<table class='tableau'>\n";
	echo "<thead>\n" ;
	echo "<tr>\n" ;
	echo "<th class='s'>Commentaire</th>\n" ;
	echo "<th>Institution</th>\n" ;
	echo "<th>Courriel</th>\n" ;
	echo "<th>Prénom</th>\n" ;
	echo "<th><span class='majuscules'>Nom</span></th>\n" ;
	echo "<th>Identifiant</th>\n" ;
	echo "<th title='Mot de passe'>MdP</th>\n" ;
	echo "<th><span class='help' title='Nombre de formations'>N</span></th>\n" ;
	echo "<th><span class='help' title='Nombre de commentaires'>C</span></th>\n" ;
	echo "<th><span class='help' title='Transfert possible'>Transfert</span></th>\n" ;
	echo "<th colspan='2'>Action</th>\n" ;
	echo "<th class='invisible'></th>\n" ;
	echo "</tr>\n" ;
	echo "</thead>\n" ;
	echo "<tbody>\n" ;
}
	
$i = 0 ;
while ( $enr = mysqli_fetch_assoc($res) )
{
	$lien = "?select=".$enr["codesel"] ;
	$class = $i % 2 ? "pair" : "impair" ;

	echo "<tr class='$class' id='r".$enr["codesel"]."'>\n" ;

	echo "<td class='s'>" . nl2br(trim($enr["commentaire"])) . "</td>\n" ;

	echo "<td>".$enr["institution"]."</td>\n" ;

	echo "<td><a href='mailto:".$enr["email"]."'>".$enr["email"]."</a></td>\n" ;

	echo "<td>".$enr["prenomsel"]."</td>\n" ;

	echo "<td><strong class='majuscules'>".$enr["nomsel"]."</strong></td>\n" ;

	if ( $enr["pwdsel"] == $enr["usersel"] ) {
		echo "<td class='mono pwd0'><strong>" . $enr["usersel"] . "</strong></td>\n" ;
	}
	else if ( (substr_count($enr["pwdsel"], $enr["usersel"]) > 0) OR (substr_count($enr["usersel"], $enr["pwdsel"]) > 0) ) {
		echo "<td class='mono pwd1'><strong>" . $enr["usersel"] . "</strong></td>\n" ;
	}
	else {
		echo "<td class='mono'><strong>" . $enr["usersel"] . "</strong></td>\n" ;
	}

	if ( $enr["pwdsel"] == $enr["usersel"] ) {
		echo "<td class='mono pwd0'>" . $enr["pwdsel"] . "</td>\n" ;
	}
	else if ( (substr_count($enr["pwdsel"], $enr["usersel"]) > 0) OR (substr_count($enr["usersel"], $enr["pwdsel"]) > 0) ) {
		echo "<td class='mono pwd1'>" . $enr["pwdsel"] . "</td>\n" ;
	}
	else {
		echo "<td class='mono'>" . $enr["pwdsel"] . "</td>\n" ;
	}

	echo "<td class='r'><strong>".$enr["N"]."</strong></td>\n" ;
	echo "<td class='r'>".$enr["C"]."</td>\n" ;

	if ( $enr["transfert"] == "Oui" ) {
		echo "<td class='c'>possible</td>\n" ;
	}
	else {
		echo "<th></th>\n" ;
	}

	echo "<td><a href='modification.php".$lien."'>Modifier</a></td>\n" ;
	echo "<td><a href='affiche.php".$lien."'>Afficher</a></td>\n" ;
	if ( (intval($enr["N"]) == 0) AND (intval($enr["C"]) == 0) ) {
		echo "<td><a href='delete.php".$lien."'>Supprimer</a></td>\n" ;
	}
	else {
		echo "<td class='invisible'></td>\n" ;
	}

	echo "</tr>\n" ;
	$i++ ;
}
if ( $nbResponsables != 0 )
{
	echo "</tbody>\n" ;
	echo "</table>\n" ;
}

deconnecter($cnx) ;
echo $end ;
?>
