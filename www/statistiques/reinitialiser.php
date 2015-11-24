<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["statistiques"]) ;
header("Location: /statistiques/") ;
?>
