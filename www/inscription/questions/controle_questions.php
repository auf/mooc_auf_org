<?php
$erreur_questions = "" ;

if ( $nombre_questions > 0 )
{
	for ( $i=1 ; $i <= $nombre_questions ; $i++ )
	{
		if ( trim($T["question$i"]) == "" ) {
			$erreur_questions .= "<li>La réponse à la question " ;
			$erreur_questions .= "«&nbsp;<span class='erreur_champ'>" ;
			$erreur_questions .= $Questions[$i-1]["texte_quest"] ;
			$erreur_questions .= "</span>&nbsp;» est obligatoire.</li>\n" ;
		}
	}
}

?>
