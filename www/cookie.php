<?php
session_start() ;

if ( !isset($_COOKIE["PHPSESSID"]) )
{
	include("inc_html.php") ;
	echo $dtd1 ;
	echo "<title>Cookies</title>" ;
	echo $dtd2 ;
	?>
<p>Votre navigateur doit accepter les cookies.</p>

<p>Une fois les cookies activés, <a href="/login.php">cliquez ici</a>.</p>
	
	<?php
	echo $end ;
}
else 
{
	if ( $_GET["temps"] == "temps" ) {
		header("Location: /identification.php?temps=temps") ;
	}
	else {
		header("Location: /identification.php") ;
	}
}
