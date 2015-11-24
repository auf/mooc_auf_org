<?php
include("inc_session.php");

if ( isset($_GET["id_atelier"]) ) {
	$id_atelier = intval($_GET["id_atelier"]) ;
}
else if ( isset($_POST["id_atelier"]) ) {
	$id_atelier = intval($_POST["id_atelier"]) ;
}
else {
	unset($id_atelier) ;
}

if ( isset($_GET["action"]) ) {
	$action = $_GET["action"] ;
}
else if ( isset($_POST["action"]) ) {
	$action = $_POST["action"] ;
}
else {
	unset($action) ;
}

if ( !isset($id_atelier) AND !isset($action) ) {
	header("Location: index.php") ;
	exit() ;
}

include("inc_mysqli.php") ;
include("inc_html.php");
require_once("inc_groupe.php") ;

function tr_formulaire($libelle, $champ, $doc="")
{
	echo "<tr>\n" ;
		echo "\t<th>".$libelle."&nbsp;: " ;
			if ( $doc != "" ) {
				echo "<div class='s normal'>$doc</div>" ;
			}
			echo "</th>\n" ;
		echo "\t<td>".$champ."</td>\n" ;
	echo "</tr>\n" ;
}
function input($name, $value, $size, $maxlength)
{
	$champ  = "<input type='text' name='$name' " ;
	$champ .= "size='$size' maxlength='$maxlength' " ;
	$champ .= "value=\"".$value."\" />" ; 
	return $champ ;
}
function submit($value)
{
	$submit  = "<p class='c'>" ;
	$submit .= "<input type='submit' class='b' value='$value' />" ;
	$submit .= "</p>\n" ;
	echo $submit ;
}
function hidden($name, $value)
{
	$hidden = "<input type='hidden' name='$name' value='$value' />\n" ;
	echo $hidden ;
}
function erreur($texte) {
	echo "<p class='erreur'>" ;
	echo $texte ;
	echo "</p>\n" ;
}

function select_niveau($niveau)
{
	$NIVEAU = array(
		"",
		"3. DU",
		"3. L3",
		"4. M1",
		"5. DU",
		"5. M2",
		"8. D",
	) ;
	$select = "<select name=\"niveau\">\n" ;
	foreach($NIVEAU as $Niveau) {
		$select .= "<option value=\"$Niveau\"" ;
		if ( $Niveau == $niveau ) {
			$select .= " selected=\"selected\"" ;
		}
		$select .= ">$Niveau</option>\n" ;
	}
	$select .= "</select>" ;
	return $select ;
}

