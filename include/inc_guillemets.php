<?php

function enleve_guillemets($str)
{
//	$str = str_replace("'", "&apos;", $str) ;
	$str = str_replace('"', "&quot;", $str) ;
	return $str ;
}

function remets_guillemets($str)
{
//	$str = str_replace("&apos;", "'", $str) ;
	$str = str_replace("&quot;", '"', $str) ;
	return $str ;
}
function sans_balise($str)	
{
	$str = str_replace("<", "&lt;", $str) ;
	$str = str_replace(">", "&gt;", $str) ;
	return $str ;
}

function magic_strip($chaine)
{
	if ( get_magic_quotes_gpc() ) {
		return stripslashes($chaine) ;
	}
	else {
		return $chaine ;
	}
}
function magic_add($chaine)
{
	if ( get_magic_quotes_gpc() ) {
		return $chaine ;
	}
	else {
		return addslashes($chaine) ;
	}
}
?>
