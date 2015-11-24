<?php
/**
@file inc_anciens.php
@addtogroup Groupe1
@addtogroup Groupe2
*/
require_once("inc_noms.php");
require_once("inc_identite.php") ;
require_once("inc_etat_dossier.php") ;
require_once("inc_dossier.php") ;
require_once("inc_date.php") ;

/**
 A partir d'un tableau representatif de la table candidat,
 retourner un tableau representatif de la table ancien.
 Traite nom, nom_jf, prenom, et situation_actu/emploi_actu
 */
function candidat2anciens($T)
{
	$ancien = array(
		"courriel" => $T["email1"],
		"civilite" => $T["civilite"],
		"nom" => "",
		"nom_jf" => "",
		"prenom" => "",
		"naissance" => $T["naissance"],
		"nationalite" => $T["nationalite"],
		"pays" => $T["pays"],
		"profession" => "",
		"employeur" => $T["employeur"],
		"date_maj" => $T["date_maj"],
	) ;
	$ancien["nom"] = nom($T["nom"]) ;
	if ( $T["civilite"] == 'Monsieur' ) {
		$ancien["nom_jf"] = "" ;
	}
	else {
		$ancien["nom_jf"] = nom($T["nom_jf"])  ;
	}
	$ancien["prenom"] = prenom($T["prenom"]) ;
	if	(
			( $T["situation_actu"] != "Etudiant(e)" )
			AND ( $T["situation_actu"] != "Sans emploi" )
		)
	{
		$ancien["profession"] = $T["emploi_actu"] ;
	}
	else {
		$ancien["profession"] = $T["situation_actu"] ;
	}
	return $ancien ;
}

/**
 Information publiques pour l'annuaire, provenant d la table ancien
 (ou de la table candidat après candidat2anciens())
 */
function ancienPublic($T)
{
	$str  = "" ;
	$str .= identite($T) ;
	return $str ;
}
function ancienPublicLien($T)
{
	$str  = "" ;
	$str .= "<a href='/anciens/ancien.php?id_ancien=".$T["id_ancien"]."'>" ;
	$str .= identite($T) ;
	$str .= "</a>" ;
	$str .= " - " . $T["courriel"] ;
	$str .= "<div style='float: right'>" ;
	$str .= $T["nom_pays"] ;
	$str .= " - " . mysql2date($T["naissance"]) ;
	$str .= "</div>" ;
	return $str ;
}

/**
 Informations privées pour l'annuaire, provenant de la table anciens
 ou de la table candidat après candidat2anciens()
 */
function ancienPrive($T)
{
	$str  = "" ;
	$str .= "<div class='date_maj'>" ;
	$str .= "Informations mises à jour le " ;
	$str .= mysql2datenum($T["date_maj"]) ;
	$str .= "</div>\n" ;
	$str .= "<table class='ancien'>\n" ;
	$str .= "<tr>\n" ;
	$str .= "<th><span class='champ'>Courriel :</span></th>\n" ;
	$str .= "<td><a href='".$T["courriel"]."'>".$T["courriel"]."</a></td>" ;
	$str .= "</tr><tr>\n" ;
	$str .= "<th><span class='champ'>Naissance :</span></th>\n" ;
	$str .= "<td>". mysql2datealpha($T["naissance"]) ."</td>" ;
	$str .= "</tr><tr>\n" ;
	$str .= "<th><span class='champ'>Nationalité&nbsp;:</span></th>\n" ;
	$str .= "<td>". $T["nationalite"] ."</td>" ;
	$str .= "</tr><tr>\n" ;
	$str .= "<th><span class='champ'>Pays :</span></th>\n" ;
	$str .= "<td>". $T["nom_pays"] ."</td>" ;
	$str .= "</tr><tr>\n" ;
	$str .= "<th><span class='champ'>Profession :</span></th>\n" ;
	$str .= "<td>". $T["profession"] ."</td>" ;
	$str .= "</tr><tr>\n" ;
	$str .= "<th><span class='champ'>Employeur :</span></th>\n" ;
	$str .= "<td>". $T["employeur"] ."</td>" ;
	$str .= "</tr>\n" ;
	$str .= "</table>\n" ;
	return $str ;
}

/**
 Informations d'administration provenant de la table anciens
 */
