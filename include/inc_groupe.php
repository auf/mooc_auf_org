<?php
function select_groupe($groupe)
{
    $GROUPE = array(
        "",
        "Droit, économie et gestion",
        "Éducation et formation",
        "Environnement et développement durable",
        "Médecine et santé publique",
        "Sciences de l'ingénieur",
        "Sciences humaines",
        "Test",
/*
*/
    ) ;
    $select = "<select name=\"groupe\">\n" ;
    foreach($GROUPE as $Groupe) {
        $select .= "<option value=\"$Groupe\"" ;
        if ( $Groupe == $groupe ) {
            $select .= " selected=\"selected\"" ;
        }
        $select .= ">$Groupe</option>\n" ;
    }
    $select .= "</select>" ;
    return $select ;
}
?>
