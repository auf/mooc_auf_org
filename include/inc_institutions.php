<?php
require_once("inc_pays.php") ;
/*
ref_etablissement.id     
ref_etablissement.nom    
ref_etablissement.pays   
ref_etablissement.region 
ref_etablissement.membre 
ref_etablissement.qualite
ref_etablissement.ville  
ref_etablissement.url    
ref_etablissement.actif  
ref_etablissement.statut 
ref_etablissement.sigle  

ref_pays.id           
ref_pays.code         
ref_pays.nom          
ref_pays.region       
ref_pays.actif        

ref_region.id                 
ref_region.code               
ref_region.nom                
ref_region.implantation_bureau
ref_region.actif              
*/

function nomInstitution($cnx, $id)
{
	$req = "SELECT nom FROM ref_etablissement WHERE id='$id'" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;
	return $enr["nom"] ;
}

function selectChaineInstitutions($cnx, $name, $value, $req="")
{
	$select_region  = "<select name='".$name."_region' id='".$name."_region'>\n" ;
	$select_region .= "<option value='0'>Région</option>\n" ;

	$select_pays  = "<select name='".$name."_pays' id='".$name."_pays'>\n";
	$select_pays .= "<option class='sub_0' value='0'>Pays</option>\n" ;

	$select_ville  = "<select name='".$name."_ville' id='".$name."_ville'>\n";
	$select_ville .= "<option class='sub_0' value='0'>Ville</option>\n" ;

	$select_institution  = "<select name='$name' id='$name'>\n" ;
	$select_institution .= "<option class='sub_0' value='0'>Institution</option>\n" ;

	$compteur_region = 0 ;
	$compteur_pays = 0 ;
	$compteur_ville = 0 ;

	$region_precedente = "" ;
	$pays_precedent = "" ;
	$ville_precedent = "" ;

	$region_courante = "" ;
	$pays_courant = "" ;
	$ville_courante = "" ;
	$institution_courante = "" ;

	if ( $req == "" )
	{
		$req = "SELECT
			ref_etablissement.id AS code_etablissement, ref_etablissement.nom AS nom_etablissement,
			ref_etablissement.ville AS ville, ref_etablissement.sigle AS sigle_etablissement,
			ref_pays.code AS code_pays, ref_pays.nom AS nom_pays,
			ref_region.id AS code_region, ref_region.nom AS nom_region
			FROM ref_etablissement
				LEFT JOIN ref_pays ON ref_etablissement.pays=ref_pays.code
				LEFT JOIN ref_region ON ref_pays.region=ref_region.id
			WHERE ref_etablissement.actif=1
			ORDER BY nom_region, nom_pays, ville, nom_etablissement" ;
		//		AND ref_etablissement.membre=1
	}

	// Une premiere requete pour determiner les compteurs de la selection
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$changement_region = FALSE ;
		if ( $enr["code_region"] != $region_precedente ) {
			$compteur_region++ ;
			$region_precedente = $enr["code_region"] ;
			$changement_region = TRUE ;
		}
		$changement_pays = FALSE ;
		if	( 
				( $enr["code_pays"] != $pays_precedent )
				OR $changement_region 
			)
		{
			$compteur_pays++ ;
			$pays_precedent = $enr["code_pays"] ;
			$changement_pays = TRUE ;
		}
		if	(
				( $enr["ville"] != $ville_precedent )
				OR $changement_pays
				OR $changement_region
			)
		{
			$compteur_ville++ ;
			$ville_precedent = $enr["ville"] ;
		}
		if ( $value == $enr["code_etablissement"] )
		{
			$region_courante = $compteur_region ;
			$pays_courant = $compteur_pays ;
			$ville_courante = $compteur_ville ;
			$institution_courante = $enr["code_etablissement"] ;
		}
	}

	$compteur_region = 0 ;
	$compteur_pays = 0 ;
	$compteur_ville = 0 ;

	$region_precedente = "" ;
	$pays_precedent = "" ;
	$ville_precedent = "" ;

	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$changement_region = FALSE ;
		if ( $enr["code_region"] != $region_precedente )
		{
			$compteur_region++ ;
			$region_precedente = $enr["code_region"] ;
			$select_region .= "<option value='".$compteur_region."'" ;
			if ( $region_courante == $compteur_region ) {
				$select_region .= " selected='selected'" ;
			}
			$select_region .= ">".$enr["nom_region"]."</option>\n" ;
			$changement_region = TRUE ;
		}
		$changement_pays = FALSE ;
		if	(
				( $enr["code_pays"] != $pays_precedent )
				OR $changement_region 
			)
		{
			$compteur_pays++ ;
			$pays_precedent = $enr["code_pays"] ;
			$select_pays .= "<option class='sub_".$compteur_region."' value='".$compteur_pays."'" ;
			if ( $pays_courant == $compteur_pays ) {
				$select_pays .= " selected='selected'" ;
				$pays_courant = $compteur_pays ;
			}
			$select_pays .= ">".$enr["nom_pays"]."</option>\n" ;
			$changement_pays = TRUE ;
		}

		if	(
				($enr["ville"] != $ville_precedent) 
				OR $changement_pays
				OR $changement_region
			)
		{
			$compteur_ville++ ;
			$ville_precedent = $enr["ville"] ;
			$select_ville .= "<option class='sub_".$compteur_pays."' value='".$compteur_ville."'" ;
			if ( $ville_courante == $compteur_ville ) {
				$select_ville .= " selected='selected'" ;
				$ville_courante = $compteur_ville ;
			}
			$select_ville .= ">".$enr["ville"]."</option>\n" ;
		}

		$select_institution .= "<option class='sub_".$compteur_ville."' value='".$enr["code_etablissement"]."'" ;
		if ( $value == $enr["code_etablissement"] ) {
			$select_institution .= " selected='selected'" ;
			$institution_courante = $enr["code_etablissement"] ;
		}
		$select_institution .= ">".$enr["nom_etablissement"] ;
		if ( $enr["sigle_etablissement"] != "" ) {
			$select_institution .= " (" . $enr["sigle_etablissement"] . ")" ;
		}
		$select_institution .= "</option>\n" ;
		
	}
	
	$select_region .= "</select>\n" ;
	$select_pays .= "</select>\n" ;
	$select_ville .= "</select>\n" ;
	$select_institution .= "</select>\n" ;
	
	$form  = "" ;
	$form .= $select_region ;
	$form .= $select_pays ;
	$form .= $select_ville ;
	$form .= "<br />" ;
	$form .= $select_institution ;
	
	$script = "
