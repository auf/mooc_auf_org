<?php
include_once("inc_session.php") ;
if ( intval($_SESSION["id"]) > 3 ) {
    header("Location: /logout.php") ;
	exit ;
}
if ( !isset($_GET["id_imputation"]) AND !isset($_GET["id_dossier"]) ) {
	header("Location: /imputations/statistiques.php") ;
	exit ;
}

require_once("inc_mysqli.php") ;
$cnx = connecter() ;
require_once("inc_imputations.php") ;
require_once("inc_devises.php") ;
require_once("inc_identite.php") ;
require_once("inc_dossier.php") ;

if ( isset($_GET["id_dossier"]) ) {
	$T = imputation_dossier($cnx, $_GET["id_dossier"]) ;
}
else if ( isset($_GET["id_imputation"]) ) {
	$T = imputation_imputation($cnx, $_GET["id_imputation"]) ;
}
else {
	$T = array() ;
	$T["erreur"] = "<p class='erreur'>Erreur theoriquement impossible (pas d'id dans l'URL).</p>" ;
}

$identite = identite($T) ;
require_once("inc_html.php") ;
$titre = "Imputation pour $identite" ;
$haut_page_1 = $dtd1
	. "<title>".strip_tags($titre)."</title>\n"
	. $dtd2 ;
$haut_page_2 = "<div class='noprint'>"
	. $debut_chemin
	. "<a href='/bienvenue.php'>Accueil</a>"
	. " <span class='arr'>&rarr;</span> "
	. "<a href='/imputations/index.php'>Imputations</a>"
	. " <span class='arr'>&rarr;</span> "
	. $identite
	. $fin_chemin ;
//	. "</div>\n" ;

// Formulaire soumis
if ( isset($_POST["bouton"]) )
{
	// Ici, on ecrase les donnees de T par celles de _POST
	while ( list($key, $val) = each($_POST) )
	{
		// . comme separateur pour les float
		if ( $key == "montant" ) {
			$val = str_replace(",", ".", $val) ;
		}
		$T[$key] = trim($val) ;
	}

	$erreurs = verif_imputation($cnx, $T) ;

	$code = calcule_imputation($T) ;

	// INSERT (id_dossier) ou UPDATE (id_imputation)
	if ( $erreurs == "" )
	{
		//
		// INSERT
		//
		if ( isset($_GET["id_dossier"]) )
		{
			$req = "INSERT INTO imputations
				(ref_dossier,
				lieu_examen,
				lieu_paiement, montant, monnaie, imputation,
				commentaire,
				date_imput, date_maj_imput) VALUES(
				".$T["id_dossier"].",
				'".mysqli_real_escape_string($cnx, $T["lieu_examen"])."',
				'".mysqli_real_escape_string($cnx, $T["lieu_paiement"])."',
				".$T["montant"].",
				'".$T["monnaie"]."',
				'$code',
				'".mysqli_real_escape_string($cnx, trim($T["commentaire"]))."',
				CURDATE(),
				CURDATE()
				)" ;
			//echo "<p>$req</p>" ;
			mysqli_query($cnx, $req) ;
			$id_imputation = mysqli_insert_id($cnx) ;
			deconnecter($cnx) ;
			header("Location: /imputations/attestation.php?id=$id_imputation") ;
		}
		//
		// UPDATE
		//
		else if ( isset($_GET["id_imputation"]) )
		{
			$req = "UPDATE imputations SET
				lieu_examen='".mysqli_real_escape_string($cnx, $T["lieu_examen"])."',
				lieu_paiement='".mysqli_real_escape_string($cnx, $T["lieu_paiement"])."',
				montant=".$T["montant"].",
				monnaie='".$T["monnaie"]."',
				imputation='$code',
				date_maj_imput=CURDATE(),
				commentaire='".mysqli_real_escape_string($cnx, trim($T["commentaire"]))."'
				WHERE id_imputation=".$T["id_imputation"] ;
			//echo "<p>$req</p>" ;
			mysqli_query($cnx, $req) ;
	
			deconnecter($cnx) ;
			header("Location: /imputations/attestation.php?id=".$T["id_imputation"]) ;
		}
		// Erreur theoriquement impossible
		else {
			echo "<p class='erreur'>Erreur theoriquement impossible " ;
			echo "(pas d'id dans l'URL apres soumission du formulaire).</p>" ;
		}
	}
	else
	{
		echo $haut_page_1 ;
		include("inc_menu.php") ;
		echo $haut_page_2 ;
		if ( isset($T["erreur"]) AND ($T["erreur"] != "") ) {
			echo $T["erreur"] ;
		}
		else {
			formulaire_imputation($cnx, $T, TRUE) ;
			echo "<br />" ;
			affiche_dossier($cnx, $T) ;
		}
	}
}
// Arrivee dans la page ou le formulaire
else
{
	echo $haut_page_1 ;
	include("inc_menu.php") ;
	echo $haut_page_2 ;
	if ( isset($T["erreur"]) AND ($T["erreur"] != "") ) {
		echo $T["erreur"] ;
	}
	else {
		formulaire_imputation($cnx, $T) ;
		echo "<br />" ;
		affiche_dossier($cnx, $T) ;
	}
}

//diagnostic() ;
deconnecter($cnx) ;
echo $end ;
?>
