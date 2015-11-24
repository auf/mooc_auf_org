<?php
include("inc_session.php") ;
include("inc_mysqli.php") ;
$cnx = connecter() ;

/*
while (list($key, $val) = each($_POST)) {
   echo "$key => $val<br />";
}
*/

if ( isset($_POST["comment_auf"]) )
{
	if ( !empty($_POST["id_comment_auf"]) )
	{
		$req = "UPDATE comment_auf 
			SET commentaire='".trim($_POST["comment_auf"])."'
			WHERE id_comment_auf=".$_POST["id_comment_auf"] ;
		mysqli_query($cnx, $req) ;
	}
	else {
		if ( trim($_POST["comment_auf"]) != "" )
		{
			$req  = "INSERT INTO comment_auf(ref_candidat, commentaire) " ;
			$req .= "VALUES(".$_POST["id_candidat"].", " ;
			$req .= "'".trim($_POST["comment_auf"])."')" ;
			mysqli_query($cnx, $req) ;
		}
	}
}
// Comentaire sélectionneur
if ( isset($_POST["comment_sel"]) )
{
	if ( !empty($_POST["id_comment_sel"]) )
	{
		$req = "UPDATE comment_sel
			SET commentaire='".trim($_POST["comment_sel"])."',
			etat_sel='".$_POST["etat_sel"]."'
			WHERE id_comment_sel=".$_POST["id_comment_sel"] ;
		mysqli_query($cnx, $req) ;
	}
	else {
		$req  = "INSERT INTO comment_sel(ref_selecteur, ref_candidat, commentaire, etat_sel) " ;
		$req .= "VALUES(".$_SESSION["id"].", " ;
		$req .= $_POST["id_candidat"].", " ;
		$req .= "'".trim($_POST["comment_sel"])."', " ;
		$req .= "'".$_POST["etat_sel"]."')" ;
		mysqli_query($cnx, $req) ;
	}
}
// Récupération des champs etat et transferts
$req = "SELECT id_session, etat_dossier, transferts FROM dossier
	 WHERE id_dossier=".$_POST["id_dossier"] ;
$res = mysqli_query($cnx, $req) ;
$enr = mysqli_fetch_assoc($res) ;
$ancien_etat = $enr["etat_dossier"] ;
$ancien_transferts = $enr["transferts"] ;
$ancien_id_session = $enr["id_session"] ;

// Etat
if ( ( $_SESSION["id"] != "02" ) AND ( $ancien_etat != $_POST["etat"] ) )
{
	$req = "UPDATE dossier SET etat_dossier='".$_POST["etat"]."',
		date_maj_etat=CURDATE()
		WHERE id_dossier=".$_POST["id_dossier"] ;
	mysqli_query($cnx, $req) ;

	require_once("inc_historique.php") ;
	historiqueAdd($cnx,
		$_POST["id_dossier"], $_POST["etat"], $_POST["evaluations"]) ;
}

// Transfert
if ( !empty($_POST["transfert"]) )
{
	$req = "SELECT intitule FROM atelier, session
		WHERE session.ref_atelier=atelier.id_atelier
		AND session.id_session=$ancien_id_session" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;
	$nouveau_transferts = $ancien_transferts
		. " Le " . date("d/m/Y") . " de " . $enr["intitule"] . "." ;
	$req = "UPDATE dossier SET
		id_session=".$_POST["transfert"].",
		transferts='".mysqli_real_escape_string($cnx, $nouveau_transferts)."'
		WHERE id_dossier=".$_POST["id_dossier"] ;
	mysqli_query($cnx, $req) ;
}

deconnecter($cnx) ;
header("Location: /candidatures/candidatures.php?id_session=".$_POST["id_session"]."#d".$_POST["id_dossier"]) ;
?>
