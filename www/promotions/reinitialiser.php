<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["promotions"]) ;
header("Location: /promotions/") ;
?>
