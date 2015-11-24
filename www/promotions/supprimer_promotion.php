<?php
include("inc_session.php") ;
include("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "SELECT COUNT(id_dossier) FROM dossier, candidat
	WHERE id_session=".$_GET["session"]
	." AND dossier.id_candidat=candidat.id_candidat" ;
$res = mysqli_query($cnx, $req) ;
$ligne = mysqli_fetch_row($res) ;
$N = $ligne[0] ;

if ( intval($N) == 0 ) {
	$req = "DELETE FROM session WHERE id_session=".$_GET["session"] ;
	$res = mysqli_query($cnx, $req);
	header("Location: index.php") ;
}
else {
	include("inc_html.php");
	echo $dtd1 ;
	echo "<title>Suppression impossible</title>" ;
	echo $dtd2 ;
	echo "<p>Suppression impossible, car il y a déjà des inscrits.</p>" ;
	echo $end ;
}
deconnecter($cnx) ;
?>
