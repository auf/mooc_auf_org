<?php
include("inc_session.php") ;

include("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "SELECT * FROM atelier, session, dossier
	LEFT JOIN imputations ON imputations.ref_dossier=dossier.id_dossier
	WHERE session.id_session=dossier.ref_session
	AND atelier.id_atelier=session.ref_atelier
	AND dossier.id_dossier=".$_GET["id_dossier"] ;
$res = mysqli_query($cnx, $req) ;
if ( mysqli_num_rows($res) == 0 ) {
	deconnecter($cnx) ;
	header("Location: /inscrits/index.php") ;
	exit ;
}
$T = mysqli_fetch_assoc($res) ;

if ( ( intval($_SESSION["id"]) > 4 )
	AND ( !in_array($T["id_session"], $_SESSION["tableau_toutes_promotions"]) ) )
{
	deconnecter($cnx) ;
	header("Location: /inscrits/index.php") ;
	exit ;
}

$titre = strtoupper($T["nom"]) . " " . $T["prenom"] . " - " . $T["intitule"] . " - " . $T["intit_ses"] ;
include("inc_html.php") ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $htmlPhotobox ; // Gallerie
echo $dtd2 ;
include("inc_menu.php") ;

echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='index.php'>Gestion des inscrits</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='inscrits.php?id_session=".$T["id_session"]."#d".$T["id_dossier"]."'>".$T["intitule"]." - ".$T["intit_ses"]."</a>" ;
echo $fin_chemin ;

//Ajout de l'institution au tableau
$T["ref_institution"] = $enr["ref_institution"] ;

// Dossier
include_once("inc_formulaire_inscription.php") ;
include_once("inc_date.php");
include_once("inc_etat_dossier.php") ;
include_once("inc_dossier.php");
//include_once("inc_guillemets.php");

affiche_dossier($cnx, $T) ;

echo "<div style='float: left; width: 50%; margin: 0 1em 0 0;'>\n" ;
echo "</div>\n" ;





echo "<script type='text/javascript'>
$(document).ready(function() {
  $('#pj').photobox('a.box',{ time:0, single:true});
});
</script>\n" ;

echo $end;
deconnecter($cnx) ;
?>
