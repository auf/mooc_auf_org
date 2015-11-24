<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["institutions"]) ;
header("Location: /institutions/") ;
?>
