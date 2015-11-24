<?
session_start() ;
session_unset() ;
session_destroy() ;
$p = session_get_cookie_params() ;
setcookie(session_name(), "", 0, $p["path"], $p["domain"]) ;
if ( $_GET["temps"] == "temps" ) {
	header("Location: /login.php?temps=temps") ;
}
else {
	header("Location: /login.php") ;
}
?>
