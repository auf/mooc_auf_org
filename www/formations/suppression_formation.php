<?php
include("inc_session.php") ;
include("inc_mysqli.php") ;
$cnx = connecter() ;

$id_atelier = $_GET["id_atelier"] ;

// On vérifie que l'atelier n'a pas de session
$req = "SELECT COUNT(id_session) AS N FROM session WHERE id_atelier=$id_atelier" ;
$res = mysqli_query($cnx, $req) ;
$enregistrement = mysqli_fetch_assoc($res) ;

if ( strval($enregistrement["N"]) == 0 )
{
	$req = "DELETE FROM atelier WHERE id_atelier=$id_atelier" ;
	$res = mysqli_query($cnx, $req) ;
	header("Location: index.php") ;
}
else {
	echo "<p>Cette formation ne peut pas être supprimée, car il en existe des promotions&nbsp;!</p>" ;
	echo "<p><a href='index.php'>Retour à la gestion des formations</a></p>" ;
}
deconnecter($cnx) ;
?>
