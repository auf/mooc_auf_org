<?php
// Table dossier, longueurs connues
$EXPORTER = array(
	"id_mooc" => 1,

	"email" => "dossier",
	"genre" => "dossier",
	"nom" => "dossier",
	"prenom" => "dossier",
	"naissance" => "dossier",
	"pays_naissance" => "dossier",
	"pays_nationalite" => "dossier",
	"pays_residence" => "dossier",
	"situation_actu" => "dossier",
	"sit_autre" => "dossier",

	"ident_nature" => "dossier",
	"ident_autre" => "dossier",
	"ident_numero" => "dossier",
	"ident_date" => "dossier",
	"ident_lieu" => "dossier",

	"id_dossier" => "dossier",
	"date_inscrip" => "dossier",
	"date_maj" => "dossier",
	"etat_dossier" => "dossier",

	"lieu_examen" => "imputations",
	"lieu_paiement" => "imputations",
	"imputation" => "imputations",
	"date_imput" => "imputations",
	"date_maj_imput" => "imputations",
) ;

// Tableau des champs à exporter, par table
// (Issu du tabeau enregistré dans la session)
// Pour les exports par annee, il faudrait mutiplier les colonnes qui
// dependent de la promotion (question, selectionneurs) ; ce serait
// très couteux en memoire ; et vraissemblablement inutile
// Ces colonnes sont supprimees das index.php
function exporter2champs($exporter)
{
	global $EXPORTER ;

	$champs = array(
		"dossier" => array(),
		"liste_dossier" => ""
	) ;
	if ( count($exporter) == 0 ) {
		return $champs ;
	}
	$speciaux = array(
		"date_inscrip" => "dossier",
		"date_maj" => "dossier",
		"etat_dossier" => "dossier",
	) ;
	$nombre = array(
		"dossier" => 0,
		"imputations" => 0,
	) ;
	while ( list($key, $val) = each($exporter) )
	{
		// Champ rajoute : formation
		if ( $key == "intitule" ) {
				$champs["$key"] = $val ;
		}

		// Champ qui n'est pas dans la table candidat
		else if ( array_key_exists($key, $speciaux) ) {
			// Champs de la table dossier
			if ( $speciaux[$key] == "dossier" ) {
				$champs["dossier"][] = $val ;
				if ( $nombre["dossier"] > 0 ) {
					$champs["liste_dossier"] .= ", " ;
				}
				$champs["liste_dossier"] .= $val ;
				$nombre["dossier"]++ ;
			}
			// Autre
			else {
				$champs["$key"] = $val ;
			}
		}
		// Cahmps de la table candidat
		else {
			if ( $nombre["candidat"] > 0 ) {
				$champs["liste_candidat"] .= ", " ;
			}
			$champs["liste_candidat"] .= $val ;
			$champs["candidat"][] = $val ;
			$nombre["candidat"]++ ;
			if ( $EXPORTER[$key] == 1 ) {
				$champs["candidat1"][] = $val ;
			}
			else if ( $EXPORTER[$key] == 2 ) {
				$champs["candidat2"][] = $val ;
			}
			else {
				echo "<p class='erreur'>Erreur dans exporter2champs()</p>" ;
			}
		}
	}
	return $champs ;
}

$exp_dossier = array(
	"date_inscrip" => array(
		"Date de dépôt de la candidature",
		15
		),
	"date_maj" => array(
		"Date de mise à jour de la candidature",
		15
		),
	"etat_dossier" => array(
		"Etat du dossier",
		13
		),
) ;
function longueurd($champ)
{
	global $exp_dossier ;
	return $exp_dossier[$champ][1] ;
}

function libeld($champ)
{
	global $exp_dossier ;
	return $exp_dossier[$champ][0] ;
}