function ancienAdmin($T)
{
	$str  = "" ;
	$str .= "<table class='ancien'>\n" ;
	$str .= "<tr>\n" ;
	$str .= "<th><span class='champ'>Visibilité :</span></th>\n" ;
	$str .= "<td>". $T["visibilite"] ."</td>" ;
	$str .= "</tr><tr>\n" ;
	$str .= "<th><span class='champ'>Dernière connexion :</span></th>\n" ;
	$str .= "<td>". $T["date_con"] ."</td>" ;
	$str .= "</tr><tr>\n" ;
	$str .= "<th><span class='champ'>Nombre de connexions :</span></th>\n" ;
	$str .= "<td>". $T["nb_con"] ."</td>" ;
	$str .= "</tr><tr>\n" ;
	$str .= "<th><span class='champ'>Courriels envoyés :</span></th>\n" ;
	$str .= "<td>". $T["nb_envoi"] ."</td>" ;
	$str .= "</tr>\n" ;
	$str .= "</table>\n" ;
	return $str ;
}

/**
 $details = affichage des pictos
 Lien suppression pour administrateur, et pour page d'un ancien
 */
function ancienDiplome($T, $details=FALSE)
{
	global $etat_dossier_img_class;
	$str  = "" ;

	// Affichage du lien pour supprimer un diplome si
	// administrateur et URL = anciens/ancien.php
	if	(
			( intval($_SESSION["id"]) == 0 )
			AND
				( 
					( $_SERVER["SCRIPT_NAME"] == "/anciens/ancien.php" )
					OR 
						(
							( $_SERVER["SCRIPT_NAME"] == "/anciens/editer.php" )
							AND ( $_GET["id_dossier"] != $T["id_dossier"] )
						)
				)
		)
	{
		$str .= "<div class='undiplome'>" ;
		$str .= "<div class='fright'>" ;

		$str .= "<a href='/anciens/supprimer.php?" ;
		$str .= "id_ancien=".$T["id_ancien"] ;
		$str .= "&amp;" ;
		$str .= "id_dossier=".$T["id_dossier"]."'>" ;
		$str .= "Supprimer" ;
		$str .= "</a>" ;

		$str .= " - " ;

		$str .= "<a href='/anciens/editer.php?" ;
		$str .= "id_ancien=".$T["id_ancien"] ;
		$str .= "&amp;" ;
		$str .= "id_dossier=".$T["id_dossier"]."'>" ;
		$str .= "Éditer" ;
		$str .= "</a>" ;

		$str .= "</div>" ;
	}
	else if	(
			( intval($_SESSION["id"]) == 0 )
			AND ( $_SERVER["SCRIPT_NAME"] == "/anciens/editer.php" )
			AND ( $_GET["id_dossier"] == $T["id_dossier"] )
		)
	{
		$str .= "<div class='undiplome'>" ;
		$str .= "<div class='fright'>" ;

		$str .= "<form method='post' action='/anciens/anneed.php'>\n" ;
		$str .= "<input type='hidden' name='id_ancien' value='".$_GET["id_ancien"]."' />\n" ;
		$str .= "<input type='hidden' name='id_dossier' value='".$_GET["id_dossier"]."' />\n" ;
		$str .= "<table class='formulaire'><tr><td>\n" ;
		$str .= "<label style='display:block;' for='anneed'>Année d'obtention :</label>\n" ;
		$str .= "<select id='anneed' name='anneed'>\n" ;
		for ($i = $T["annee"]+$T["nb_annees"] ; $i <= date("Y", time()) ; $i++ ) {
			$str .= "<option value='$i'" ;
			if ( $i == $T["anneed"] ) {
				$str .= " selected='selected'" ;
			}
			$str .= ">$i</option>\n" ;
		}
			$str .= "\n" ;
		$str .= "</select>\n" ;
		$str .= "<input type='submit' value='OK' />\n" ;
		$str .= "</td></tr></table>\n" ;
		$str .= "</form>\n" ;

		$str .= "</div>" ;
	}
	else {
		$str .= "<div>" ;
	}

	$str .= "<strong>" ;
	$str .= $T["annee"] ;
	$str .= "</strong> - " ;

	$str .= "<span class='intitule'>" ;
	$str .= $T["intitule"] ;
	$str .= "</span>" ;

	$str .= " (" . $T["intit_ses"] . ") " ;
	/*
	$str .= " " ;
	$str .= $T["annee"] ;
	$str .= "<span class='universite'>" ;
	$str .= $T["universite"] ;
	$str .= "</span>" ;
	*/

	if ( $details ) {
		$str .= "<div class='petit'>" ;

		$str .= "<span class='".$etat_dossier_img_class[$T["etat_dossier"]]."'>" ;
		$str .= "<strong>".$T["etat_dossier"]."</strong>" ;
		$str .= "</span>" ;
		if ( $T["id_imputation1"] != "" ) {
			$str .= " <span class='paye'>".LABEL_INSCRIT."</span>" ;
		}
		if ( $T["id_imputation2"] != "" ) {
			$str .= " <span class='paye'>".LABEL_INSCRIT_2."</span>" ;
			if ( $T["etat2"] != $T["etat_dossier"] ) {
				$str .= " (<span class='small ".$etat_dossier_img_class[$T["etat2"]]."'>" ;
				$str .= $T["etat2"] ;
				$str .= "</span>)" ;
			}
		}
		if ( $T["diplome"] == "Oui" ) {
			$str .= " <span class='diplome'>".LABEL_DIPLOME." ".$T["anneed"]."</span>" ;
		}
		$str .= " - " ;
		$str .= "<a target='_blank' href='/candidatures/autre.php?id_dossier="
			. $T["id_dossier"] . "'>".LIEN_DOSSIER."</a>" ;
		if ( isset($T["id_imputation"]) AND ($T["id_imputation"] != "") ) {
			$str .= " - " ;
			$str .= "<a href='/imputations/attestation.php?id="
				. $T["id_imputation"] ."'>".LIEN_IMPUTATION."</a>" ;
		}
		$str .= "</div>" ;
	}

	$str .= "<div style='clear: both;'></div>" ;
	$str .= "</div>" ;
	return $str ;
}

