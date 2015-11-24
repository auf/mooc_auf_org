<?php
include("inc_session.php") ;

if ( $_SESSION["id"] != "00" ) {
	header("Location: /candidatures/index.php") ;
	exit ;
}
if ( empty($_POST["id_dossier"]) ) {
	header("Location: /candidatures/index.php") ;
	exit ;
}

include("inc_mysqli.php") ;
$cnx = connecter() ;

$id_dossier = $_POST["id_dossier"] ;

$req = "SELECT id_candidat, id_session FROM dossier
	WHERE id_dossier=$id_dossier" ;
$res = mysqli_query($cnx, $req) ;
$enr = mysqli_fetch_assoc($res) ;
$id_candidat = $enr["id_candidat"] ;
$id_session = $enr["id_session"] ;

// Commentaires
$req = "DELETE FROM comment_auf WHERE ref_candidat=$id_candidat" ;
$res = mysqli_query($cnx, $req) ;
echo mysqli_error($cnx) ;
$req = "DELETE FROM comment_sel WHERE ref_candidat=$id_candidat" ;
$res = mysqli_query($cnx, $req) ;
echo mysqli_error($cnx) ;

// Réponses aux questions
$req = "DELETE FROM reponse WHERE id_dossier=$id_dossier" ;
$res = mysqli_query($cnx, $req) ;
echo mysqli_error($cnx) ;

// Stages
$req = "DELETE FROM stage WHERE id_candidat=$id_candidat" ;
$res = mysqli_query($cnx, $req) ;
echo mysqli_error($cnx) ;

// Diplômes
$req = "DELETE FROM diplomes WHERE id_candidat=$id_candidat" ;
$res = mysqli_query($cnx, $req) ;
echo mysqli_error($cnx) ;

// Candidat
$req = "DELETE FROM candidat WHERE id_candidat=$id_candidat" ;
$res = mysqli_query($cnx, $req) ;
echo mysqli_error($cnx) ;

// Dossier
$req = "DELETE FROM dossier WHERE id_dossier=$id_dossier" ;
$res = mysqli_query($cnx, $req) ;
echo mysqli_error($cnx) ;

deconnecter($cnx) ;
header("Location: /candidatures/candidatures.php?id_session=$id_session") ;
?>
