<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["formations"]) ;
header("Location: /formations/") ;
?>
