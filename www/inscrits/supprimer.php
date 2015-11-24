<?php
include("inc_session.php") ;

if ( $_SESSION["id"] != "00" ) {
	header("Location: /candidatures/index.php") ;
	exit ;
}

include("inc_html.php") ;
$titre = "Supprimer de candidature ?" ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
include("inc_menu.php") ;

include("inc_mysqli.php") ;
$cnx = connecter() ;


$req = "SELECT * FROM candidat, dossier
	WHERE dossier.id_dossier=".$_GET["id_dossier"]."
	AND dossier.id_candidat=candidat.id_candidat" ;
$res = mysqli_query($cnx, $req) ;
$T = mysqli_fetch_assoc($res) ;

// Chemin
$req = "SELECT intitule, intit_ses FROM atelier, session
    WHERE atelier.id_atelier=session.ref_atelier
    AND session.id_session=".$T["id_session"] ;
$res = mysqli_query($cnx, $req) ;
$enr = mysqli_fetch_assoc($res) ;

echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='index.php'>Gestion des candidatures</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='candidatures.php?id_session=".$T["id_session"]."'>".$enr["intitule"]." <span class='normal'>(".$enr["intit_ses"].")</span></a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "Supprimer un dossier de candidature&nbsp;?" ;
echo $fin_chemin ;



echo "<form method='post' action='delete.php'>" ;
echo "<input type='hidden' name='id_dossier' value='".$_GET["id_dossier"]."' />" ;

$bouton = "<p class='c'><input type='submit' style='font-weight: bold;' value='Supprimer ce dossier de candidature' /></p>\n" ;
echo $bouton ;


// Dossier
include("inc_formulaire_candidature.php") ;
include("inc_date.php");
include("inc_etat_dossier.php") ;
include("inc_dossier.php");
affiche_dossier($cnx, $T, FALSE) ;




echo $bouton ;
echo "</form>\n"  ;


echo $end;
deconnecter($cnx) ;
?>
