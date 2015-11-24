<?php
include_once("inc_session.php") ;
include_once("inc_pays.php") ;
include("inc_statistiques.php");

/*
if ( isset($_SESSION["tableau_toutes_promotions"]) AND (count($_SESSION["tableau_toutes_promotions"]) == 1) ) {
    header("Location: /statistiques/promotion.php?session="
        .$_SESSION["tableau_toutes_promotions"][0]) ;
    exit ;
}
*/

$titre = "Statistiques" ;
include("inc_html.php");
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
include("inc_menu.php");
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

include("inc_mysqli.php");
$cnx = connecter() ;

/*
if ( intval($_SESSION["id"]) < 4 ) {
	echo "<p class='c'><strong><a href='imputes.php'>Détails pour les candidats imputés</a></strong></p>" ;
}
*/



echo "<form method='post' action='criteres.php'>" ;
echo "<table class='formulaire'>\n<tbody>\n" ;
filtreStatsAnnee($cnx) ;
filtreStatsRegion($cnx) ;
filtreStatsPays($cnx) ;
filtreStatsEtat() ;
filtreStatsResultat() ;
echo "<tr>\n<th>Afficher&nbsp;:</th>\n<td colspan='2'>" ;
echo "<label class='bl formulaire'><input type='checkbox' name='stats_details' value='details'" ;
if ( isset($_SESSION["filtres"]["statistiques"]["details"]) AND ($_SESSION["filtres"]["statistiques"]["details"] == "details") ) {
	echo " checked='checked'" ;
}
echo " /> &nbsp; Statistiques sur les réponses</label></td></tr>" ;
echo "<tr>\n<td colspan='3'>" . FILTRE_BOUTON_LIEN . "</td>\n</tr>\n" ;
echo "</tbody>\n</table>\n" ;
echo "</form>" ;
echo "<br />" ;

if ( !isset($_SESSION["filtres"]["statistiques"]["annee"]) )
{
	$_SESSION["filtres"]["statistiques"]["annee"] = $_SESSION["derniere_annee"] ;
}






// Sessions
$req  = "SELECT groupe, id_session, intitule, intit_ses,
	(SELECT COUNT(*) FROM dossier WHERE ref_session=id_session) AS T,
	(SELECT COUNT(*) FROM dossier LEFT JOIN imputations ON ref_dossier=id_dossier
		WHERE ref_session=id_session AND ref_dossier IS NULL) AS P,
	(SELECT COUNT(*) FROM dossier LEFT JOIN imputations ON ref_dossier=id_dossier
		WHERE ref_session=id_session AND ref_dossier IS NOT NULL) AS I
	FROM atelier, session
	WHERE atelier.id_atelier=session.ref_atelier " ;

if ( !isset($_SESSION["filtres"]["statistiques"]["annee"]) ) {
	$req .= "AND session.annee=".$_SESSION["derniere_annee"] ;
}
else {
	$req .= "AND session.annee=".$_SESSION["filtres"]["statistiques"]["annee"] ;
}

if ( intval($_SESSION["id"]) > 4 ) {
	$req .=	" AND session.id_session IN (".$_SESSION["liste_toutes_promotions"].") " ;
}

$req .= " ORDER BY annee DESC, groupe, intitule" ;

$resultat = mysqli_query($cnx, $req) ;
while ( $ligne = mysqli_fetch_assoc($resultat) )
{
	$sessionsActives[$ligne["id_session"]] = array(
		$ligne["intitule"],		// 0
		$ligne["intit_ses"],	// 1
		$ligne["groupe"],		// 2
		$ligne["P"],			// 3
		$ligne["I"],			// 4
		$ligne["T"],			// 5
	) ;
}

// entete, total; corps; pied
$htmlStatsEntete = "" ;
$htmlStatsTotal = "" ;
$htmlStatsCorps = "" ;
$htmlStatsPied = "" ;

