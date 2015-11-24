<?php
include("inc_session.php") ;
include("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "UPDATE selecteurs
	SET ref_institution='".$_POST["ref_institution"]."',
	nomsel='".mysqli_real_escape_string($cnx, $_POST["zn"])."',
	prenomsel='".mysqli_real_escape_string($cnx, $_POST["zp"])."',
	usersel='".mysqli_real_escape_string($cnx, $_POST["zuser"])."',
	pwdsel='".mysqli_real_escape_string($cnx, $_POST["zpwd"])."',
	transfert='".$_POST["transfert"]."',
	commentaire='".mysqli_real_escape_string($cnx, $_POST["commentaire"])."',
	email='".mysqli_real_escape_string($cnx, $_POST["zemail"])."'
	WHERE codesel='".$_POST["select"]."'" ;
mysqli_query($cnx, $req) ;

$req = "DELETE  FROM atxsel WHERE id_sel='".$_POST["select"]."'" ;
mysqli_query($cnx, $req) ;

while ( list($indice, $val) = @each($_POST["dip"]) )
{
   $req = "INSERT INTO atxsel VALUES('$val','".$_POST["select"]."') ";
   mysqli_query($cnx, $req) ;
}

deconnecter($cnx) ;
header("Location: /responsables/index.php#r".$_POST["select"]) ;
?>