require_once("inc_institutions.php") ;
require_once("inc_disciplines.php") ;
function formulaire_formation($cnx, $valeurs)
{
	$intitule			= ( isset($valeurs["intitule"])			? $valeurs["intitule"]			: "" ) ;
	$ref_institution	= ( isset($valeurs["ref_institution"])	? $valeurs["ref_institution"]   : "" ) ;
	$ref_discipline		= ( isset($valeurs["ref_discipline"])	? $valeurs["ref_discipline"]	: "" ) ;
	$universite			= ( isset($valeurs["universite"])		? $valeurs["universite"]		: "" ) ;
	$groupe				= ( isset($valeurs["groupe"])			? $valeurs["groupe"]			: "" ) ;
//	$niveau				= ( isset($valeurs["niveau"])			? $valeurs["niveau"]			: "" ) ;
	$responsable		= ( isset($valeurs["responsable"])		? $valeurs["responsable"]		: "" ) ;
	$email_resp			= ( isset($valeurs["email_resp"])		? $valeurs["email_resp"]		: "" ) ;
	$commentaire		= ( isset($valeurs["commentaire"])		? $valeurs["commentaire"]		: "" ) ;

	echo "<table class='formulaire'>\n" ;
	echo "<tr>\n<th>" . "Intitulé de la formation" . "&nbsp;: </th>\n" ;
		echo "<td colspan='2'>". input("intitule", $intitule, 80, 255) ."</td>\n</tr>\n" ;

	echo "<tr><td style='padding: 1px; background: #777; height: 1px;' colspan='3'></td></tr>\n" ;

	echo "<tr>\n\t<th>Institution principale&nbsp;: " ;
		echo "<div class='s normal'>Invisible dans les formulaires d'inscription.<br />
		Utile pour les filtres, et dans les exports.</div>" ;
		echo "</th>\n<td colspan='2'>" ;
		//liste_institutions($cnx, "ref_institution", $ref_institution) ;
		$tabSelectInstitutions = selectChaineInstitutions($cnx, "ref_institution", $ref_institution) ;
		echo $tabSelectInstitutions["form"] ;
		echo $tabSelectInstitutions["script"] ;
		echo "</td></tr>\n" ;

	echo "<tr>\n<th>" . "Institution(s)" . "&nbsp;: " ;
		echo "<div class='s normal'>Affichée(s) dans les formulaires d'inscription</div>" ;
		echo "</th>\n" ;
		echo "<td colspan='2'>". input("universite",  $universite,  80, 255) ."</td>\n</tr>\n" ;

	echo "<tr>\n<th>". "Domaine" . "&nbsp;: </th>\n" ;
		echo "<td colspan='2'>". select_groupe($groupe) ."</td>\n</tr>\n" ;

	echo "<tr>\n<th>" . "Discipline principale" . "&nbsp;: " ;
		echo "<div class='s normal'>Discipline AUF, facultative</div>" ;
		echo "</th>\n" ;
		echo "<td colspan='2'>". selectDiscipline($cnx, "ref_discipline", $ref_discipline) ."</td>\n</tr>\n" ;
	/*
	echo "<tr>\n<th>" . "Niveau" . "&nbsp;: </th>\n" ;
		echo "<td colspan='2'>". select_niveau($niveau)."</td>\n</tr>\n" ;
	*/

	echo "<tr><td style='padding: 1px; background: #777; height: 1px;' colspan='3'></td></tr>\n" ;

	echo "<tr>\n<th>" . "Nom du responsable" . "&nbsp;: </th>\n" ;
		echo "<td>". input("responsable", $responsable, 70, 100) ."</td>\n" ;
		echo "<td rowspan='3' class='s'>Pour mémoire<br /> seulement.</td>\n</tr>\n" ;
	echo "<tr>\n<th>" . "Courriel du responsable" . "&nbsp;: </th>\n" ;
		echo "<td>". input("email_resp",  $email_resp,  70, 100) ."</td>\n</tr>\n" ;
	echo "<tr>\n<th>" . "Commentaire" . "&nbsp;: </th>\n" ;
		echo "<td><textarea name='commentaire' rows='3' cols='68'>". $commentaire ."</textarea></td>\n</tr>\n" ;
	/*
	echo "<tr>\n<th>" . "" . "&nbsp;: </th>\n" ;
		echo "<td colspan='2'>".."</td>\n</tr>\n" ;
	*/
	echo "</table>\n" ;
}

function erreurs_formation($tableau)
{
	if ( $tableau["intitule"] == "" ) {
		$erreurs[] = "Une formation doit avoir un <span class='chp'>intitulé</span>." ;
	}
	if ( ($tableau["universite"] == "") AND ($tableau["ref_institution"] == "0") ) {
		$erreurs[] = "Le champ <span class='chp'>Institutions(s)</span> est obligatoire
lorsque le champ <span class='chp'>Institution principale</span> n'est pas renseigné." ;
	}
	if ( $tableau["groupe"] == "" ) {
		$erreurs[] = "Une formation doit avoir un <span class='chp'>domaine</span>.<br />"
			. "Pour ajouter un domaine&nbsp;: "
			. "<a href='mailto:cedric.musso@labor-liber.org'>"
			. "cedric.musso@labor-liber.org</a>." ;
	}
	/*
	if ( $tableau["niveau"] == "" ) {
		$erreurs[] = "Une formation doit avoir un niveau.<br />"
			. "Pour ajouter un niveau&nbsp;: "
			. "<a href='mailto:cedric.musso@labor-liber.org'>"
			. "cedric.musso@labor-liber.org</a>." ;
	}
	*/
	return $erreurs ;
}

