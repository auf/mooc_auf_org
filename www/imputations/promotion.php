<?php
include("inc_session.php") ;
/*
while (list($key, $val) = each($_POST)) {
   echo "$key => $val<br />";
}
*/
$_SESSION["filtres"]["imputations"]["promotion"]  = $_GET["promotion"] ;
header("Location: index.php") ;
?>
