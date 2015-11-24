<?php
/*
 * 
 */

define("TITRE_ANCIENS", "Recherche") ;

define("LISTE_FORMATIONS_TEST", "(77)") ;
define("LISTE_PROMOTIONS_TEST", "(172)") ;

function liste_annees_anciens($cnx, $name, $value, $test=TRUE)
{
	$req = "SELECT DISTINCT(annee) FROM session
		WHERE id_session IN
		(SELECT DISTINCT ref_session FROM dossier_anciens)" ;
	if ( intval($_SESSION["id"]) > 3 ) {
		$req .= " AND id_session IN (".$_SESSION["liste_toutes_promotions"].")" ;
	}
	$req .= " ORDER BY annee DESC" ;
	$res = mysqli_query($cnx, $req) ;

	$liste  = "<select name='$name' id='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;

	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$liste .= "<option value='".$enr["annee"]."'" ;
		if ( $enr["annee"] == $value ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">".$enr["annee"]."</option>\n" ;
	}
	$liste .= "</select>\n" ;

	return $liste;
}

function liste_formations_anciens($cnx, $name, $value, $test=TRUE)
{
	if ( intval($_SESSION["id"]) < 4 ) {
		$requete = "SELECT id_atelier, groupe, intitule FROM atelier
			WHERE id_atelier IN
				(SELECT DISTINCT ref_atelier FROM dossier_anciens)" ;
		if ( !$test ) {
			$requete .= " AND id_atelier NOT IN ".LISTE_FORMATIONS_TEST ;
		}
	}
	else {
		$requete = "SELECT DISTINCT atelier.id_atelier, intitule
			FROM atelier, session
			WHERE atelier.id_atelier=session.id_atelier
			AND atelier.id_atelier IN
				(SELECT DISTINCT ref_atelier FROM dossier_anciens)
			AND session.id_session IN 
				(".$_SESSION["liste_toutes_promotions"].")" ;
	}
	$requete .= " ORDER BY groupe, niveau, intitule" ;

	$resultat = mysqli_query($cnx, $requete) ;

	$liste  = "<select name='$name' id='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;

	$groupe = 0 ;
	$groupe_precedent = "aucun" ;
	while ( $ligne = mysqli_fetch_assoc($resultat) ) {
		if	(
				( isset($liste_promotions) AND ($liste_promotions == "") )
				AND ( isset($ligne["groupe"]) AND ($ligne["groupe"] != $groupe_precedent) )
			)
		{
			if ( $groupe != 0 ) {
				$liste .= "</optgroup>\n" ;
			}
			$groupe++ ;
			$liste .= "<optgroup label=\"".$ligne["groupe"]."\">\n" ;
			$groupe_precedent = $ligne["groupe"] ;
		}
		$liste .= "<option value='".$ligne["id_atelier"]."'" ;
		if ( $ligne["id_atelier"] == $value ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">".$ligne["intitule"]."</option>\n" ;
	}
	if ( isset($liste_promotions) AND ($liste_promotions == "") ) {
		$liste .= "</optgroup>\n" ;
	}	

	$liste .= "</select>\n" ;

	return $liste ;
}


function liste_promotions_anciens($cnx, $name, $value, $test=TRUE)
{
	if ( intval($_SESSION["id"]) < 4 ) {
		$requete = "SELECT id_session, annee, groupe, intitule, intit_ses
			FROM atelier, session
			WHERE session.id_atelier=atelier.id_atelier 
			AND id_session IN
				(SELECT DISTINCT ref_session FROM dossier_anciens) " ;
		if ( !$test ) {
			$requete .= " AND id_session NOT IN ".LISTE_PROMOTIONS_TEST ;
		}
	}
	else {
		$requete = "SELECT id_session, annee, groupe, intitule, intit_ses
			FROM atelier, session, atxsel
			WHERE session.id_atelier=atelier.id_atelier
			AND id_session IN
				(SELECT DISTINCT ref_session FROM dossier_anciens)
			AND atelier.id_atelier=atxsel.id_atelier
			AND atxsel.id_sel='".$_SESSION["id"]."' " ;
	}
	$requete .= "ORDER BY annee DESC, groupe, niveau, intitule" ;
	$resultat = mysqli_query($cnx, $requete) ;

	$liste  = "<select name='$name' id='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;

	$annee = 0 ;
	$annee_precedente = "aucune" ;
	$groupe = 0 ;
	$groupe_precedent = "aucun" ;
	while ( $ligne = mysqli_fetch_assoc($resultat) ) 
	{
		if ( $ligne["annee"] != $annee_precedente )
		{
			$annee_precedente = $ligne["annee"] ;
			$groupe_precedent = "aucun" ;
			if ( $annee  != 0 ) {
					$liste .= "</optgroup>\n" ;
			}
			$annee++;
			$liste .= "<optgroup class='annee' label='".$ligne["annee"]."'>\n" ;
		}

		if ( intval($_SESSION["id"]) < 4 )
		{
			if ( $ligne["groupe"] != $groupe_precedent ) {
				if ( $groupe != 0 ) {
					$liste .= "</optgroup>\n" ;
				}
				$groupe++ ;
				$liste .= "<optgroup label=\"".$ligne["groupe"]." (".$ligne["annee"].")\">\n" ;
				$groupe_precedent = $ligne["groupe"] ;
		}
		}

		$liste .= "<option value='".$ligne["id_session"]."'" ;
		if ( $ligne["id_session"] == $value ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">".$ligne["intitule"] ;
		$liste .= " (".$ligne["intit_ses"].")" ;
		$liste .= "</option>\n" ;

//		$tableau[$ligne["id_atelier"]] = $ligne["intitule"] ;
	}
	if ( $groupe != 0 ) {
		$liste .= "</optgroup>\n" ;
	}
	if ( $annee  != 0 ) {
			$liste .= "</optgroup>\n" ;
	}

	$liste .= "</select>\n" ;

	return $liste ;
}

function liste_pays_anciens($cnx, $name, $selected, $test=TRUE)
{
	if ( $test ) {
		if ( intval($_SESSION["id"]) < 4 ) {
			$req = "SELECT DISTINCT anciens.pays, ref_pays.nom AS nom_pays
				FROM anciens
				LEFT JOIN ref_pays ON anciens.pays=ref_pays.code" ;
		}
		else {
			$req = "SELECT DISTINCT anciens.pays, ref_pays.nom AS nom_pays
				FROM anciens
					LEFT JOIN ref_pays ON anciens.pays=ref_pays.code,
					dossier_anciens
				WHERE anciens.id_ancien=dossier_anciens.ref_ancien
				AND ref_session IN (".$_SESSION["liste_toutes_promotions"].")" ;
		}
	}
	else {
		$req = "SELECT DISTINCT anciens.pays, ref_pays.nom AS nom_pays
			FROM anciens
				LEFT JOIN ref_pays ON anciens.pays=ref_pays.code,
				dossier_anciens
			WHERE anciens.id_ancien=dossier_anciens.ref_ancien
			AND ref_atelier NOT IN ".LISTE_FORMATIONS_TEST ;
	}
	$req .= " ORDER BY nom_pays " ;

	$res = mysqli_query($cnx, $req) ;

	$liste  = "<select name='$name' id='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;
	while ($enr = mysqli_fetch_assoc($res) ) {
		$pay = $enr["pays"] ;
		$liste .= "<option value=\"$pay\"" ;
		if ( $selected == $pay ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">".$enr["nom_pays"]."</option>\n" ;
	}
	$liste .= "</select>" ;

	return $liste ;
}

function afficheAncienDiplomesSpip($cnx, $id_ancien, $titre=FALSE)
{
	$dip  = "" ;

	$req = "SELECT intitule, universite, intit_ses, anneed
		FROM anciens, dossier_anciens, session, atelier
		WHERE anciens.id_ancien=".$id_ancien."
		AND anciens.id_ancien=dossier_anciens.ref_ancien
		AND dossier_anciens.ref_session=session.id_session
		AND dossier_anciens.ref_atelier=atelier.id_atelier
		AND session.id_atelier=atelier.id_atelier
		ORDER BY annee DESC" ;
	$res = mysqli_query($cnx, $req) ;

	if ( mysqli_num_rows($res) > 1 ) { $s = "s" ; } else { $s = "" ; }

	if ( $titre ) {
		$dip .= "<h3 class='spip'>Diplôme".$s."</h3>" ;
	}

	$dip .= "\n<ul class='diplomes'>\n" ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$dip .= "<li>" ;
		$dip .= "<span class='annee'>" ;
		$dip .= $enr["anneed"] ;
		$dip .= " : " ;
		$dip .= "</span>" ;

		$dip .= "<span class='intitule'>" ;
		$dip .= $enr["intitule"] ;
		$dip .= "</span> " ;

		$dip .= "<div class='universite'>" ;
		$dip .= "<span>" ;
		$dip .= $enr["universite"] ;
		$dip .= "</span>" ;
		$dip .= "</div>" ;

		//$dip .= " <span class=''>(" ;
		//$dip .= $enr["intit_ses"] ;
		//$dip .= ")</span>" ;
		$dip .= "</li>\n" ;
	}
	$dip .= "</ul>\n" ;
	return $dip ;
}

?>
