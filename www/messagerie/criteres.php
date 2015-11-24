<?php
include("inc_session.php") ;
require_once("inc_guillemets.php");
/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/
$_SESSION["filtres"]["messagerie"]["annee"] = $_POST["m_annee"] ;
$_SESSION["filtres"]["messagerie"]["groupe"] = $_POST["groupe"] ;
header("Location: /messagerie/index.php") ;
?>
