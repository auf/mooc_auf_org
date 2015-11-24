<?php
include("inc_session.php") ;

unset($_SESSION["filtres"]["exporter"]) ;
$nbChamps = 0 ;
while (list($key, $val) = each($_POST)) {
	if ( $key == $val ) {
		$nbChamps++ ;
		$_SESSION["filtres"]["exporter"][$key] = $val ;
	}
}

if ( $nbChamps != 0 ) {
	//print_r($_SESSION["filtres"]["exporter"]) ;
	$_SESSION["modif_champs_export"] = "ok" ;
}

header("Location: /exports/")
?>