if ( count($sessionsActives) != 0 )
{
	// Entete
	$htmlStatsEntete .= "<table class='stats'>\n" ;
	$htmlStatsEntete .= "<thead>\n" ;
	$htmlStatsEntete .= "<tr>\n" ;
	$htmlStatsEntete .= "<th rowspan='2' class='vertical'><div><span>Total</span></div></th>\n" ;
	$htmlStatsEntete .= "<th rowspan='2' class='vertical'><div><span>Pré-inscrits</span></div></th>\n" ;
	$htmlStatsEntete .= "<th rowspan='2' class='vertical'><div><span>Inscrits</span></div></th>\n" ;
	$htmlStatsEntete .= "<th rowspan='2'>Formation (promotion)" ;
	$htmlStatsEntete .= "<p class='normal'>Les liens mènent aux statistiques de chaque promotion.</p></th>\n" ;
	$htmlStatsEntete .= "<th colspan='".intval(count($ETAT_DOSSIER)+1)."'>Résultat des inscrits</th>\n" ;
	$htmlStatsEntete .= "</tr>\n" ;
	$htmlStatsEntete .= "<tr style='height: 7em;'>\n" ;
	reset($ETAT_DOSSIER) ;
	while ( list($key, $val) = each($ETAT_DOSSIER) ) {
		$htmlStatsEntete .= "<th class='vertical ".$ETAT_DOSSIER_IMG_CLASS[$key]."'><div>".$val."</div></th>\n" ;
		$totalEtat[$key] = 0 ;
	}
	reset($ETAT_DOSSIER) ;
	$htmlStatsEntete .= "<th>Total</th>\n" ;
	$htmlStatsEntete .= "</tr>\n" ;
	$htmlStatsEntete .= "</thead>\n" ;
	$htmlStatsEntete .= "<tbody>\n" ;
	// Corps
	$total = 0 ;
	$nbPreInscrits = 0 ;
	$nbInscrits = 0 ;
	$nbTotal = 0 ;
	$groupe = "" ;
	foreach(array_keys($sessionsActives) as $idSession)
	{
		if ( ( intval($_SESSION["id"]) < 4 ) AND
			( $groupe != $sessionsActives[$idSession][2] ) )
		{
			$groupe = $sessionsActives[$idSession][2] ;
			$htmlStatsCorps .= "<tr><td style='background: #ccc;'" ;
			$htmlStatsCorps .= " colspan='".strval(count($ETAT_DOSSIER)+5)."' class='r'>" ;
			$htmlStatsCorps .= "<b style='font-size: 100%;'>$groupe</b></td></tr>" ;
		}
		$htmlStatsCorps .= "<tr>\n" ;
		$htmlStatsCorps .= "<td>".$sessionsActives[$idSession][5]."</td>\n" ;
		$htmlStatsCorps .= "<td>".$sessionsActives[$idSession][3]."</td>\n" ;
		$htmlStatsCorps .= "<td>".$sessionsActives[$idSession][4]."</td>\n" ;
		$nbPreInscrits  += $sessionsActives[$idSession][3] ;
		$nbInscrits  += $sessionsActives[$idSession][4] ;
		$nbTotal  += $sessionsActives[$idSession][5] ;


		$htmlStatsCorps .= "<th><a class='bl' href='promotion.php?session=$idSession'>" ;
		$htmlStatsCorps .= $sessionsActives[$idSession][0] ;
		$htmlStatsCorps .= " <span style='font-weight: normal'>(" ;
		$htmlStatsCorps .= $sessionsActives[$idSession][1].")</span></a></th>\n " ;

		$req  = "SELECT etat_dossier, COUNT(etat_dossier) AS N, id_imputation
			FROM dossier
				LEFT JOIN ref_pays ON dossier.pays_residence=ref_pays.code
				LEFT JOIN ref_region ON ref_pays.region=ref_region.id
			LEFT JOIN imputations ON imputations.ref_dossier=dossier.id_dossier
			WHERE ref_dossier IS NOT NULL
			AND ref_session=".$idSession." " ;

		if ( isset($_SESSION["filtres"]["statistiques"]["region"]) AND ($_SESSION["filtres"]["statistiques"]["region"]!="") )
		{
			$req .= "AND ref_pays.region='". $_SESSION["filtres"]["statistiques"]["region"] ."' " ;
		}
		if ( isset($_SESSION["filtres"]["statistiques"]["pays"]) AND ($_SESSION["filtres"]["statistiques"]["pays"]!="") )
		{
			$req .= "AND pays_residence='". $_SESSION["filtres"]["statistiques"]["pays"] ."' " ;
		}

		if ( isset($_SESSION["filtres"]["statistiques"]["etat"]) AND ($_SESSION["filtres"]["statistiques"]["etat"] != "") )
		{
			if ( $_SESSION["filtres"]["statistiques"]["etat"] == "P" ) {
				$req .= " AND id_imputation IS NULL " ;
			}
			else if ( $_SESSION["filtres"]["statistiques"]["etat"] == "I" ) {
				$req .= " AND id_imputation IS NOT NULL " ;
			}
			else {
			}
		}

		if ( isset($_SESSION["filtres"]["statistiques"]["resultat"]) AND ($_SESSION["filtres"]["statistiques"]["resultat"] != "") )
		{
			$req .= " AND id_imputation IS NOT NULL " ;
			$req .= " AND etat_dossier='".$_SESSION["filtres"]["statistiques"]["resultat"]."' " ;
		}

		$req .= " GROUP BY dossier.etat_dossier" ;
		$resultat = mysqli_query($cnx, $req) ;
		unset($etatDossier) ;
		while ( $ligne = mysqli_fetch_assoc($resultat) ) {
			$etatDossier[$ligne["etat_dossier"]] = $ligne["N"] ;
		}
		$sousTotal = 0 ;
		unset($accepte) ;
		while ( list($key, $val) = each($ETAT_DOSSIER) )
		{
			if ( !isset($etatDossier[$key]) ) {
				$htmlStatsCorps .= "<td class='".$ETAT_DOSSIER_IMG_CLASS[$key]."'>0</td>\n" ;
			}
			else {
				$htmlStatsCorps .= "<td class='".$ETAT_DOSSIER_IMG_CLASS[$key]."'>".$etatDossier[$key]."</td>\n" ;
				$sousTotal += $etatDossier[$key] ;
				$totalEtat[$key] += $etatDossier[$key] ;
			}
		}
		reset($ETAT_DOSSIER) ;
		$htmlStatsCorps .= "<th class='total'>$sousTotal</th>\n" ;
	
		$htmlStatsCorps .= "</tr>\n" ;
		$total += $sousTotal ;
	}
	// Total
	$htmlStatsTotal .= "<tr>\n" ;
	$htmlStatsTotal .= "<th class='total'>".$nbTotal."</th>\n" ;
	$htmlStatsTotal .= "<th class='total'>".$nbPreInscrits."</th>\n" ;
	$htmlStatsTotal .= "<th class='total'>".$nbInscrits."</th>\n" ;
	$htmlStatsTotal .= "<th style='text-align: center;'>Total</th>\n" ;
	while ( list($key, $val) = each($ETAT_DOSSIER) ) {
		$htmlStatsTotal .= "<th class='total'>".$totalEtat[$key]."</th>" ;
	}
	reset($ETAT_DOSSIER) ;
	$htmlStatsTotal .= "<th class='total'>".$total."</th>" ;
	$htmlStatsTotal .= "</tr>\n" ;
	// Pied
	$htmlStatsPied .= "</tbody>\n" ;
	$htmlStatsPied .= "</table>" ;
	$aucun = FALSE ;

	echo $htmlStatsEntete . $htmlStatsTotal . $htmlStatsCorps . $htmlStatsTotal . $htmlStatsPied ;
	
}
else {
	$aucun = TRUE ;
	echo "<p class='c'>Aucune promotion pour ces critères.</p>" ;
}





if ( intval($_SESSION["id"]) < 4 )
{
	$S = 0 ;
}
else {
	$S = $_SESSION["liste_promotions"] ;
}


if	(
		isset($_SESSION["filtres"]["statistiques"]["details"]) AND ($_SESSION["filtres"]["statistiques"]["details"] == "details")
		AND !$aucun
	)
{
	afficheDetails($cnx, $S) ;
}

deconnecter($cnx) ;
echo $end ;
?>
