<?php
function enleve_guillemets($str)
{
    $str = str_replace("'", "&apos;", $str) ;
    $str = str_replace('"', "&quot;", $str) ;
    return $str ;
}

function remets_guillemets($str)
{
    $str = str_replace("&apos;", "'", $str) ;
    $str = str_replace("&quot;", '"', $str) ;
    return $str ;
}

$str = "Employé(e) d'une ONG ou d'une coopération" ;

$str = enleve_guillemets($str) ;
echo $str ;
echo "\n" ;

$str = remets_guillemets($str) ;
echo $str ;
echo "\n" ;


?>