$cnx = connecter() ;

if ( isset($id_atelier) )
{
	$req = "SELECT * FROM atelier where id_atelier=$id_atelier" ;
	$res = mysqli_query($cnx, $req) ;
	$enregistrement = mysqli_fetch_assoc($res) ;

	if ( mysqli_num_rows($res) == 1 ) {
		$intitule = $enregistrement["intitule"] ;
		$responsable = $enregistrement["responsable"] ;
		$email_resp = $enregistrement["email_resp"] ;
	}
}

// Consultation
if ( isset($id_atelier) AND !isset($action) )
{
	echo $dtd1 ;
	echo "<title>".$enregistrement["intitule"]."</title>\n" ;
	echo $htmlJquery . $htmlMakeSublist . $dtd2 ;
	include("inc_menu.php");
	echo "<h1>".$enregistrement["intitule"]."</h1>\n" ;

	echo "<table class='data'>\n" ;
	tr_formulaire("Groupe", $enregistrement["groupe"]) ;
//	tr_formulaire("Niveau", $enregistrement["niveau"]) ;
	tr_formulaire("Intitulé de la formation", $enregistrement["intitule"]) ;
	tr_formulaire("Université", $enregistrement["universite"]) ;
	tr_formulaire("Nom du responsable", $enregistrement["responsable"]) ;
	tr_formulaire("Courriel du responsable", $enregistrement["email_resp"]) ;
	tr_formulaire("Commentaire", $enregistrement["commentaire"]) ;
	echo "</table>\n" ;

	$requete = "SELECT * from session
		WHERE id_atelier=$id_atelier ORDER BY date_deb DESC" ;
	$result = mysqli_query($cnx, $requete) ;
	$nbre = mysqli_num_rows($result) ;

	echo "</body>\n" ;
	echo "</html>" ;
}

if ( $_GET["action"] == "ajout" )
{
	$titre = "Nouvelle formation" ;
	echo $dtd1 ;
	echo "<title>$titre</title>\n" ;
	echo $htmlJquery . $htmlMakeSublist . $dtd2 ;
	include("inc_menu.php") ;
	echo $debut_chemin ;
	echo "<a href='/bienvenue.php'>Accueil</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "<a href='/formations/index.php'>Formations</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo $titre ;
	echo $fin_chemin ;

//	echo "<form action='".$_SERVER["SCRIPT_NAME"]."' method='post'>\n" ;
	echo "<form action='formation.php' method='post'>\n" ;
	formulaire_formation($cnx, "") ;
	hidden("action", "add") ;
	submit("Enregistrer") ;
	echo "</form>\n" ;

	echo $end ;
}

if ( isset($_POST["action"]) AND ($_POST["action"] == "add") )
{
	// Des erreurs ?
	unset($erreurs) ;
	$erreurs = erreurs_formation($_POST) ;

	if ( count($erreurs) > 0 )
	{
		$titre = "Nouvelle formation" ;
		echo $dtd1 ;
		echo "<title>$titre</title>\n" ;
		echo $htmlJquery . $htmlMakeSublist . $dtd2 ;
		include("inc_menu.php");
		echo $debut_chemin ;
		echo "<a href='/bienvenue.php'>Accueil</a>" ;
		echo " <span class='arr'>&rarr;</span> " ;
		echo "<a href='/formations/index.php'>Formations</a>" ;
		echo " <span class='arr'>&rarr;</span> " ;
		echo $titre ;
		echo $fin_chemin ;

		foreach($erreurs as $erreur) {
			erreur($erreur) ;
		}

//		echo "<form action='".$_SERVER["SCRIPT_NAME"]."' method='post'>\n" ;
		echo "<form action='formation.php' method='post'>\n" ;
		formulaire_formation($cnx, $_POST) ;
		hidden("action", "add") ;
		submit("Enregistrer") ;
		echo "</form>\n" ;

		echo $end ;
	}
	else {
		if ( trim($_POST["universite"]) == "" ) {
			$universite = nomInstitution($cnx, $_POST["ref_institution"]) ;	
		}
		else {
			$universite = trim($_POST["universite"]) ;
		}
		$req = "INSERT INTO atelier
			(groupe, intitule, ref_institution, ref_discipline, universite,
			responsable, email_resp, commentaire)
			VALUES(
			'".mysqli_real_escape_string($cnx, $_POST["groupe"])."',
			'".mysqli_real_escape_string($cnx, $_POST["intitule"])."',
			'".mysqli_real_escape_string($cnx, $_POST["ref_institution"])."',
			'".mysqli_real_escape_string($cnx, $_POST["ref_discipline"])."',
			'".mysqli_real_escape_string($cnx, $universite)."',
			'".mysqli_real_escape_string($cnx, $_POST["responsable"])."',
			'".mysqli_real_escape_string($cnx, $_POST["email_resp"])."',
			'".mysqli_real_escape_string($cnx, $_POST["commentaire"])."'
			)" ;
		mysqli_query($cnx, $req) ;
		header("Location: index.php") ;
	}
}

