<?php

include("inc_session.php") ;
if ( intval($_SESSION["id"]) != 0 ) {
	header("Location: /bienvenue.php") ;
	exit() ;
}


require_once("inc_pays.php") ;
require_once("inc_cnf.php") ;
require_once("inc_form_select.php") ;
require_once("inc_bool.php") ;


function formulaireIndividu($tab, $confirm=0)
{
	$CIVILITE = array(
		"Madame",
		"Monsieur"
	) ;

	$form  = "" ;
	$form .= "\n<form method='post' action='individu.php'>\n" ;
	$form .= "<table class='formulaire'>\n" ;

	$form .= "<tr>\n<th>Pays&nbsp;:</th>\n<td>" ;
	$form .= listePays("pays", $tab["pays"]) ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>CNF&nbsp;:</th>\n<td>" ;
	$form .= listeCnf("cnf", $tab["cnf"], TRUE) ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>CivilitÃ©&nbsp;:</th>\n<td>" ;
	$form .= formSelect1($CIVILITE, "civilite", $tab["civilite"], TRUE) ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>PrÃ©nom&nbsp;:</th>\n<td>" ;
	$form .= '<input type="text" name="prenom" size="70" value="' ;
	$form .= $tab["prenom"] ;
	$form .= '" />' ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>Nom&nbsp;:</th>\n<td>" ;
	$form .= '<input type="text" name="nom" size="70" value="' ;
	$form .= $tab["nom"] ;
	$form .= '" />' ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>Courriel&nbsp;:</th>\n<td>" ;
	$form .= '<input type="text" name="courriel" size="80" value="' ;
	$form .= $tab["courriel"] ;
	$form .= '" />' ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>Actif&nbsp;:</th>\n<td>" ;
	$form .= radioActifInactif("actif", $tab["actif"]) ;
	if ( $confirm )
	{
		$form .= "<div class='erreur'>" ;
		$form .= "Rendre un individu inactif entraÃ®ne sa suppression des destinataires de tous les fils de messages." ;
		$form .= "<br />\n" ;
		$form .= "Cocher la case suivante pour confirmer ce choix :</div>\n" ;
		$form .= "<div><label for='confirm'><input type='checkbox' name='confirm' id='confirm' value='confirm' /> " ;
		$form .= "Supprimer des destinataires dans ".$confirm." fil" ;
		if ( $confirm > 1 ) { $form .= "s" ; } 
		$form .= " de messages</label></div>" ;
	}
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr><td colspan='2'>" ;
	$form .= "<input type='hidden' name='id_individu' value='".$tab["id_individu"]."' />\n" ;
	$form .= "<p class='c'><input class='b' name='submit' type='submit' value='Enregistrer' /></p>\n" ;

	$form .= "</table>\n" ;
	$form .= "</form>" ;

	return $form ;
}

function verifierIndividu($post)
{
	$erreurs = "" ;
	if ( trim($post["pays"]) == "" ) {
		$erreurs .= "<li>Le pays est obligatoire.</li>\n" ;
	}
	if ( trim($post["cnf"]) == "" ) {
		$erreurs .= "<li>Le CNF est obligatoire.</li>\n" ;
	}
	if ( trim($post["prenom"]) == "" ) {
		$erreurs .= "<li>Le prenom est obligatoire.</li>\n" ;
	}
	if ( trim($post["nom"]) == "" ) {
		$erreurs .= "<li>Le nom est obligatoire.</li>\n" ;
	}
	if ( trim($post["courriel"]) == "" ) {
		$erreurs .= "<li>Le courriel est obligatoire.</li>\n" ;
	}
	if (!filter_var($post["courriel"], FILTER_VALIDATE_EMAIL)) {
		$erreurs .= "<li>Courriel non valide.</li>\n" ;
	}

	if ( $erreurs != "" )
	{
		$erreurs = "<ul class='erreur c'>\n" . $erreurs . "</ul>\n" ;
	}
	return $erreurs ;
}




if ( isset($_GET["id"]) ) {
	$id_individu = $_GET["id"] ;
}
else if ( isset($_POST["id_individu"]) ) {
	$id_individu = $_POST["id_individu"] ;
}
else {
	$id_individu = 0 ;
}

include("inc_mysqli.php") ;
$cnx = connecter() ;

if ( $id_individu != 0 ) {
	$req = "SELECT * FROM individus
		WHERE id_individu='".mysqli_real_escape_string($cnx, $id_individu)."'" ;
	$res = mysqli_query($cnx, $req) ;
	if ( mysqli_num_rows($res) == 0 )
	{
		header("Location: /individus/index.php") ;
		deconnecter($cnx) ;
		exit() ;
	}
	else
	{
		$tabIndividu = mysqli_fetch_assoc($res) ;
	}
}
else {
	unset($tabIndividu) ;
}

if ( is_array($tabIndividu) ) {
	$titre = $tabIndividu["civilite"] . " " . $tabIndividu["prenom"] . " " . $tabIndividu["nom"] ;
}
else {
	$titre = "Nouvel individu" ;
}


