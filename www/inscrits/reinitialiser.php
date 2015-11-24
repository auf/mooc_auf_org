<?php
include_once("inc_session.php") ;
unset($_SESSION["filtres"]["inscrits"]) ;
header("Location: /inscrits/inscrits.php?id_session=".$_GET["id_session"]) ;
?>
