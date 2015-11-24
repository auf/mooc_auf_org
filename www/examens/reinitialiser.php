<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["examens"]) ;
header("Location: /examens/") ;
?>
