<?php
include("inc_session.php") ;

include("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "DELETE FROM imputations
	WHERE id_imputation=".$_GET["id"] ;
$res = mysqli_query($cnx, $req) ;

deconnecter($cnx) ;
header("Location: /imputations/index.php") ;
?>
