<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["individus"]) ;
header("Location: /individus/") ;
?>
