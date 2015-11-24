<?php
include("inc_session.php") ;
require_once("inc_mysqli.php") ;

//
// Changement d'état
//
if ( $_POST["changement_etat"] == "OK" )
{
	// Aucune candidature à changer d'état
	// Le tableau de chexboxes, (cases inscrits)
	if ( count($_POST["cinscrits"]) == 0 )
	{
		header("Location: /inscrits/inscrits.php?id_session=".$_POST["promotion"]."&erreur=zero") ;
	}
	else
	{
		$cnx = connecter() ;

		$liste_cinscrits = "" ;
		foreach($_POST["cinscrits"] as $cinscrit) {
			$liste_cinscrits .= $cinscrit . ", " ;
		}
		$liste_cinscrits = substr($liste_cinscrits, 0, -2) ;

		// Historique
		// Parmi les etats a modifier, quels sont ceux qui sont differents ?
		$req = "SELECT id_dossier FROM dossier
			WHERE etat_dossier != '".$_POST["nouvel_etat"]."'
			AND id_dossier IN (".$liste_cinscrits.")" ;
		//echo $req ."\n" ;
		$res = mysqli_query($cnx, $req) ;

		$tab_hist = array() ;		
		$liste_maj = "" ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
			$tab_hist[] = $enr["id_dossier"] ;
			$liste_maj .= $enr["id_dossier"] . ", " ;
		}
		$liste_maj = substr($liste_maj, 0, -2) ;
		require_once("inc_historique.php") ;
		foreach ($tab_hist as $id)
		{
			historiqueAdd($cnx, $id, $_POST["nouvel_etat"]) ;
		}

		$req = "UPDATE dossier SET etat_dossier='".$_POST["nouvel_etat"]."',
			date_maj_etat=CURDATE()
			WHERE id_dossier IN (".$liste_maj.")
			AND ref_session=".$_POST["promotion"] ;
		//echo $req ."\n" ;
		mysqli_query($cnx, $req) ;
		deconnecter($cnx) ;
		header("Location: /inscrits/inscrits.php?id_session=" . $_POST["promotion"] ) ;
	}
}
// Erreur
else {
	if ( !empty($_POST["promotion"]) ) {
		header("Location: /inscrits/inscrits.php?id_session=" . $_POST["promotion"] ) ;
	}
}

/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/
?>