require_once("inc_html.php") ;
$titreI = "Individus <span>(destinataires)" ;
$avantMenu = $dtd1 . "<title>".strip_tags($titreI)."</title>\n" . $dtd2 ;
$apresMenu = $debut_chemin . "<a href='/bienvenue.php'>Accueil</a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/messagerie_cnf/index.php'>Messagerie <span>(CNF)</span></a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/individus/index.php'>$titreI</a>"
	. " <span class='arr'>&rarr;</span> "
	. $titre
	. $fin_chemin ;

//require_once("inc_individus.php") ;
require_once("inc_guillemets.php") ;


// Formulaire soumis
if ( isset($_POST["submit"]) )
{
	$erreurs = verifierIndividu($_POST) ;
	if ( $erreurs )
	{
		echo $avantMenu ;
		include("inc_menu.php") ;
		echo $apresMenu ;
		echo $erreurs ;
		echo formulaireIndividu($_POST) ;
	}
	else
	{
		if ( $id_individu != 0 )
		{
			if ( ($tabIndividu["actif"] == "1") AND ($_POST["actif"] == "0") )
			{
				// ?ombre de fils dont l'individu est destinataire
				$req = "SELECT COUNT(*) AS N FROM fils_individus
					WHERE ref_individu='".mysqli_real_escape_string($cnx, $id_individu)."'" ;
				$res = mysqli_query($cnx, $req) ;
				$enr = mysqli_fetch_assoc($res) ;
				$N = $enr["N"] ;

				if ( ($N != "0") AND ($_POST["confirm"] != "confirm") )
				{
					echo $avantMenu ;
					include("inc_menu.php") ;
					echo $apresMenu ;
					echo formulaireIndividu($_POST, $N) ;
					exit() ;
					deconnecter($cnx) ;
					echo $end ;
				}
				else
				{
					$req = "UPDATE individus SET
						civilite='".mysqli_real_escape_string($cnx, $_POST['civilite'])."',
						prenom='".mysqli_real_escape_string($cnx, $_POST['prenom'])."',
						nom='".mysqli_real_escape_string($cnx, $_POST['nom'])."',
						courriel='".mysqli_real_escape_string($cnx, $_POST['courriel'])."',
						cnf='".mysqli_real_escape_string($cnx, $_POST['cnf'])."',
						pays='".mysqli_real_escape_string($cnx, $_POST['pays'])."',
						actif='".mysqli_real_escape_string($cnx, $_POST['actif'])."'
						WHERE id_individu=". mysqli_real_escape_string($cnx, $id_individu) ;
					$res = mysqli_query($cnx, $req) ;
					$id = $id_individu ;

					$req = "DELETE FROM fils_individus
						WHERE ref_individu='".mysqli_real_escape_string($cnx, $id_individu)."'" ;
					mysqli_query($cnx, $req) ;
				}
			}
			else
			{
				$req = "UPDATE individus SET
					civilite='".mysqli_real_escape_string($cnx, $_POST['civilite'])."',
					prenom='".mysqli_real_escape_string($cnx, $_POST['prenom'])."',
					nom='".mysqli_real_escape_string($cnx, $_POST['nom'])."',
					courriel='".mysqli_real_escape_string($cnx, $_POST['courriel'])."',
					cnf='".mysqli_real_escape_string($cnx, $_POST['cnf'])."',
					pays='".mysqli_real_escape_string($cnx, $_POST['pays'])."',
					actif='".mysqli_real_escape_string($cnx, $_POST['actif'])."'
					WHERE id_individu=". mysqli_real_escape_string($cnx, $id_individu) ;
				$res = mysqli_query($cnx, $req) ;
				$id = $id_individu ;
			}
		}
		else
		{
			$req = "INSERT INTO individus
				(civilite, prenom, nom, courriel, cnf, pays, actif)
				VALUES('".mysqli_real_escape_string($cnx, $_POST['civilite'])."'
				,'".mysqli_real_escape_string($cnx, $_POST['prenom'])."'
				,'".mysqli_real_escape_string($cnx, $_POST['nom'])."'
				,'".mysqli_real_escape_string($cnx, $_POST['courriel'])."'
				,'".mysqli_real_escape_string($cnx, $_POST['cnf'])."'
				,'".mysqli_real_escape_string($cnx, $_POST['pays'])."'
				,'".mysqli_real_escape_string($cnx, $_POST['actif'])."'
				)" ;
			//echo $req ;
			$res = mysqli_query($cnx, $req) ;
			$req = "SELECT LAST_INSERT_ID() AS N" ;
			$res = mysqli_query($cnx, $req) ;
			$enr = mysqli_fetch_assoc($res) ;
			$id = $enr["N"] ;
		}
		header("Location: /individus/index.php#i".$id) ;
	}	
}
// ArrivÃ©e dans le formulaire
else
{
	if ( $id_individu != 0 )
	{
			echo $avantMenu ;
			include("inc_menu.php") ;
			echo $apresMenu ;
			echo formulaireIndividu($tabIndividu) ;
	}
	else
	{
		echo $avantMenu ;
		include("inc_menu.php") ;
		echo $apresMenu ;
		echo formulaireIndividu(array()) ;
	}
}

//diagnostic() ;
deconnecter($cnx) ;
echo $end ;
?>
