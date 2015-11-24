<?php
if ( isset($_GET["id_session"]) ) {
	$id_session = $_GET["id_session"] ;
}
else {
	$id_session = $T["id_session"] ;
}


$requete = "SELECT id_question, texte_quest
	FROM question, session, mod_dossier
	WHERE session.id_session=$id_session
	AND session.ref_atelier=mod_dossier.id_atelier
	AND mod_dossier.id_modos=question.id_modos
	ORDER BY id_question" ; 
$resultat = mysqli_query($cnx, $requete) ;
$nombre_questions = @mysqli_num_rows($resultat) ;

if ( $nombre_questions > 0 )
{
	while ( $ligne = mysqli_fetch_assoc($resultat) )
	{
		$Questions[] = $ligne ;
	}
}
?>