if ( 
	( $_GET["action"] == "modification"  OR $_POST["action"] == "modification" ) 
	AND  isset($_GET["id_atelier"]) 
)
{
	$titre = $enregistrement["intitule"] ;
	echo $dtd1 ;
	echo "<title>$titre</title>\n" ;
	echo $htmlJquery . $htmlMakeSublist . $dtd2 ;
	include("inc_menu.php");
	echo $debut_chemin ;
	echo "<a href='/bienvenue.php'>Accueil</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "<a href='/formations/index.php'>Formations</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo $titre ;
	echo $fin_chemin ;


	echo "<form action='formation.php' method='post'>\n" ;
	formulaire_formation($cnx, $enregistrement) ;
	hidden("action", "modification") ;
	hidden("id_atelier", $_GET["id_atelier"]) ;
	submit("Modifier") ;
	echo "</form>\n" ;

}
if ( isset ($_POST["action"]) AND ($_POST["action"] == "modification") AND  isset($_POST["id_atelier"]) )
{
	unset($erreurs) ;
	$erreurs = erreurs_formation($_POST) ;

	if ( count($erreurs) > 0 )
	{
		$titre = $enregistrement["intitule"] ;
		echo $dtd1 ;
		echo "<title>$titre</title>\n" ;
		echo $htmlJquery . $htmlMakeSublist . $dtd2 ;
		include("inc_menu.php");
		echo "<h1>$titre</h1>\n" ;

		foreach($erreurs as $erreur) {
			erreur($erreur) ;
		}

		echo "<form action='formation.php' method='post'>\n" ;
		formulaire_formation($cnx, $_POST) ;
		hidden("action", "modification") ;
		hidden("id_atelier", $_GET["id_atelier"]) ;
		submit("Modifier") ;
		echo "</form>\n" ;
	}
	else {
		if ( trim($_POST["universite"]) == "" ) {
			$universite = nomInstitution($cnx, $_POST["ref_institution"]) ;	
		}
		else {
			$universite = trim($_POST["universite"]) ;
		}
		$req = "UPDATE atelier SET
			groupe='".mysqli_real_escape_string($cnx, $_POST["groupe"])."',
			intitule='".mysqli_real_escape_string($cnx, $_POST["intitule"])."',
			ref_institution='".$_POST["ref_institution"]."',
			ref_discipline='".$_POST["ref_discipline"]."',
			universite='".mysqli_real_escape_string($cnx, $universite)."',
			responsable='".mysqli_real_escape_string($cnx, $_POST["responsable"])."',
			email_resp='".mysqli_real_escape_string($cnx, $_POST["email_resp"])."',
			commentaire='".mysqli_real_escape_string($cnx, $_POST["commentaire"])."'
			WHERE id_atelier=$id_atelier" ;
		mysqli_query($cnx, $req) ;
		header("Location: index.php#formation$id_atelier") ;
	}
}

deconnecter($cnx) ;
?>
