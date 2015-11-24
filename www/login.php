<?php
session_start() ;
if ( $_GET["temps"] == "temps" ) {
	header("Location: /cookie.php?temps=temps") ;
}
else {
	header("Location: /cookie.php") ;
}
?>
