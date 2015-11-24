<?php
$titreIndispos = "Agenda des CNF" ;

function scriptCheckBoxes()
{
	$script = '<script type="text/javascript" language="javascript">
<!--
function checkAll() {
    lg=document.forms[0].elements.length;
    for ( i=0;i<lg;i++) {
        if (document.forms[0].elements[i].type=="checkbox") {
            document.forms[0].elements[i].checked=true;
        }
    }   
}     
function unCheck() {
    lg=document.forms[0].elements.length;
    for ( i=0;i<lg;i++) {
        if (document.forms[0].elements[i].type=="checkbox") {
            document.forms[0].elements[i].checked=false; 
        }
    }
}
-->
</script>' ;
	return $script ;
}

require_once("inc_cnf.php") ;
require_once("inc_date.php") ;


function checkboxCNF($name, $values)
{
    global $CNF ;
	$CNFCAI = $CNF ;
//	$CNFCAI[] = "L'apostrophe" ;
	$CNFCAI[] = "TOUS LES CNF" ;

/*
	if ( !is_array($values) ) {
		$values = array() ;
	}
*/

/*
    // Colonnes, si sans float
    $nb = 4 ; // nombre de colonnes
    $N = count($CNF) ;
    $n = (int)($N / $nb) ;
    $mod = $N % $nb ;
*/
    $output = "" ;
    foreach($CNFCAI as $cnf)
    {
        $output .= "<label class='cnf'>" ;
        $output .= '<input type="checkbox" name="'.$name.'[]"' ;
        $output .= ' value="'.$cnf.'"' ;
        if ( @in_array($cnf, $values) ) {
            $output .= " checked='checked'" ;
        }
        $output .= " /> " ;
        $output .= $cnf ;
        $output .= "</label>\n" ;
    }
    return $output;
}


function formulaireIndispo($tab, $action, $submit)
{
	$form  = "" ;
	$form .= "\n<form method='post' action='indispo.php'>\n" ;
	$form .= "<table class='formulaire'>\n" ;

    $form .= "<tr>\n<th>Date</th>\n<td>" ;
    $form .= "<input type='text' size='10' id='date_indispo' name='date_indispo' " ;
	if ( trim($tab["date_indispo"]) != "" ) {
    	$form .= " value='" ;
		$form .= mysql2date($tab["date_indispo"]) ;
		$form .= "' " ;
	}
	else {
    	$form .= " value='' " ;
	}
    $form .= " />" ;
    $form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n<th>Commentaire&nbsp;:</th>\n<td>" ;
	$form .= "<textarea name='commentaire' rows='3' cols='70'>" ;
	$form .= $tab["commentaire"] ;
	$form .= "</textarea>" ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr>\n" ;
	$form .= "<th>CNF&nbsp;:" ;
	$form .= "<div style='font-weight: normal; margin: 0.5em 0;'>" ;
	$form .= "<div><a href='javascript:checkAll()'>Tout cocher</a></div>\n" ;
	$form .= "<div><a href='javascript:unCheck()'>Tout décocher</a></div>\n" ;
	$form .= "</div>" ;
	$form .= "</th>\n" ;
	$form .= "<td style='width: 50em;'>" ;
	$form .= checkboxCNF("lieux",
		( isset($tab["lieux"]) ? $tab["lieux"] : "" )
		) ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "<tr><td colspan='2' class='invisible'>" ;
	$form .= "<input type='hidden' name='action' value='$action' />\n" ;
	$form .= "<input type='hidden' name='id_indispo' value='".$tab["id_indispo"]."' />\n" ;
	$form .= "<p class='c'><input class='b' type='submit' value='$submit' /></p>\n" ;

	if ( isset($_GET["id_indispo"]) )
		$form .= "<p class='r'><a href='indispo.php?action=delete&amp;id_indispo=".$_GET["id_indispo"]."'>Supprimer</a></p>" ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "</table>\n" ;
	$form .= "</form>" ;

	return $form ;
}


function verifier_indispo($post)
{
	$erreurs = "" ;
	if ( trim($post["date_indispo"]) == "" ) {
		$erreurs .= "<li>La date est obligatoire.</li>\n" ;
	}
	if ( trim($post["commentaire"]) == "" ) {
		$erreurs .= "<li>Le commentaire est obligatoire.</li>\n" ;
	}
	if ( !isset($post["lieux"]) OR !is_array($post["lieux"]) ) {
		$erreurs .= "<li>Il faut cocher au moins un CNF.</li>\n" ;
	}

	if ( $erreurs != "" )
	{
		$erreurs = "<ul class='erreur c'>\n" . $erreurs . "</ul>\n" ;
	}
	return $erreurs ;
}
?>
