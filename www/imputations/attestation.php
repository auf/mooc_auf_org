<?php
include("inc_session.php") ;

include("inc_html.php") ;
$titre = "Imputations" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo "<div class='noprint'>" ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/imputations/index.php'>" ;
echo $titre ;
echo "</a>" ;
echo "</div>" ;
echo $fin_chemin ;

include_once("inc_mysqli.php") ;
include_once("inc_imputations.php") ;
require_once("inc_date.php") ;
$cnx = connecter() ;

$req = "SELECT *
	FROM atelier, session, dossier, imputations
	WHERE id_imputation=".$_GET["id"]."
	AND ref_dossier=id_dossier
	AND dossier.ref_session=session.id_session
	AND session.ref_atelier=atelier.id_atelier" ;
$res = mysqli_query($cnx, $req) ;
$enr = mysqli_fetch_assoc($res) ;


if
	(
		( intval($_SESSION["id"]) < 3 )
		AND ( dateOuiNon($enr["imputations_deb"], $enr["imputations_fin"]) == "Oui"  )
	)
{
	echo "<div class='noprint' style='margin: 1em 0;'>" ;
	echo "<p class='c navig'>\n" ;

	if ( isset($_GET["action"]) AND ($_GET["action"] == "supprimer") ) {
		echo "Confirmer la suppression de cette imputation&nbsp;?" ;
		echo "<br />\n" ;
		echo "<br />\n" ;
		echo "<a href='supprimer.php?id=".$enr["id_imputation"]."'>Supprimer</a>\n" ;
		echo "<a href='attestation.php?id=" ;
		echo $enr["id_imputation"]."'>Annuler</a></strong>" ;
	}
	else {
		echo "<a href='attestation.php?id=".$enr["id_imputation"]."&amp;action=supprimer'>Supprimer</a>\n" ;
		echo "<a href='modifier.php?id_imputation=".$enr["id_imputation"]."'>Modifier</a>\n" ;
		echo "<a href='javascript.php' onclick='window.print(); return false;'>Imprimer</a>" ;
	}

	echo "</p>" ;

	echo "<br />\n" ;
	echo "</div>" ;
}

echo attestation($cnx, $enr) ;

deconnecter($cnx) ;
echo $end ;
?>
