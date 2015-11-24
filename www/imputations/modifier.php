<?php
include("inc_session.php") ;

/*
require_once("inc_noms.php") ;

if ( isset($_GET["id_imputation"]) ) 
{
	include("inc_mysqli.php") ;
	$cnx = connecter() ;

	$req = "SELECT imputations.*,
		intitule, nb_annees,
		intit_ses, universite, annee, tarifa, tarifp, tarif2a, tarif2p,
		session.imputation AS code_imputation,
		session.imputation2 AS code_imputation2,
		civilite, nom, prenom, nom_jf, naissance, nationalite
		FROM imputations, dossier, candidat, session, atelier
		WHERE id_imputation=".$_GET["id_imputation"]."
		AND ref_dossier=id_dossier
		AND dossier.id_candidat=candidat.id_candidat
		AND dossier.id_session=session.id_session
		AND session.id_atelier=atelier.id_atelier" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;

	// Numéro de dossier, et informations liées et non modifiables
	$imputation["id_imputation"] = $enr["id_imputation"] ;
	$imputation["ref_dossier"] = $enr["ref_dossier"] ;
	$imputation["intitule"] = $enr["intitule"] ;
	$imputation["nb_annees"] = $enr["nb_annees"] ;
	$imputation["intit_ses"] = $enr["intit_ses"] ;
	$imputation["universite"] = $enr["universite"] ;
	$imputation["code_imputation"] = $enr["code_imputation"] ;
	$imputation["code_imputation2"] = $enr["code_imputation2"] ;
	$imputation["etat"] = $enr["etat"] ;
	$imputation["annee"] = $enr["annee"] ;
	$imputation["tarifp"] = $enr["tarifp"] ;
	$imputation["tarifa"] = $enr["tarifa"] ;
	$imputation["tarif2p"] = $enr["tarif2p"] ;
	$imputation["tarif2a"] = $enr["tarif2a"] ;

	// Informations modifiables
	$imputation["civilite"] = $enr["civilite"] ;
	$imputation["nom"] = $enr["nom"] ;
	$imputation["nom_jf"] = $enr["nom_jf"] ;
	$imputation["prenom"] = $enr["prenom"] ;
	$tab_naissance = explode("-", $enr["naissance"]) ;
	$imputation["annee_n"] = $tab_naissance[0] ;
	$imputation["mois_n"] = $tab_naissance[1] ;
	$imputation["jour_n"] = $tab_naissance[2] ;
	$imputation["nationalite"] = $enr["nationalite"] ;

	// Paiement
	$imputation["annee_absolue"] = $enr["annee_absolue"] ;
	$imputation["annee_relative"] = $enr["annee_relative"] ;
	$imputation["lieu"] = $enr["lieu"] ;
	$imputation["montant"] = floatval($enr["montant"]) ;
	$imputation["monnaie"] = $enr["monnaie"] ;
	$imputation["montant_frais"] = floatval($enr["montant_frais"]) ;
	$imputation["monnaie_frais"] = $enr["monnaie_frais"] ;
	$imputation["particulier"] = $enr["particulier"] ;
	$imputation["commentaire"] = $enr["commentaire"] ;
	// Imputation (pour modification par Service des bourses (SCP) seulement
	$imputation["imputation"] = $enr["imputation"] ;

	$_SESSION["imputation"] = $imputation ;

	deconnecter($cnx) ;
	header("Location: /imputations/imputation.php") ;
}
else {
	header("Location: /imputations/index.php") ;
}
*/
$url = "/imputations/imputation.php" ;
if ( isset($_GET["id_imputation"]) ) {
	$url .= "?id_imputation=" . $_GET["id_imputation"] ;
}
header("Location: $url") ;
?>
