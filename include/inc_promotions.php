<?php

function chaine_liste_promotions($name, $value, $req, $cnx)
{
	$select_annee  = "<select name='".$name."_annee' id='".$name."_annee'>\n" ;
	$select_annee .= "<option value='0'>Année</option>\n" ;

	$select_groupe  = "<select name='".$name."_groupe' id='".$name."_groupe'>\n";
	$select_groupe .= "<option class='sub_0' value='0'>Domaine</option>\n" ;

	$select_niveau  = "<select name='".$name."_niveau' id='".$name."_niveau'>\n";
	$select_niveau .= "<option class='sub_0' value='0'>Niveau</option>\n" ;

	$select_promo  = "<select name='$name' id='$name'>\n" ;
	$select_promo .= "<option class='sub_0' value='0'>Promotion</option>\n" ;

	$compteur_annee = 0 ;
	$compteur_groupe = 0 ;
	$compteur_niveau = 0 ;

	$annee_precedente = "" ;
	$groupe_precedent = "" ;
	$niveau_precedent = "" ;

	$annee_courante = "" ;
	$groupe_courant = "" ;
	$niveau_courant = "" ;
	$promo_courante = "" ;

	// Requete pour memoire :
	// SELECT id_session, annee, groupe, niveau, intitule, intit_ses
	// ...
	// ORDER BY annee DESC, groupe, niveau, intitule
	if ( $req == "" )
	{
		$req = "SELECT id_session, annee, groupe, niveau, intitule, intit_ses
    		FROM atelier, session
    		WHERE session.ref_atelier=atelier.id_atelier
    		ORDER BY annee DESC, groupe, niveau, intitule" ;
	}

	// Une premiere requete pour determiner les compteurs de la selection
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$changement_annee = FALSE ;
		if ( $enr["annee"] != $annee_precedente ) {
			$compteur_annee++ ;
			$annee_precedente = $enr["annee"] ;
			$changement_annee = TRUE ;
		}
		$changement_groupe = FALSE ;
		if	( 
				( $enr["groupe"] != $groupe_precedent )
				OR $changement_annee 
			)
		{
			$compteur_groupe++ ;
			$groupe_precedent = $enr["groupe"] ;
			$changement_groupe = TRUE ;
		}
		if	(
				( $enr["niveau"] != $niveau_precedent )
				OR $changement_groupe
				OR $changement_annee
			)
		{
			$compteur_niveau++ ;
			$niveau_precedent = $enr["niveau"] ;
		}
		if ( $value == $enr["id_session"] )
		{
			$annee_courante = $compteur_annee ;
			$groupe_courant = $compteur_groupe ;
			$niveau_courant = $compteur_niveau ;
			$promo_courante = $enr["id_session"] ;
		}
	}

	$compteur_annee = 0 ;
	$compteur_groupe = 0 ;
	$compteur_niveau = 0 ;

	$annee_precedente = "" ;
	$groupe_precedent = "" ;
	$niveau_precedent = "" ;

	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$changement_annee = FALSE ;
		if ( $enr["annee"] != $annee_precedente )
		{
			$compteur_annee++ ;
			$annee_precedente = $enr["annee"] ;
			$select_annee .= "<option value='".$compteur_annee."'" ;
			if ( $annee_courante == $compteur_annee ) {
				$select_annee .= " selected='selected'" ;
			}
			$select_annee .= ">".$enr["annee"]."</option>\n" ;
			$changement_annee = TRUE ;
		}
		$changement_groupe = FALSE ;
		if	(
				( $enr["groupe"] != $groupe_precedent )
				OR $changement_annee 
			)
		{
			$compteur_groupe++ ;
			$groupe_precedent = $enr["groupe"] ;
			$select_groupe .= "<option class='sub_".$compteur_annee."' value='".$compteur_groupe."'" ;
			if ( $groupe_courant == $compteur_groupe ) {
				$select_groupe .= " selected='selected'" ;
				$groupe_courant = $compteur_groupe ;
			}
			$select_groupe .= ">".$enr["groupe"]."</option>\n" ;
			$changement_groupe = TRUE ;
		}

		if	(
				($enr["niveau"] != $niveau_precedent) 
				OR $changement_groupe
				OR $changement_annee
			)
		{
			$compteur_niveau++ ;
			$niveau_precedent = $enr["niveau"] ;
			$select_niveau .= "<option class='sub_".$compteur_groupe."' value='".$compteur_niveau."'" ;
			if ( $niveau_courant == $compteur_niveau ) {
				$select_niveau .= " selected='selected'" ;
				$niveau_courant = $compteur_niveau ;
			}
			$select_niveau .= ">".$enr["niveau"]."</option>\n" ;
		}

		$select_promo .= "<option class='sub_".$compteur_niveau."' value='".$enr["id_session"]."'" ;
		if ( $value == $enr["id_session"] ) {
			$select_promo .= " selected='selected'" ;
			$promo_courante = $enr["id_session"] ;
		}
		$select_promo .= ">".$enr["intitule"]."</option>\n" ;
		
	}
	
	$select_annee .= "</select>\n" ;
	$select_groupe .= "</select>\n" ;
	$select_niveau .= "</select>\n" ;
	$select_promo .= "</select>\n" ;
	
	$form  = "" ;
	$form .= $select_annee ;
	$form .= $select_groupe ;
	$form .= $select_niveau ;
	$form .= "<br />" ;
	$form .= $select_promo ;
	
	$script = "
<script type='text/javascript'>
makeSublist('".$name."_niveau','".$name."', false,'$promo_courante');
makeSublist('".$name."_groupe','".$name."_niveau', false,'$niveau_courant');
makeSublist('".$name."_annee','".$name."_groupe', false,'$groupe_courant');
</script>
	" ;
/*
$(document).ready(function()
{
});
*/

	$resultat = array(
		"form" => $form,
		"script" => $script,
	) ;
	return $resultat;
}




// Liste unique des promotions
function liste_promotions($name, $value, $cnx, $empty=FALSE)
{
	if ( intval($_SESSION["id"]) < 4 ) {
		$requete = "SELECT id_session, annee, groupe, intitule, intit_ses
				FROM atelier, session
				WHERE session.ref_atelier=atelier.id_atelier " ;
	}
	else {
		$requete = "SELECT id_session, annee, groupe, intitule, intit_ses
			FROM atelier, session, atxsel
			WHERE session.ref_atelier=atelier.id_atelier
			AND atelier.id_atelier=atxsel.id_atelier
			AND atxsel.id_sel='".$_SESSION["id"]."' " ;
	}
	$requete .= "ORDER BY annee DESC, groupe, niveau, intitule" ;
	$resultat = mysqli_query($cnx, $requete) ;

	$liste  = "<select name='$name' id='$name'>\n" ;
	if ( $empty ) {
		$liste .= "<option value=''></option>\n" ;
	}

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
		$liste .= " - ".$ligne["intit_ses"]."" ;
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
	//$liste .= $requete ;

	return $liste ;

//	return $tableau ;
}

function idpromotion2nom($id, $cnx)
{
	$requete = "SELECT intitule, intit_ses
		FROM atelier, session
		WHERE session.ref_atelier=atelier.id_atelier
		AND session.id_session=$id" ;
	$resultat = mysqli_query($cnx, $requete) ;
	$ligne = mysqli_fetch_assoc($resultat) ;
	return $ligne ;
}
?>
