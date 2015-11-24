<?php
include("inc_session.php") ;

// Aucune candidature à imprimer
if ( count($_POST["imprimer"]) == 0 )
{
	header("Location: /candidatures/candidatures.php?id_session="
		.$_POST["promotion"]."&erreur=imprimer") ;
}
// Impression
else
{
	// Candidatures
	$i = 0 ;
	foreach($_POST["imprimer"] as $imp) {
		if ( $i == 0 ) {
			$liste_imp = "$imp" ;
		}
		else {
			$liste_imp .= ", $imp" ;
		}
		$i++ ;
	}
	
	include("inc_html.php") ;
	$titre = "Dossier de candidature" ;
	echo $dtd1 ;
	echo "<title>$titre</title>" ;
	echo $dtd2 ;
	include("inc_menu.php") ;
	
	include("inc_mysqli.php") ;
	$cnx = connecter() ;
	
	// Chemin
	$req = "SELECT intitule, intit_ses
		FROM atelier, session
	    WHERE atelier.id_atelier=session.ref_atelier
	    AND session.id_session=".$_POST["promotion"] ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;
	
	echo $debut_chemin ;
	echo "<a href='/bienvenue.php'>Accueil</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "<a href='index.php'>Gestion des candidatures</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "<a href='candidatures.php?id_session=".$_POST["promotion"]."'>".$enr["intitule"]." <span class='normal'>(".$enr["intit_ses"].")</span></a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "Impression de $i dossiers de candidature le ".date("d/m/Y") ;
	if ( !empty($_SESSION["filtres"]["candidatures"]["etat"]) ) {
		echo " <span class='normal'>(".$_SESSION["filtres"]["candidatures"]["etat"].")</span>" ;
	}
	echo $fin_chemin ;
	
	
	$req = "SELECT * FROM candidat, dossier
		WHERE dossier.id_dossier IN ($liste_imp)
		AND dossier.id_candidat=candidat.id_candidat" ;
	$res = mysqli_query($cnx, $req) ;

	include("inc_formulaire_candidature.php") ;
	include("inc_date.php");
	include("inc_etat_dossier.php");
	include("inc_dossier.php");

	$i = 0 ;
	while( $T = mysqli_fetch_assoc($res) ) {
		if ( $i != 0 ) {
			echo "<hr class='saut'/>\n" ;
		}
		affiche_dossier($cnx, $T, FALSE) ;
		$i++ ;
	}
	
	echo $end;
	deconnecter($cnx) ;
}
?>
