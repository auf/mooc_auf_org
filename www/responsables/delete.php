<?php
include("inc_session.php") ;

if ( !isset($_GET["select"]) ) {
	header("Location: /responsables/index.php") ;
	exit() ;
}

include("inc_mysqli.php") ;
$cnx = connecter() ;

if ( isset($_GET["confirmation"]) AND ($_GET["confirmation"]=='oui') )
{
	$req = "DELETE  FROM selecteurs WHERE codesel=".$_GET["select"] ;
	$res = mysqli_query($cnx, $req) ;
	header("Location: /responsables/index.php") ;
}
else 
{
	// On compte les promotions, commentaires
	// et aussi les messages envoyés par l'adresse ?
	$req = "SELECT selecteurs.*,
	    (SELECT COUNT(id_atelier) FROM atxsel WHERE id_sel=codesel) AS N,
	    (SELECT COUNT(id_comment_sel) FROM comment_sel WHERE ref_selecteur=codesel) AS C,
	    institution
		FROM selecteurs
	    LEFT JOIN institutions ON id_institution=ref_institution
		WHERE codesel='".intval($_GET["select"])."'" ;
	$res = mysqli_query($cnx, $req) ;
	if ( mysqli_num_rows($res) == 0 ) {
		header("Location: /responsables/index.php") ;
		deconnecter($cnx) ;
		exit() ;
	}
	$enr = mysqli_fetch_assoc($res) ;

	include("inc_html.php") ;
	$titre = "Supprimer un responsable ?" ;
	echo $dtd1 . "<title>$titre</title>" . $dtd2 ;
	include("inc_menu.php") ;
	echo $debut_chemin ;
	echo "<a href='/bienvenue.php'>Accueil</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "<a href='/responsables/'>Responsables</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo $titre ;
	echo $fin_chemin ;

	require_once("inc_responsables.php") ;
	echo responsableData($cnx, $_GET["select"], TRUE) ;

	echo $end ;
} 

deconnecter($cnx) ;
?>