/**
Affichage d'un ancien
@param int $id_ancien
@param $cnx
@param bool $public
 FALSE : ancienPublic, TRUE : ancienPublicLien 
@param bool $details
 TRUE : pictos, liens dossier, imputation
@param bool $prive
 TRUE : infos privees
@param bool $admin
 TRUE : infos d'admin
 */
function afficheAncien($id_ancien, $cnx, $public=FALSE, $details=FALSE, $prive=FALSE, $admin=FALSE)
{
	// requete incluant deux conditions inutiles (ref_dossier, ref_session)
	$req = "SELECT intitule, universite, intit_ses, session.annee,
		anciens.*,
		ref_pays.nom AS nom_pays,
		dossier_anciens.anneed,
		etat_dossier, diplome, dossier.id_dossier, nb_annees,
		(SELECT id_imputation FROM imputations WHERE ref_dossier=id_dossier AND annee_relative=1)
		AS id_imputation1,
		(SELECT id_imputation FROM imputations WHERE ref_dossier=id_dossier AND annee_relative=2)
		AS id_imputation2,
		(SELECT etat FROM imputations WHERE ref_dossier=id_dossier AND annee_relative=2)
		AS etat2
		FROM anciens
		LEFT JOIN ref_pays ON anciens.pays=ref_pays.code,
		dossier_anciens, session, atelier, dossier
		WHERE anciens.id_ancien=".$id_ancien."
		AND anciens.id_ancien=dossier_anciens.ref_ancien
		AND dossier_anciens.ref_dossier=dossier.id_dossier
		AND dossier.id_session=session.id_session
		AND dossier_anciens.ref_session=session.id_session
		AND session.id_atelier=atelier.id_atelier
		AND dossier_anciens.ref_atelier=atelier.id_atelier
		ORDER BY session.annee DESC" ;
//	echo $req ;
	$res = mysqli_query($cnx, $req) ;
	$N = mysqli_num_rows($res) ;
	if ( $N == 0 ) {
		echo "<p class='erreur'>Cet ancien n'existe pas.</p>" ;
		exit() ;
	}
	$diplomes  = "" ;
	$diplomes .= "<ul>\n" ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		$T = $enr ;
		$diplomes .= "<li>\n" ;
		$diplomes .= ancienDiplome($T, $details) ;
		$diplomes .= "</li>\n" ;
	}
	$diplomes .= "</ul>\n" ;
	
