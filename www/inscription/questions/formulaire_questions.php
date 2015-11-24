<?php
if ( isset($Questions) AND (count($Questions) > 0) )
{
	section_candidature("9") ;
	affiche_si_non_vide(isset($erreur_questions) ? $erreur_questions : "") ;
	$i = 1 ;
	foreach($Questions as $question)
	{
		echo "<tr>\n" ;
		echo "<td><div><strong>" ;
		echo $question["texte_quest"] ;
		echo "</strong></div>" ;
		textarea("question$i", $T["question$i"], 90, 10) ;
		echo "\n<input type='hidden' name='id_question$i' " ;
		echo "value='".$question["id_question"]."' />" ;
		echo "</td></tr>\n" ;
		$i++ ;
	}
	$nb = $i -1 ;
	echo "</table>\n" ;
	echo "<input type='hidden' name='nombre_questions' value='$nb' />\n" ;
}

?>
