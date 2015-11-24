<?php
include("inc_session.php") ;

if ( isset($_GET["id_dossier"]) )
{
	$url  = "/imputations/imputation.php" ;
    $url .= "?id_dossier=" . $_GET["id_dossier"] ;
}
else {
	$url = "/imputations/index.php" ;
}
header("Location: $url") ;

/*
if ( isset($_GET["id_dossier"]) ) 
{
	include("inc_mysqli.php") ;
	$cnx = connecter() ;

	$req = "SELECT COUNT(id_imputation) AS N FROM imputations
		WHERE ref_dossier='".$_GET["id_dossier"]."'" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;
	if ( intval($enr["N"]) > 0 ) {
		echo "</p>Déjà imputé</p>" ;
		exit() ;
	}

	$req = "SELECT intitule, universite,
		session.*, dossier.*
		FROM dossier, session, atelier
		WHERE id_dossier=".$_GET["id_dossier"]."
		AND dossier.ref_session=session.id_session
		AND session.ref_atelier=atelier.id_atelier" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;

	// Numéro de dossier, et informations liées et non modifiables
	$imputation["ref_dossier"] = $_GET["id_dossier"] ;
	$imputation["universite"] = $enr["universite"] ;
	$imputation["intitule"] = $enr["intitule"] ;
	$imputation["intit_ses"] = $enr["intit_ses"] ;
	$imputation["code_imputation"] = $enr["imputation"] ;
	$imputation["tarif"] = $enr["tarif"] ;
	$imputation["tarif1"] = $enr["tarif1"] ;
	$imputation["tarif2"] = $enr["tarif2"] ;
	$imputation["tarif3"] = $enr["tarif3"] ;
	$imputation["etat"] = $enr["etat_dossier"] ;
	// Informations modifiables
	$imputation["genre"] = $enr["genre"] ;
	$imputation["nom"] = $enr["nom"] ;
	$imputation["prenom"] = $enr["prenom"] ;
//	$imputation["naissance"] = $enr["naissance"] ;
	$tab_naissance = explode("-", $enr["naissance"]) ;
	$imputation["annee_n"] = $tab_naissance[0] ;
	$imputation["mois_n"] = $tab_naissance[1] ;
	$imputation["jour_n"] = $tab_naissance[2] ;
	$imputation["pays_nationalite"] = $enr["pays_nationalite"] ;

	// C'est un ajout, pas une édition
	unset($imputation["id_imputation"]) ;

	deconnecter($cnx) ;
	header("Location: /imputations/imputation.php") ;
}
else {
	header("Location: /imputations/index.php") ;
}
*/
?>
