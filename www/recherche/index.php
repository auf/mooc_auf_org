<?php
include_once("inc_session.php") ;
include_once("inc_recherche.php") ;
require_once("inc_mysqli.php");
$cnx = connecter() ;

require_once("inc_html.php");
$titre = "Recherche, export" ;
$avant_menu = $dtd1
	. "<title>$titre</title>"
	. $htmlJquery
	. $htmlMakeSublist
	. $dtd2 ;
$apres_menu = $debut_chemin
	. "<a href='/bienvenue.php'>Accueil</a>"
	. " <span class='arr'>&rarr;</span> "
	. $titre
	. $fin_chemin ;


if ( 
		// Après clic sur bouton rechercher
		(
			isset($_SESSION["filtres"]["recherche"]["ok"])
			AND ($_SESSION["filtres"]["recherche"]["ok"] == "ok")
		)
		// Après clic sur lien vers page de résultats
		OR ( isset($_GET["page"]) )
		// Après clic sur bouton Exporter
	)
{
	$req = "FROM atelier, session, dossier
		LEFT JOIN ref_pays ON dossier.pays_residence=ref_pays.code
		LEFT JOIN ref_region ON ref_pays.region=ref_region.id
		LEFT JOIN imputations ON imputations.ref_dossier=dossier.id_dossier
		WHERE atelier.id_atelier=session.ref_atelier
		AND session.id_session=dossier.ref_session" ;
	if ( intval($_SESSION["id"]) > 9 )
	{
		$req = "FROM atelier, atxsel, session, dossier
			LEFT JOIN ref_pays ON dossier.pays_residence=ref_pays.code
			LEFT JOIN ref_region ON ref_pays.region=ref_region.id
			LEFT JOIN imputations ON imputations.ref_dossier=dossier.id_dossier
			WHERE atelier.id_atelier=atxsel.id_atelier
			AND atxsel.id_sel=".$_SESSION["id"]."
			AND atelier.id_atelier=session.ref_atelier
			AND session.id_session=dossier.ref_session" ;
	}
	// Année
	if ( isset($_SESSION["filtres"]["recherche"]["annee"]) ) {
		$req .= " AND session.annee=".$_SESSION["filtres"]["recherche"]["annee"] ;
	}
	// Institution
	if ( isset($_SESSION["filtres"]["recherche"]["ref_institution"]) AND ($_SESSION["filtres"]["recherche"]["ref_institution"]!=0) ) {
		$req .= " AND atelier.ref_institution=".$_SESSION["filtres"]["recherche"]["ref_institution"] ;
	}
	// Formation
	if ( isset($_SESSION["filtres"]["recherche"]["formation"]) AND ($_SESSION["filtres"]["recherche"]["formation"]!=0) ) {
		$req .= " AND atelier.id_atelier=".$_SESSION["filtres"]["recherche"]["formation"] ;
	}
	// Inscription
	if ( isset($_SESSION["filtres"]["recherche"]["promo"]) AND ($_SESSION["filtres"]["recherche"]["promo"]!=0) ) {
		$req .= "  AND dossier.ref_session=".$_SESSION["filtres"]["recherche"]["promo"] ;
	}

	// Etat
	if ( isset($_SESSION["filtres"]["recherche"]["etat"]) ) {
		if ( $_SESSION["filtres"]["recherche"]["etat"] == "I" ) {
			$req .= " AND id_imputation IS NOT NULL " ;
		}
		else if ( $_SESSION["filtres"]["recherche"]["etat"] == "P" ) {
			$req .= " AND id_imputation IS NULL " ;
		}
	}
	// Lieu de paiement
	if ( isset($_SESSION["filtres"]["recherche"]["lieu_paiement"]) AND ($_SESSION["filtres"]["recherche"]["lieu_paiement"]!="") ) {
		$req .= "  AND lieu_paiement='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["recherche"]["lieu_paiement"])."'" ;
	}
	// Lieu de examen
	if ( isset($_SESSION["filtres"]["recherche"]["lieu_examen"]) AND ($_SESSION["filtres"]["recherche"]["lieu_examen"]!="") ) {
		$req .= "  AND lieu_examen='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["recherche"]["lieu_examen"])."'" ;
	}
	// Résultat (etat_dossier
	if ( isset($_SESSION["filtres"]["recherche"]["etat_dossier"]) ) {
		if ( $_SESSION["filtres"]["recherche"]["etat_dossier"] == "0" ) {
			$req .= " AND etat_dossier='".$_SESSION["filtres"]["recherche"]["etat_dossier"]."'" ;
			$req .= " AND id_imputation != ''" ;
		}
		else {
			$req .= " AND etat_dossier='".$_SESSION["filtres"]["recherche"]["etat_dossier"]."'" ;
		}
	}

	// Genre
	if ( isset($_SESSION["filtres"]["recherche"]["genre"]) ) {
		if ( $_SESSION["filtres"]["recherche"]["genre"] == "Homme" ) {
			$req .= " AND dossier.genre='Homme'" ;
		}
		else if ( $_SESSION["filtres"]["recherche"]["genre"] == "Femme" ) {
			$req .= " AND dossier.genre='Femme'" ;
		}
	}
	// ID MOOC
	if ( isset($_SESSION["filtres"]["recherche"]["id_mooc"]) ) {
		$req .= " AND dossier.id_mooc LIKE '%" ;
		$req .= mysqli_real_escape_string($cnx, $_SESSION["filtres"]["recherche"]["id_mooc"]) ;
		$req .= "%' " ;
	}
	// Nom
	if ( isset($_SESSION["filtres"]["recherche"]["nom"]) ) {
		$req .= " AND dossier.nom LIKE '%" ;
		$req .= mysqli_real_escape_string($cnx, $_SESSION["filtres"]["recherche"]["nom"]) ;
		$req .= "%' " ;
	}
	// Prénom
	if ( isset($_SESSION["filtres"]["recherche"]["prenom"]) ) {
		$req .= " AND dossier.prenom LIKE '%" ;
		$req .= mysqli_real_escape_string($cnx, $_SESSION["filtres"]["recherche"]["prenom"]) ;
		$req .= "%' " ;
	}
	// Courriel
	if ( isset($_SESSION["filtres"]["recherche"]["email"]) ) {
		$req .= " AND dossier.email LIKE '%" ;
		$req .= mysqli_real_escape_string($cnx, $_SESSION["filtres"]["recherche"]["email"]) ;
		$req .= "%' " ;
	}
	// Pays
	if ( isset($_SESSION["filtres"]["recherche"]["pays"]) ) {
		$req .= " AND dossier.pays_residence='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["recherche"]["pays"])."'" ;
	}
	// Région
	if ( isset($_SESSION["filtres"]["recherche"]["region"]) AND ($_SESSION["filtres"]["recherche"]["region"] != "") )
	{
		$req .= " AND ref_region.id=".$_SESSION["filtres"]["recherche"]["region"] ;
	}

	// Tri
	$order = "" ;
	if ( isset($_SESSION["filtres"]["recherche"]["tri1"]) ) {
		$order .= " ORDER BY ".$_SESSION["filtres"]["recherche"]["tri1"] ;
		if ( isset($_SESSION["filtres"]["recherche"]["tri2"]) ) {
			$order .= ", ".$_SESSION["filtres"]["recherche"]["tri2"] ;
			if ( isset($_SESSION["filtres"]["recherche"]["tri3"]) ) {
				$order .= ", ".$_SESSION["filtres"]["recherche"]["tri3"] ;
			}
		}
	}



	// *********
	// RECHERCHE
	// *********
	if	(
			($_SESSION["filtres"]["recherche"]["submit"] == "Rechercher")
			OR isset($_GET["page"])
		)
	{
		echo $avant_menu ;
		require_once("inc_menu.php");
		echo $apres_menu ;
		formulaire_recherche($cnx) ;

		$req_count = "SELECT dossier.id_dossier, id_imputation $req" ;
		//echo $req_count ;
		$res_count = mysqli_query($cnx, $req_count) ;
		$N = mysqli_num_rows($res_count) ;
		
	
	
		// Résultat de la recherche : Inscrits / Pré-inscrits / (Pré-)Inscrits
		if	(
				isset($_SESSION["filtres"]["recherche"]["etat"])
				AND ( $_SESSION["filtres"]["recherche"]["etat"] == "P" )
			)
		{
			$resultats = "pré-inscrit" ;
		}
		else if (
				( isset($_SESSION["filtres"]["recherche"]["etat"]) AND ( $_SESSION["filtres"]["recherche"]["etat"] == "I" ) )
				OR ( isset($_SESSION["filtres"]["recherche"]["lieu_paiement"]) AND ($_SESSION["filtres"]["recherche"]["lieu_paiement"]!="") )
				OR ( isset($_SESSION["filtres"]["recherche"]["lieu_examen"]) AND ($_SESSION["filtres"]["recherche"]["lieu_examen"]!="") )
				OR ( isset($_SESSION["filtres"]["recherche"]["etat_dossier"]) )
			)
		{
			$resultats = "inscrit" ;
		}
		else
		{
			$resultats = "(pré-) inscrit" ;
		}
	
	
		if ( $N == 0 ) {
			echo "<p class='c'><strong>" ;
			echo "Aucun $resultats pour ces critères" ;
			echo "</strong></p>" ;
			echo "</form>" ;
		}
		else
		{
			if ( $N == 1 ) {
				echo "<p id='resul' class='c'><strong>" ;
				echo $N . " " . $resultats ;
				echo "</strong></p>\n" ;
				echo "</form>" ;
			}
			else
			{
				echo "<p id='resul' class='c'><strong>" ;
				echo $N . " " . $resultats . "s" ;
				echo "</strong></p>\n" ;
				echo "</form>" ;
			}
	
			define("NB_PAR_PAGE", 20) ;
			$nbPages = intval($N / NB_PAR_PAGE) ;
			if ( ($N % NB_PAR_PAGE) != 0 ) {
				$nbPages++ ;
			}
	
			if ( $nbPages > 1 ) {
				if ( !isset($_GET["page"]) ) {
					$start = "" ;
				}
				else {
					$start = ( intval($_GET["page"]) -1 ) * NB_PAR_PAGE ;
					$start = strval($start).", " ;
				}
			}
			else {
				$start = "" ;
			}
	
			$nav = "" ;
			if ( $nbPages > 1 )
			{
				if ( !isset($_GET["page"]) ) {
					$_GET["page"] = 1 ;
				}
				$page = intval($_GET["page"]) ;
				$nav  = "<p class='c pagination'>" ;
				if ( $page > 2 ) {
					$nav .= "<a href='?page=1#resul'>" ;
					$nav .= "<img src='/img/pagination/premiere.gif' " ;
					$nav .= "alt='Première page' title='Première page' " ;
					$nav .= "width='39' height='30' /></a>" ;
				}
				if ( $page > 1 ) {
					$nav .= "<a href='?page=".strval($page -1)."#resul'>" ;
					$nav .= "<img src='/img/pagination/precedente.gif' " ;
					$nav .= "alt='Page précédente' title='Page précédente' " ;
					$nav .= "width='30' height='30' /></a>" ;
				}
				$nav .= "<strong>Page ". $page ." / ".$nbPages."</strong>" ;
				if ( $page < $nbPages) {
					$nav .= " <a href='?page=".strval($page +1)."#resul'>" ;
					$nav .= "<img src='/img/pagination/suivante.gif' " ;
					$nav .= "alt='Page suivante' title='Page suivante' " ;
					$nav .= "width='30' height='30' /></a>" ;
				}
				if ( $page < ($nbPages - 1) ) {
					$nav .= " <a href='?page=".$nbPages."#resul'>" ;
					$nav .= "<img src='/img/pagination/derniere.gif' " ;
					$nav .= "alt='Dernière page' title='Dernière page' " ;
					$nav .= "width='39' height='30' /></a>" ;
				}
				$nav .= "</p>\n" ;
			}
	
			echo $nav ;
	
			$req_select = "SELECT dossier.*,
				(DATEDIFF(date_examen, naissance) DIV 365.25 ) AS age,
				intitule,
				session.*,
				id_imputation, lieu_paiement, lieu_examen,
				ref_pays.nom AS nom_pays,
				ref_region.nom AS ref_region
				$req $order LIMIT $start "
				.NB_PAR_PAGE ;
			//echo $req_select ;
			$res =  mysqli_query($cnx, $req_select) ;	
			while ( $row = mysqli_fetch_assoc($res) )
			{
				resultat_recherche($row) ;
			}
	
			echo $nav ;
		}
		echo $end ;
	}
	// ******
	// EXPORT
	// ******
	else if ( $_SESSION["filtres"]["recherche"]["submit"] == "Exporter" )
	{
		require_once("inc_traitements_caracteres.php") ;
		require_once("inc_formulaire_inscription.php") ;
		require_once("inc_pays.php") ;
		$statiquePays = statiquePays($cnx) ;

		

		$fp = fopen("php://temp", "r+") ;

		$fichier = date("Y-m-d-H-s", time()) . ".csv" ;

		if ( isset($_SESSION["filtres"]["exporter"]) AND is_array($_SESSION["filtres"]["exporter"]) AND (count($_SESSION["filtres"]["exporter"])!=0) )
		{
		}
		else
		{
			$champs = array("id_mooc", "email", "genre", "nom", "prenom", "naissance", "age",
				"lieu_naissance", "pays_naissance", "pays_nationalite", "pays_residence", "situation_actu", "sit_autre",
				"ident_nature", "ident_autre", "ident_numero", "ident_date", "ident_lieu",
				"id_dossier", "date_inscrip", "date_maj",
				"id_imputation", "date_imput", "date_maj_imput", "lieu_paiement", "montant", "monnaie", "imputation", "lieu_examen",
				"etat_dossier", "date_maj_etat") ;

			if ( intval($_SESSION["id"]) > 9 )
			{
				$req_export = "SELECT id_mooc, email, genre, dossier.nom, prenom, naissance,
					(DATEDIFF(date_examen, naissance) DIV 365.25) AS age,
					lieu_naissance, pays_naissance, pays_nationalite, pays_residence, situation_actu, sit_autre,
					ident_nature, ident_autre, ident_numero, ident_date, ident_lieu,
					id_dossier, date_inscrip, date_maj,
					id_imputation, date_imput, date_maj_imput, lieu_paiement, montant, imputations.monnaie, imputation, lieu_examen,
					etat_dossier, date_maj_etat " ;
			}
			else
			{
				$req_export = "SELECT id_mooc, email, genre, dossier.nom, prenom, naissance,
					(DATEDIFF(date_examen, naissance) DIV 365.25) AS age,
					lieu_naissance, pays_naissance, pays_nationalite, pays_residence, situation_actu, sit_autre,
					ident_nature, ident_autre, ident_numero, ident_date, ident_lieu,
					id_dossier, date_inscrip, date_maj,
					id_imputation, date_imput, date_maj_imput, lieu_paiement, montant, imputations.monnaie, imputation, lieu_examen,
					etat_dossier, date_maj_etat,
					ref_institution, universite, groupe, ref_discipline, intitule, intit_ses, id_session " ;
				array_push($champs, "ref_institution", "universite", "groupe", "ref_discipline", "intitule", "intit_ses", "id_session") ;
			}
			$tableau = array() ;
			if ( isset($_SESSION["filtres"]["recherche"]["latin1"]) AND ($_SESSION["filtres"]["recherche"]["latin1"] = "latin1") )
			{
				foreach($champs as $champ) {
					$tableau[] = utf8_decode(libelc("$champ")) ;
				}
			}
			else
			{
				foreach($champs as $champ) {
					$tableau[] = libelc("$champ") ;
				}
			}
		}

		fputcsv($fp, $tableau);

		$req_export .= " $req $order " ;
		//echo $req_export ;
		$res =  mysqli_query($cnx, $req_export) ;	
		while ( $enr = mysqli_fetch_assoc($res) )
		{
			$tableau = array() ;
			while ( list($key, $val) = each($enr) )
			{
				if	(
						($key == "naissance")
						OR ($key == "ident_date")
						OR ($key == "date_inscrip")
						OR ($key == "date_maj")
						OR ($key == "date_imput")
						OR ($key == "date_maj_imput")
						OR ($key == "date_maj_etat")
					)
				{
					$tableau[] = mysql2date($val) ;
				}
				else if (
						($key == "pays_naissance")
						OR ($key == "pays_nationalite")
						OR ($key == "pays_residence")
					)
				{
					$tableau[] = $statiquePays[$val] ;
				}
				else if ( ($key == "situation_actu") )
				{
					$tableau[] = $SITUATION[$val] ;
				}
				else if ( ($key == "ident_nature") )
				{
					$tableau[] = $IDENTITE[$val] ;
				}
				else if ( ($key == "etat_dossier") )
				{
					if ( $enr["id_imputation"]!="" ) {
						$tableau[] = $ETAT_DOSSIER[$val] ;
					}
					else {
						$tableau[] = "" ;
					}
				}
				/*
				else if ( ($key == "") )
				{
				}
				*/
				else
				{
					$tableau[] = trim($val) ;
				}
			}
			fputcsv($fp, $tableau);
		}
		rewind($fp);
		if ( isset($_SESSION["filtres"]["recherche"]["latin1"]) AND ($_SESSION["filtres"]["recherche"]["latin1"] = "latin1") )
		{
			header("Content-type: text/csv; charset=iso-8859-1");
		}
		else
		{
			header("Content-type: text/csv; charset=UTF-8");
		}
		header("Content-Disposition: attachment; filename=\"".$fichier."\";" );
		//echo $req_export . "\n<br />\n<br />";
		echo stream_get_contents($fp);
		fclose($fp) ;
	}
	//
	//
	//
	else
	{
		echo "Erreur" ;
	}
	
	unset($_SESSION["filtres"]["recherche"]["submit"]) ;
	unset($_SESSION["filtres"]["recherche"]["ok"]) ;
}
else
{
	echo $avant_menu ;
	require_once("inc_menu.php");
	echo $apres_menu ;
	formulaire_recherche($cnx) ;
	echo $end ;
}
/*
echo "<pre>" ;
print_r($_SESSION["filtres"]["recherche"]) ;
echo "</pre>" ;
*/
deconnecter($cnx) ;
?>
