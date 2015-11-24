<?php
include("inc_session.php") ;
$_SESSION["selections_multiples"]["annee"] = $_POST["selection_annee"] ;
$_SESSION["selections_multiples"]["etat"] = $_POST["selection_etat"] ;
$_SESSION["selections_multiples"]["imputes"] = $_POST["selection_imputes"] ;
header("Location: /candidatures/selections_multiples.php") ;
?>
