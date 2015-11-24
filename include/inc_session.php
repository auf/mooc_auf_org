<?php
session_start() ;
if ( $_SESSION["authentification"] != "oui" ) {
	header("Location: /logout.php") ;
	exit ;
}
else if ( time() > ($_SESSION["temps"] + 3600) ) {
    header("Location: /logout.php?temps=temps") ;
	exit ;
}
else {
	$_SESSION["temps"] = time() ;
}

require_once("inc_config.php") ;

function diagnostic()
{
	echo "<pre>" ;
	echo "<strong>POST</strong> : " ;
	print_r($_POST) ;
	echo "<strong>GET</strong> : " ;
	print_r($_GET) ;
	echo "<strong>SESSION</strong> : " ;
	print_r($_SESSION) ;
	echo "</pre>" ;
}
?>
