<?php

function historiqueAdd($cnx, $ref_dossier, $etat_hist)
{
	$req = "INSERT INTO etat_hist
		(ref_dossier, etat_hist, date_hist, etat_par)
		VALUES(
			" . $ref_dossier . ",
			'". $etat_hist ."',
			CURDATE(),
			'". mysqli_real_escape_string($cnx, $_SESSION["utilisateur"]) ."'
		)" ;
	//echo $req ;
	mysqli_query($cnx, $req) ;
}

require_once("inc_date.php") ;
require_once("inc_etat_dossier.php") ;
function historiqueShow($cnx, $ref_dossier)
{
	global $ETAT_DOSSIER ;
	global $ETAT_DOSSIER_IMG_CLASS ;

	$req = "SELECT * FROM etat_hist WHERE ref_dossier=".$ref_dossier."
		ORDER BY id_etat_hist" ;
	$res = mysqli_query($cnx, $req) ;

	$tab = "" ;

	if ( mysqli_num_rows($res) == 0 ) {
		return $tab ;
	}

	$tab .= "<div class='encart'>\n" ;
	$tab .= "<div class='historique'><div>" ;
	$tab .= "<table class='petit' style='margin: 0'>\n" ;
	$tab .= "<caption>Historique de résultat</caption>\n" ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$tab .= "<tr>\n" ;
		$tab .= "<td>".mysql2date($enr["date_hist"])."</td>\n" ;
		$tab .= "<td><span class='".$ETAT_DOSSIER_IMG_CLASS[$enr["etat_hist"]]."'>".$ETAT_DOSSIER[$enr["etat_hist"]]."</span></td>\n" ;
		$tab .= "<td>".$enr["etat_par"]."</td>\n" ;
		$tab .= "</tr>\n" ;
	}
	$tab .= "</table>\n" ;
	$tab .= "</div></div>\n" ;
	$tab .= "</div>\n" ;
	return $tab ;
}


function historiqueTitle($cnx, $ref_dossier)
{
	$req = "SELECT * FROM etat_hist WHERE ref_dossier=".$ref_dossier."
		ORDER BY id_etat_hist" ;
	$res = mysqli_query($cnx, $req) ;

	$str = "" ;

	if ( mysqli_num_rows($res) == 0 ) {
		return $str ;
	}

	$str .= "<span class='help' title=\"" ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$str .= $enr["etat_hist"] ;
		$str .= " le " ;
		$str .= mysql2date($enr["date_hist"]) ;
		$str .= " par " ;
		$str .= $enr["etat_par"] ;
		$str .= "\n" ;
	}
	$str .= "\">" ;

	return $str ;
}

?>
