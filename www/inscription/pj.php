<?php
$id_pj = $_GET["id_pj"] ;
$ref_dossier = $_GET["ref_dossier"] ;
$fichier = urldecode($_GET["fichier"]) ;

include("inc_mysqli.php") ;
$cnx = connecter() ; 

$req = "SELECT * FROM pj
	WHERE id_pj=$id_pj
	AND ref_dossier=$ref_dossier
	AND fichier='".mysqli_real_escape_string($cnx, $fichier)."'" ;
$res = mysqli_query($cnx, $req) ;
if ( mysqli_num_rows($res) != 0 )
{
	$enr = mysqli_fetch_assoc($res) ;
	$chemin = $_SERVER["DOCUMENT_ROOT"] . "/../pj/" . $ref_dossier . "/" . $fichier ;
	//$chemin = DIR_PJ . $ref_dossier . "/" . $fichier ;
	$mime = $enr["mime"] ;
	$largeur = $enr["largeur"] ;
	$hauteur = $enr["hauteur"] ;

	if ( isset($_GET["action"]) AND ($_GET["action"] == "voir") ) {
		header("Content-Type: text/html") ;
		echo "<html>\n<head></head>\n<body>\n" ;
		$url_image = "/inscription/pj.php?id_pj=".$id_pj
			. "&amp;ref_dossier=".$ref_dossier
			. "&amp;fichier=".urlencode($fichier) ;
		echo "<img src='$url_image' width='$largeur' height='$hauteur' alt=\"$fichier\" title=\"$fichier\"/>\n" ;
		echo "</body></html>" ;
	}
	else {
		if ( isset($_GET["taille"]) AND ($_GET["taille"] == "vignette") ) {
			$chemin = $_SERVER["DOCUMENT_ROOT"] . "/../pj/" . $ref_dossier . "/zz_" . $fichier ;
			//$chemin = DIR_PJ . $ref_dossier . "/zz_" . $fichier ;
		}
		header("Content-Type: $mime") ;
		header("Content-Disposition: attachment; filename=$fichier") ;
		header("\n") ;
		@readfile($chemin) ;
	}
}
else
{
	echo "Accès interdit" ;
}
deconnecter($cnx) ;
?>
