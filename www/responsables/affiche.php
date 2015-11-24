<?php
include("inc_session.php") ;
include("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "SELECT selecteurs.*,
	(SELECT COUNT(id_atelier) FROM atxsel WHERE id_sel=codesel) AS N,
	(SELECT COUNT(id_comment_sel) FROM comment_sel WHERE ref_selecteur=codesel) AS C,
	ref_etablissement.nom AS institution
	FROM selecteurs
	LEFT JOIN ref_etablissement ON ref_etablissement.id=ref_institution
	WHERE codesel=".$_GET["select"] ;
$res = mysqli_query($cnx, $req) ;
$enr = mysqli_fetch_assoc($res) ;

$titre = $enr["prenomsel"] . " " . $enr["nomsel"] ;
include("inc_html.php") ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
//echo "<a href='/responsables/index.php'>Responsables</a>" ;
echo "<a href='/responsables/index.php'>Responsables</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $enr["prenomsel"]. " " . strtoupper($enr["nomsel"]) . "" ;
echo $fin_chemin ;

require_once("inc_responsables.php") ;
echo responsableData($cnx, $_GET["select"]) ;

echo "<p class='c'><strong>" ;
echo "<a href='modification.php?select=".$_GET["select"]."'>" ;
//echo "Modifier ce responsable</a></strong></p>\n\n" ;
echo "Modifier ce responsable</a></strong></p>\n\n" ;

deconnecter($cnx) ;
echo $end ;
?>
