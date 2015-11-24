<?php
function liste_formations($cnx, $name, $value, $liste_promotions="")
{
	if ( $liste_promotions == "" ) {
		$requete = "SELECT id_atelier, groupe, intitule FROM atelier
			ORDER BY groupe, niveau, intitule" ;
	}
	else {
		$requete = "SELECT DISTINCT atelier.id_atelier, intitule
			FROM atelier, session
			WHERE atelier.id_atelier=session.ref_atelier
			AND session.id_session IN (".$_SESSION["liste_toutes_promotions"].")
			ORDER BY groupe, niveau, intitule" ;
	}

	$resultat = mysqli_query($cnx, $requete) ;

	$liste  = "<select name='$name' id='$name'>\n" ;
	$liste .= "<option value=''></option>\n" ;

	$groupe = 0 ;
	$groupe_precedent = "aucun" ;
	while ( $ligne = mysqli_fetch_assoc($resultat) ) {
		if ( ($liste_promotions == "") AND ($ligne["groupe"] != $groupe_precedent) ) {
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
	if ( $liste_promotions == "" ) {
		$liste .= "</optgroup>\n" ;
	}	

	$liste .= "</select>\n" ;

	echo $liste ;
}

function chaine_liste_formations($name, $value, $req, $cnx)
{
	$select_groupe  = "<select name='".$name."_groupe' id='".$name."_groupe'>\n";
	$select_groupe .= "<option class='sub_0' value='0'>Domaine</option>\n" ;

	$select_niveau  = "<select name='".$name."_niveau' id='".$name."_niveau'>\n";
	$select_niveau .= "<option class='sub_0' value='0'>Niveau</option>\n" ;

	$select_forma  = "<select name='$name' id='$name'>\n" ;
	$select_forma .= "<option class='sub_0' value='0'>Formation</option>\n" ;

	$compteur_annee = 0 ;
	$compteur_niveau = 0 ;
	$compteur_groupe = 0 ;

	$groupe_precedent = "" ;
	$niveau_precedent = "" ;

	$groupe_courant = "" ;
	$niveau_courant = "" ;
	$forma_courante = "" ;

	$promo_courante = 0 ;

	// Requete pour memoire :
	// SELECT id_session, annee, groupe, niveau, intitule, intit_ses
	// ...
	// ORDER BY annee DESC, groupe, niveau, intitule
	if ( $req == "" )
	{
		$req = "SELECT id_atelier, groupe, niveau, intitule
    		FROM atelier
    		ORDER BY groupe, niveau, intitule" ;
	}

	// Une premiere requete pour determiner les compteurs de la selection
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$changement_groupe = FALSE ;
		$changement_annee = FALSE ;
		if ( $enr["groupe"] != $groupe_precedent )
		{
			$compteur_groupe++ ;
			$groupe_precedent = $enr["groupe"] ;
			$changement_groupe = TRUE ;
		}
		if	(
				( $enr["niveau"] != $niveau_precedent )
				OR $changement_groupe
			)
		{
			$compteur_niveau++ ;
			$niveau_precedent = $enr["niveau"] ;
		}
		if ( $value == $enr["id_atelier"] )
		{
			$groupe_courant = $compteur_groupe ;
			$niveau_courant = $compteur_niveau ;
			$forma_courante = $enr["id_atelier"] ;
		}
	}

	$compteur_groupe = 0 ;
	$compteur_niveau = 0 ;

	$groupe_precedent = "" ;
	$niveau_precedent = "" ;

	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
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
				//OR $changement_annee
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

		$select_forma .= "<option class='sub_".$compteur_niveau."' value='".$enr["id_atelier"]."'" ;
		if ( $value == $enr["id_atelier"] ) {
			$select_forma .= " selected='selected'" ;
			$promo_courante = $enr["id_atelier"] ;
		}
		$select_forma .= ">".$enr["intitule"]."</option>\n" ;
		
	}
	
	$select_groupe .= "</select>\n" ;
	$select_niveau .= "</select>\n" ;
	$select_forma .= "</select>\n" ;
	
	$form = "" ;
	$form .= $select_groupe ;
	$form .= $select_niveau ;
	$form .= "<br />" ;
	$form .= $select_forma ;
	
	$script = "
<script type='text/javascript'>
makeSublist('".$name."_niveau','".$name."', false,'$promo_courante');
makeSublist('".$name."_groupe','".$name."_niveau', false,'$niveau_courant');
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

?>
