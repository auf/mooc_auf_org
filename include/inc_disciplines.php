<?php

function selectDiscipline($cnx, $name, $value="", $requete="", $aucune="FALSE")
{
	if ( $requete == "formations" ) {
		$req = "SELECT ref_discipline, COUNT(ref_discipline) AS N, code, nom
			FROM atelier JOIN ref_discipline ON atelier.ref_discipline=ref_discipline.code
			GROUP BY ref_discipline ORDER BY code" ;
	}
	else {
		$req = "SELECT code, nom FROM ref_discipline WHERE actif=1 ORDER BY nom" ;
	}
	$str = "" ;
	$res = mysqli_query($cnx, $req) ;
	$str .= "<select name='$name'>\n" ;
	$str .= "<option value=''></option>" ;

	if ( $aucune ) {
		$str .= "<option value='-1'" ;
		if ( $value == "-1" ) {
			$str .= " selected='selected'" ;
		}
		$str .= ">Aucune</option>\n" ;
	}

	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$str .= "<option value='".$enr["code"]."'" ;
		if ( $enr["code"] == $value ) {
			$str .= " selected='selected'" ;
		}
		$str .= ">".$enr["nom"] ;
		if ( $requete == "formations" ) {
			$str .= " &nbsp; (" . $enr["N"] . ")" ; 
		}
		$str .= "</option>\n" ;
	}
	$str .= "</select>\n" ;
	return $str ;
}



?>