<script type='text/javascript'>
makeSublist('".$name."_ville','".$name."', false,'$institution_courante');
makeSublist('".$name."_pays','".$name."_ville', false,'$ville_courante');
makeSublist('".$name."_region','".$name."_pays', false,'$pays_courant');
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




function listeInstitutions($cnx, $name, $value, $requete="", $aucune=FALSE)
{
	// Par défaut : institutions des formations
	if ( $requete == "formations" ) {
		$req = "SELECT atelier.ref_institution, COUNT(ref_institution) AS N,
			ref_etablissement.id AS code_etablissement, ref_etablissement.nom AS nom_etablissement, ref_etablissement.sigle AS sigle,
			ref_pays.code AS code_pays, ref_pays.nom AS nom_pays
			FROM atelier
				LEFT JOIN ref_etablissement ON ref_institution=ref_etablissement.id
				LEFT JOIN ref_pays ON ref_etablissement.pays=ref_pays.code
			WHERE atelier.ref_institution!='0' AND atelier.ref_institution!=''
			GROUP BY ref_institution
			ORDER BY nom_pays, nom_etablissement" ;
	}
	// Institutions des responsables
	else if ( $requete == "responsables" ) {
		$req = "SELECT selecteurs.ref_institution, COUNT(ref_institution) AS N,
				ref_etablissement.id AS code_etablissement, ref_etablissement.nom AS nom_etablissement, ref_etablissement.sigle AS sigle,
				ref_pays.code AS code_pays, ref_pays.nom AS nom_pays
			FROM selecteurs
				LEFT JOIN ref_etablissement ON ref_institution=ref_etablissement.id
				LEFT JOIN ref_pays ON ref_etablissement.pays=ref_pays.code
			WHERE selecteurs.ref_institution!='0' AND selecteurs.ref_institution!=''
			GROUP BY ref_institution
			ORDER BY nom_pays, nom_etablissement" ;
	}
	// Toutes (actif)
	else {
		$req = "SELECT 
				ref_etablissement.id AS code_etablissement, ref_etablissement.nom AS nom_etablissement, ref_etablissement.sigle AS sigle,
				ref_pays.code AS code_pays, ref_pays.nom AS nom_pays
			FROM ref_etablissement
				LEFT JOIN ref_pays ON ref_etablissement.pays=ref_pays.code
			WHERE ref_etablissement.actif=1
			ORDER BY nom_pays, nom_etablissement" ;
	}
	$res = mysqli_query($cnx, $req) ;

	$liste  = "<select name='$name' id='$name'>\n" ;
	$liste .= "<option value='0'></option>\n" ;

	if ( $aucune ) {
		$liste .= "<option value='-1'" ;
		if ( $value == "-1" ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">Aucune</option>\n" ;
	}

	$pays = 0 ;
	$pays_precedent = "aucun" ;
	while ( $ligne = mysqli_fetch_assoc($res) ) {
		if	(
				//isset($_SESSION["liste_promotions"]) AND ($_SESSION["liste_promotions"] == "") AND
				($ligne["code_pays"] != $pays_precedent)
			)
		{
			if ( $pays != 0 ) {
				$liste .= "</optgroup>\n" ;
			}
			$pays++ ;
			$liste .= "<optgroup label=\"".$ligne["nom_pays"]."\">\n" ;
			$pays_precedent = $ligne["code_pays"] ;
		}
		$liste .= "<option value='".$ligne["code_etablissement"]."'" ;
		if ( $ligne["code_etablissement"] == $value ) {
			$liste .= " selected='selected'" ;
		}
		$liste .= ">".$ligne["nom_etablissement"] ;
		if ( $ligne["sigle"] != "" ) {
			$liste .= " (" . $ligne["sigle"] .")" ;
		}
		if ( ($requete == "formations") OR ($requete == "responsables") ) {
			$liste .= "<span style='padding-left: 1em;'> &nbsp; (" . $ligne["N"] .")</span>" ;
		}
		$liste .= "</option>\n" ;
	}
	//if ( $liste_promotions == "" ) {
		$liste .= "</optgroup>\n" ;
	//}	

	$liste .= "</select>\n" ;

	return $liste ;
}
function liste_institutions($cnx, $name, $value, $aucune=FALSE)
{
	echo listeInstitutions($cnx, $name, $value, $aucune) ;
}































// Avant migration
function formulaireInstitution($tab, $action="")
{
	$form  = "" ;
	$form .= "\n<form method='post' action='institution.php'>\n" ;
	$form .= "<table class='formulaire'>\n" ;

	$form .= "<tr>\n<th>Pays&nbsp;:</th>\n<td>" ;
	$form .= listePays("pays",
		( isset($tab["pays"]) ? $tab["pays"] : "" )
		) ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>Institution&nbsp;:</th>\n<td>" ;
	$form .= "<textarea name='institution' rows='2' cols='70'>" ;
	$form .= ( isset($tab["institution"]) ? $tab["institution"] : "" ) ;
	$form .= "</textarea>" ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>Code SQI&nbsp;:</th>\n<td>" ;
	$form .= "<input type='text' name='sqi_etab' size='5' value='" ;
	$form .= ( isset($tab["sqi_etab"]) ? $tab["sqi_etab"] : "" ) ;
	$form .= "' />" ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr><td colspan='2'>" ;
	$form .= "<input type='hidden' name='action' value='$action' />\n" ;
	$form .= "<input type='hidden' name='id_institution' value='"
		. ( isset($tab["id_institution"]) ? $tab["id_institution"] : "" )
		. "' />\n" ;
	$form .= "<p class='c'><input class='b' name='submit' type='submit' value='Enregistrer' /></p>\n" ;

	if ( isset($_GET["id_indispo"]) )
		$form .= "<p class='r'><a href='institution.php?action=delete&amp;id_institution=".$_GET["id_institution"]."'>Supprimer</a></p>" ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "</table>\n" ;
	$form .= "</form>" ;

	return $form ;
}


function verifier_institution($post)
{
	$erreurs = "" ;
	if ( trim($post["pays"]) == "" ) {
		$erreurs .= "<li>Le pays est obligatoire.</li>\n" ;
	}
	if ( trim($post["institution"]) == "" ) {
		$erreurs .= "<li>Le nom de l'institution est obligatoire.</li>\n" ;
	}
	if ( trim($post["sqi_etab"]) != "" )
	{
		if ( !is_numeric($post["sqi_etab"]) ) {
			$erreurs .= "<li>Le code SQI n'est pas obligatoire, mais il doit être un entier.</li>\n" ;
		}
	}

	if ( $erreurs != "" )
	{
		$erreurs = "<ul class='erreur c'>\n" . $erreurs . "</ul>\n" ;
	}
	return $erreurs ;
}



function formations_institution($cnx, $id_institution)
{
	$req = "SELECT id_atelier, intitule FROM atelier
		WHERE ref_institution=$id_institution
		ORDER BY groupe, niveau, intitule" ;
	$res = mysqli_query($cnx, $req) ;
	$N = mysqli_num_rows($res) ;
	if ( $N == 0 )
	{
		echo "<p class='c'>" ;
		echo "Aucune formation n'est liée à cette institution." ;
		echo " Elle peut donc être supprimée : " ;
		echo "<a href='/institutions/institution.php?action=delete&id=" ;
		echo $id_institution ;
		echo "'>Supprimer</a>" ;
		echo "</p>" ;
	}
	else
	{
		echo "<p class='c'>" ;
		if ( $N > 1 ) { $s = "s" ; } else { $s = "" ; }
		echo "<strong>" . $N . " formation" . $s . "</strong>" ;
		echo " liée" . $s . " à cette institution " ;
		echo "<span style='font-size: smaller;'>(qui ne peut donc pas être supprimée)</span>" ;
		echo " :" ;
		echo "</p>" ;

		echo "<table class='tableau'>" ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
			echo "<tr>\n" ;
			echo "<td>".$enr["intitule"]."</td>" ;
			echo "<td><a href='/formations/formation.php?id_atelier=".$enr["id_atelier"]."&action=modification'>Modifier</a></td>" ;
			echo "</tr>\n" ;
		}
		echo "</table>" ;
	}
}



?>