//	$str  = "<div class='ancien'>\n" ;
	$str = "" ;
	if ( $public ) {
		$str .= ancienPublicLien($T) ;
	}
	else {
		$str .= ancienPublic($T) ;
	}
	$str .= $diplomes ;
	if ( $prive ) {
		$str .= ancienPrive($T) ;
	}
	if ( $admin ) {
		$str .= ancienAdmin($T) ;
	}
//	$str .= "</div>\n" ;
	echo $str ;
}











// Recherche dans les dossiers appartenant à un ancien
// (table candidat, dossier.ref_ancien!=0)
function rechercheCandidat($T, $cnx)
{
	$noms = explode(" ", trim($T["nom"])) ;
	if ( ($T["civilite"] != 'Monsieur') AND (trim($T["nom_jf"])!="") ) {
		$noms_jf = explode(" ", trim($T["nom_jf"])) ;
	}
	else {
		$noms_jf = array() ;
	}

//		AND dossier.ref_ancien!=0
	$req = "SELECT dossier.ref_ancien
		FROM candidat, dossier
		WHERE ref_ancien!=0
		AND dossier.id_candidat=candidat.id_candidat
		AND dossier.id_dossier!=".$T["id_dossier"] ;
	if ( $T["email1"] != "" ) {
		$req .= " AND	(
				candidat.email1 LIKE '%".mysqli_real_escape_string($cnx, trim($T["email1"]))."%'
				OR (" ;
	}
	else {
		$req .= " AND ( " ;
	}
	$req .=		"
				candidat.naissance='".$T["naissance"]."' AND
					(FALSE " ;
	foreach($noms as $nom) {
		$req .= " OR candidat.nom LIKE '%".mysqli_real_escape_string($cnx, trim($nom))."%' " ;
	}
	reset($noms) ;
	if ( $T["civilite"] != 'Monsieur' ) {
		foreach($noms as $nom) {
			$req .= " OR candidat.nom_jf LIKE '%".mysqli_real_escape_string($cnx, trim($nom))."%' ";
		}
		reset($noms) ;
		foreach($noms_jf as $nom_jf) {
			$req .= " OR candidat.nom LIKE '%".mysqli_real_escape_string($cnx, trim($nom_jf))."%' ";
		}
		reset($noms_jf) ;
		foreach($noms_jf as $nom_jf) {
			$req .= " OR candidat.nom_jf LIKE '%".mysqli_real_escape_string($cnx, trim($nom_jf))."%'";
		}
		reset($noms_jf) ;
	}
	$req .=			")" ;
	if ( $T["email1"] != "" ) {
		$req .= 	")" ;
	}
	$req .= ") ORDER BY ref_ancien" ;
//	echo "<p>$req</p>" ;
	$res = mysqli_query($cnx, $req) ;

	$refs_ancien = array() ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		$refs_ancien[] = $enr["ref_ancien"] ;
	}
	return $refs_ancien ;
}
// Recherche dans les anciens, mais seulement sur l'adresse email
function rechercheAnciensEmail($T, $cnx)
{
	if ( trim($T["email1"]) == "" ) {
		return 0 ;
	}
	$req = "SELECT id_ancien FROM anciens WHERE courriel LIKE '%"
		. mysqli_real_escape_string($cnx, trim($T["email1"]))
		."%'" ;
	$res = mysqli_query($cnx, $req) ;

	$ids_ancien = array() ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		$ids_ancien[] = $enr["id_ancien"] ;
	}
	return $ids_ancien ;
}
// Recherche dans les anciens (table anciens)
function rechercheAnciens($T, $cnx)
{
	$noms = explode(" ", trim($T["nom"])) ;
	if ( ($T["civilite"] != 'Monsieur') AND (trim($T["nom_jf"])!="") ) {
		$noms_jf = explode(" ", trim($T["nom_jf"])) ;
	}
	else {
		$noms_jf = array() ;
	}

	$req = "SELECT id_ancien FROM anciens WHERE " ;
	if ( $T["email1"] != "" ) {
		$req .= " courriel LIKE '%".mysqli_real_escape_string($cnx, trim($T["email1"]))."%' OR ( " ;
	}
	$req .= " naissance='".$T["naissance"]."' AND
			(FALSE " ;
	foreach($noms as $nom) {
		$req .= " OR nom LIKE '%".mysqli_real_escape_string($cnx, trim($nom))."%' " ;
	}
	reset($noms) ;
	if ( $T["civilite"] != 'Monsieur' ) {
		foreach($noms as $nom) {
			$req .= " OR nom_jf LIKE '%".mysqli_real_escape_string($cnx, trim($nom))."%' ";
		}
		reset($noms) ;
		foreach($noms_jf as $nom_jf) {
			$req .= " OR nom LIKE '%".mysqli_real_escape_string($cnx, trim($nom_jf))."%' ";
		}
		reset($noms_jf) ;
		foreach($noms_jf as $nom_jf) {
			$req .= " OR nom_jf LIKE '%".mysqli_real_escape_string($cnx, trim($nom_jf))."%'";
		}
		reset($noms_jf) ;
	}
	if ( $T["email1"] != "" ) {
		$req .= " )" ;
	}
	$req .= ") ORDER BY id_ancien" ; 
//	echo "<p>$req</p>" ;
	$res = mysqli_query($cnx, $req) ;

	$ids_ancien = array() ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		$ids_ancien[] = $enr["id_ancien"] ;
	}
	return $ids_ancien ;
}

