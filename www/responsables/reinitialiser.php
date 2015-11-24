<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["responsables"]) ;
header("Location: /responsables/") ;
?>
