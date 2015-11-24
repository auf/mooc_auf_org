<?php
// Génère un tableau de correspondance code nom de pays
// Permet d'éviter une requête par pays, ou une jointure dans les requêtes existantes avant intégration des données externes
// Permet aussi d'afficher simplement un code de pays qui ne se trouverait plus dans les données externes.
//$statiquePAYS = statiquePays($cnx) ;
function statiquePays($cnx)
{
	$statiquePAYS = array() ;
	// Cas des champs vides (anciens dossiers...)
	$statiquePAYS[""] = "" ;
	$req = "SELECT code, nom FROM ref_pays WHERE actif=1 ORDER BY nom" ;
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		$statiquePAYS[$enr["code"]] = $enr["nom"] ;
	}
	return $statiquePAYS ;
}

// FIXME 
// Affiche le pays correspondant au code, ou le code
function refPays($code, $statiquePAYS)
{
	if ( array_key_exists($code, $statiquePAYS) ) {
		return $statiquePAYS[$code] ;
	}
	else {
		return $code ;
	}
}

function selectPays($cnx, $name, $value="", $req="SELECT code, nom FROM ref_pays WHERE actif=1 ORDER BY nom")
{
	$str = "" ;
	$res = mysqli_query($cnx, $req) ;
	$str .= "<select name='$name'>\n" ;
	$str .= "<option value=''></option>" ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
	    $str .= "<option value='".$enr["code"]."'" ;
	    if ( $enr["code"] == $value ) {
	        $str .= " selected='selected'" ;
	    }
		$str .= ">".$enr["nom"]."</option>\n" ;
	}
	$str .= "</select>\n" ;
	return $str ;
}
function selectRegion($cnx, $name, $value="", $req="SELECT id, nom FROM ref_region WHERE actif=1 ORDER BY nom")
{
	$str = "" ;
	$res = mysqli_query($cnx, $req) ;
	$str .= "<select name='$name'>\n" ;
	$str .= "<option value=''></option>" ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
	    $str .= "<option value='".$enr["id"]."'" ;
	    if ( $enr["id"] == $value ) {
	        $str .= " selected='selected'" ;
	    }
		$str .= ">".$enr["nom"]."</option>\n" ;
	}
	$str .= "</select>\n" ;
	return $str ;
}


function selectPaysRegion($cnx, $name, $value="", $req="SELECT ref_pays.code AS code_pays, ref_pays.nom AS nom_pays, ref_region.nom AS nom_region
	FROM ref_pays LEFT JOIN ref_region ON ref_pays.region=ref_region.id
	WHERE ref_pays.actif=1 ORDER BY nom_region, nom_pays")
{
	$str = "" ;
	$res = mysqli_query($cnx, $req) ;
	$str .= "<select name='$name'>\n" ;
	$str .= "<option value=''></option>" ;
	$region_precedente = "aucune" ;
	$nbRegions = 0 ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		if ( $enr["nom_region"] != $region_precedente ) {
			if ( $nbRegions != 0 ) {
				$str .= "</optgroup>\n" ;
			}
			$str .= "<optgroup label=\"".$enr["nom_region"]."\">\n" ;
			$region_precedente = $enr["nom_region"] ;
			$nbRegions++ ;
		}
	    $str .= "<option value='".$enr["code_pays"]."'" ;
	    if ( $enr["code_pays"] == $value ) {
	        $str .= " selected='selected'" ;
	    }
		$str .= ">".$enr["nom_pays"]."</option>\n" ;
		
	}
	$str .= "</optgroup>\n" ;
	$str .= "</select>\n" ;
	return $str ;
}
?>