// Ajout d'un diplome et d'un ancien
function formulaireAjoutAncien($T)
{

	$form  = "<form method='post' action='ajoutAncien.php'>\n" ;
	$form .= "<table class='formulaire'>\n" ;
	$form .= "<tr><td>\n" ;
	$form .= "<input type='hidden' name='id_dossier' value='".$T["id_dossier"]."' />\n" ;
	$form .= "<input type='hidden' name='id_session' value='".$T["id_session"]."' />\n" ;
	$form .= "<input type='hidden' name='id_atelier' value='".$T["id_atelier"]."' />\n" ;
//	$form .= "<input type='hidden' name='' value='".$T[""]."' />\n" ;
	$form .= "<p class='c'><label for='anneed'>Année d'obtention :</label>\n" ;
	$form .= "<select id='anneed' name='anneed'>\n" ;
	for ($i = $T["annee"]+$T["nb_annees"] ; $i <= date("Y", time()) ; $i++ ) {
		$form .= "<option value='$i'>$i</option>\n" ;
	}
	$form .= "</select></p>\n" ;
	$form .= "<p class='c'>\n" ;
	$form .= "<input type='submit' value='Ajouter cet individu aux anciens pour un diplôme correspondant à cette candidature' />\n" ;
	$form .= "</p>\n" ;
	$form .= "</td></tr>\n" ;
	$form .= "</table>\n" ;
	$form .= "</form>\n" ;
	return $form ;
}
// Ajout d'un diplome à un ancien existant
function formulaireAjoutDiplome($T, $id_ancien)
{
	$form  = "<form method='post' action='ajoutDiplome.php'>\n" ;
	$form .= "<table class='formulaire'>\n" ;
	$form .= "<tr><td>\n" ;
	$form .= "<input type='hidden' name='id_dossier' value='".$T["id_dossier"]."' />\n" ;
	$form .= "<input type='hidden' name='id_session' value='".$T["id_session"]."' />\n" ;
	$form .= "<input type='hidden' name='id_atelier' value='".$T["id_atelier"]."' />\n" ;
	$form .= "<input type='hidden' name='id_ancien' value='".$id_ancien."' />\n" ;
	$form .= "<p class='c'><label for='anneed'>Année d'obtention :</label>\n" ;
	$form .= "<select id='anneed' name='anneed'>\n" ;
	for ($i = $T["annee"]+$T["nb_annees"] ; $i <= date("Y", time()) ; $i++ ) {
		$form .= "<option value='$i'>$i</option>\n" ;
	}
	$form .= "</select></p>\n" ;
	$form .= "<p class='c'>\n" ;
	$form .= "<input type='submit' value='Ajouter ce diplôme à cet ancien' />\n" ;
	$form .= "</p>\n" ;
	$form .= "</td></tr>\n" ;
	$form .= "</table>\n" ;
	$form .= "</form>\n" ;
	return $form ;
}
// Ajout d'un ancien pour lequel il n'existe pas de candidat
function formulaireAjoutCandidat()
{
	$form  = "<form method='post' action='ajoutDiplome.php'>\n" ;
	$form .= "<table class='formulaire'>\n" ;
	$form .= "<tr><td>\n" ;
	$form .= "</td></tr>\n" ;
	$form .= "</table>\n" ;
	$form .= "</form>\n" ;
	return $form ;
}
?>
