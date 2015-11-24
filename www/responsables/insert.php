<?php
include("inc_session.php") ;
include("inc_mysqli.php") ;
$cnx = connecter() ;

$ref_institution = mysqli_real_escape_string($cnx, $_POST["ref_institution"]) ;
$zn = mysqli_real_escape_string($cnx, $_POST["zn"]) ;
$zp = mysqli_real_escape_string($cnx, $_POST["zp"]) ;
$zemail = mysqli_real_escape_string($cnx, $_POST["zemail"]) ;
$zuser = mysqli_real_escape_string($cnx, $_POST["zuser"]) ;
$zpwd = mysqli_real_escape_string($cnx, $_POST["zpwd"]) ;
$commentaire = mysqli_real_escape_string($cnx, $_POST["commentaire"]) ;
$req = "INSERT INTO selecteurs
	(ref_institution, nomsel, prenomsel, email, usersel, pwdsel, commentaire)
	VALUES('$ref_institution', '$zn','$zp','$zemail','$zuser','$zpwd','$commentaire')";
mysqli_query($cnx, $req) ;

$cs = mysqli_insert_id($cnx) ;

while ( list($indice, $val) = @each($_POST["dip"]) )
{
	$req = "insert into atxsel values('$val','$cs') ";
	mysqli_query($cnx, $req) ;
}

deconnecter($cnx) ;
header("Location: /responsables/index.php#r".$cs) ;
?>