function requete_principale($champs, $post, $cnx)
{
	// virgule entre les champs de candidat et ceux de dossier
	$liste_champs = $champs["liste_candidat"] ;
	if ( ($champs["liste_candidat"]!="") AND ($champs["liste_dossier"]!="") ) {
		$liste_champs .= ", " ;
	}
	$liste_champs .= $champs["liste_dossier"] ;

	// Annee
	if ( ($post["promotion"] == "0") )
	{
		$req  = "SELECT id_dossier" ;
		$req .= ", intitule, intit_ses, groupe, universite, lieu, ref_ancien, " ;
		$req .= "((DATEDIFF(date_deb, naissance)) DIV 365.25) AS age" ;
		if ( $liste_champs != "" ) {
			$req .= ", " ;
		}
		$req .= $liste_champs ;
		$req .= " FROM atelier, session, dossier LEFT JOIN imputations " ;
		$req .= " ON dossier.id_dossier=imputations.ref_dossier " ;
		$req .= "WHERE session.ref_atelier=atelier.id_atelier " ;
		$req .= "AND session.id_session=dossier.ref_session " ;
		if ( isset($post["annee"]) AND ($post["annee"] != "") ) {
			$req .= " AND session.annee=".$post["annee"] ;
		}
		
		if ( isset($post["uniquement"]) ) {
			if ( $post["uniquement"] == "imputes" ) {
				$req .= " AND ref_dossier IS NOT NULL " ;
			}
		}
		if ( isset($post["etat"]) AND !empty($post["etat"]) ) {
			if ( $post["etat"] == "inscrit" ) {
				$req .= " AND etat_dossier IN ('Allocataire', 'Payant', 'Allocataire SCAC', 'Payant établissement') " ;
			}
			else {
				$req .= " AND etat_dossier='".$post["etat"]."'" ;
			}
		}
		if ( intval($_SESSION["id"]) > 3 ) {
			$req .= " AND dossier.ref_session IN ("
				.$_SESSION["liste_toutes_promotions"].") " ;
		}
	}
	// Promotion
	else
	{
		$req = "SELECT id_dossier" ;
		if ( $liste_champs != "" ) {
			$req .= ", " ;
		}
		$req .= $liste_champs ;

		$req .= " FROM dossier LEFT JOIN imputations " ;
		$req .= " ON dossier.id_dossier=imputations.ref_dossier " ;
		$req .= "WHERE dossier.ref_session=".$post["promotion"] ;
		$req .= " AND ref_dossier IS NOT NULL " ;
	}
	if ( !empty($post["etat"]) ) {
		if ( $post["etat"] == "inscrit" ) {
			$req .= " AND etat_dossier IN ('Allocataire', 'Payant', 'Allocataire SCAC', 'Payant établissement') " ;
		}
		else {
			$req .= " AND etat_dossier='".$post["etat"]."'" ;
		}
	}
	if ( !empty($post["pays"]) ) {
		$req .= " AND pays='".mysqli_real_escape_string($cnx, $post["pays"])."'" ;
	}

	// Annee
	if ( ($post["promotion"] == "0") )
	{
		$req .= " ORDER BY groupe, niveau, intitule ASC, " .$post["tri"] ;
	}
	// Promotion
	else {
		$req .= " ORDER BY ".$post["tri"] ;
	}
	if ( $post["tri"] == "date_maj" ) {
		$req .= " DESC " ;
	}

/*
	diagnostic() ;
*/

	$resultat = array() ;
	echo $req . "\n" ;
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		$resultat[] = $enr ;
	}
	return $resultat ;
}

function entete_sylk()
{
	// en-tête du fichier SYLK
	$flux  = "ID;PASTUCES-phpInfo.net\n" ; // ID;Pappli
	$flux .= "\n" ;
	// formats
	$flux .= "P;PGeneral\n" ;
	$flux .= "P;P#,##0.00\n" ; // P;Pformat_1 (reels)
	$flux .= "P;P#,##0\n" ;    // P;Pformat_2 (entiers)
	$flux .= "P;P@\n" ;        // P;Pformat_3 (textes)
	$flux .= "\n" ;
	// polices
	$flux .= "P;EArial;M200\n";
	$flux .= "P;EArial;M200\n";
	$flux .= "P;EArial;M200\n";
	$flux .= "P;FArial;M200;SB\n";
	$flux .= "\n";

	return $flux ;
}

function txtexp($str)
{
	$str = str_replace("&apos;", "'", $str) ;
	$str = str_replace("&quot;", "''", $str) ;
	$str = str_replace("\"", "''", $str) ;
	$str = str_replace(";", ";;", $str) ;
	$str = str_replace("\r\n", " ", $str) ;
	$str = str_replace("\n", " ", $str) ;
	return $str ;
}

?>
