<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["imputations"]) ;
header("Location: " . urldecode($_GET["redirect"])) ;
?>
