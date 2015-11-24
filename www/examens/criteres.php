<?php
include("inc_session.php") ;
include("inc_date.php") ;
$_SESSION["filtres"]["examens"]["lieu"] = $_POST["lieu"] ;
$_SESSION["filtres"]["examens"]["pays"] = $_POST["pays"] ;
$_SESSION["filtres"]["examens"]["debut"] = date2mysql($_POST["debut"]) ;
$_SESSION["filtres"]["examens"]["fin"] = date2mysql($_POST["fin"]) ;
//print_r($_POST) ;
header("Location: /examens/") ;
?>
