<?php

function selectDevise($cnx, $name, $value="")
{
	$req = "SELECT code, nom FROM ref_devise ORDER BY code" ;
	$res = mysqli_query($cnx, $req) ;

	$str = "" ;
	$str .= "<select name='$name'>\n" ;
	$str .= "<option value=''></option>" ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$str .= "<option value='".$enr["code"]."'" ;
		if ( $enr["code"] == $value ) {
			$str .= " selected='selected'" ;
		}
		$str .= ">" ;
		$str .= $enr["code"] . " : " . $enr["nom"] ;
		$str .= "</option>\n" ;
	}
	$str .= "</select>\n" ;
	return $str ;
}



?>
