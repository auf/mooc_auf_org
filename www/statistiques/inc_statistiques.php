<?php

function sessionsActives()
{
	$req  = "SELECT groupe, niveau, id_session, intitule, intit_ses
		FROM atelier, session
		WHERE atelier.id_atelier=session.ref_atelier
		AND session.etat='Active'
		ORDER BY groupe, niveau, intitule" ;
	$resultat = mysqli_query($cnx, $req) ;
	while ( $ligne = mysqli_fetch_assoc($cnx, $resultat) ) {
		$sessionsActives[$ligne["id_session"]]
			= array($ligne["intitule"], $ligne["intit_ses"],
				$ligne["groupe"], $ligne["niveau"]) ;
	}
	return $sessionsActives ;
}

function sessionsAnnee($cnx, $annee)
{
	$req  = "SELECT groupe, niveau, id_session, intitule, intit_ses
		FROM atelier, session
		WHERE atelier.id_atelier=session.ref_atelier
		AND session.annee=$annee
		ORDER BY groupe, niveau, intitule" ;
	$resultat = mysqli_query($cnx, $req) ;
	while ( $ligne = mysqli_fetch_assoc($resultat) ) {
		$sessions[$ligne["id_session"]]
			= array($ligne["intitule"], $ligne["intit_ses"],
				$ligne["groupe"], $ligne["niveau"]) ;
	}
	return $sessions ;
}

function etatsSession($idSession, $etat_dossier, $intitule="", $intit_ses="")
{
	echo "<tr>\n" ;
	if ( $intitule != "" ) {
		echo "<th>$intitule</th>\n " ;
		echo "<td>$intit_ses</td>\n" ;
	}
	$req  = "SELECT etat_dossier, COUNT(etat_dossier) AS N " ;
	$req .= "FROM dossier, candidat " ;
	$req .= "WHERE id_session=$idSession " ;
	$req .= "AND dossier.id_candidat=candidat.id_candidat " ;
	$req .= "GROUP BY dossier.etat_dossier" ;
	$resultat = mysqli_query($cnx, $req) ;
	while ( $ligne = mysqli_fetch_assoc($cnx, $resultat) ) {
		$etatDossier[$ligne["etat_dossier"]] = $ligne["N"] ;
	}
	$sousTotal = 0 ;
	foreach($etat_dossier as $etat) {
		if ( !isset($etatDossier[$etat]) ) {
			echo "<td>0</td>\n" ;
		}
		else {
			echo "<td>".$etatDossier[$etat]."</td>\n" ;
			$sousTotal += $etatDossier[$etat] ;
			$totalEtat[$etat] += $etatDossier[$etat] ;
		}
	}
	echo "<th class='total'>$sousTotal</th>\n" ;
	echo "</tr>\n" ;
	$total += $sousTotal ;

}

/**
 * Comptage des valeurs d'une colonne de 'candidat' pour les candidats
 * ayant un dossier dans les sessions en cours.
 * Pour la session $id_session si $id_session!=0, pour toutes les
 * 
 * @param   
 * @param   Nom du champ de la table candidat
 * @param   "" ou "PAYS" ou "REGION"
 * @param   id_session
 * @return  hash : Valeur => N occurences
 */
function statCandidat($cnx, $colonne, $id_session=0, $jointure="")
{
	$liste = array() ;

	if ( $jointure == "PAYS" ) {
		$req = "SELECT $colonne,
			(SELECT nom FROM ref_pays WHERE code=dossier.$colonne) AS nom_pays,
			COUNT($colonne) AS N, id_imputation " ;
	}
	else if ( $jointure == "REGION" ) {
		$req = "SELECT $colonne,
			(SELECT ref_region.nom FROM ref_pays, ref_region WHERE ref_pays.region=ref_region.id AND ref_pays.code=dossier.$colonne)
			AS nom_region,
			COUNT($colonne) AS N, id_imputation " ;
	}
	else {
		$req = "SELECT $colonne, COUNT($colonne) AS N, id_imputation " ;
	}
	$req .= " FROM session, dossier
		LEFT JOIN ref_pays ON dossier.pays_residence=ref_pays.code
		LEFT JOIN ref_region ON ref_pays.region=ref_region.id
		LEFT JOIN imputations ON dossier.id_dossier=imputations.ref_dossier
		WHERE dossier.ref_session=session.id_session " ;

	if ( $id_session != 0 ) {
		$req .= "AND dossier.ref_session IN ($id_session) " ;
	}
	else {
		if ( isset($_SESSION["filtres"]["statistiques"]["annee"]) ) {
			$req .= " AND session.annee=".$_SESSION["filtres"]["statistiques"]["annee"] ." " ;
		}
		if ( intval($_SESSION["id"]) > 3 ) {
			$req .= " AND session.id_session IN (".$_SESSION["liste_toutes_promotions"].") " ;
		}
	}
	// Etat
	if ( isset($_SESSION["filtres"]["statistiques"]["etat"]) AND ($_SESSION["filtres"]["statistiques"]["etat"] != "") ) {
		if ( $_SESSION["filtres"]["statistiques"]["etat"] == "I" ) {
			$req .= " AND id_imputation IS NOT NULL " ;
		}
		else if ( $_SESSION["filtres"]["statistiques"]["etat"] == "P" ) {
			$req .= " AND id_imputation IS NULL " ;
		}
	}
	// Résultat (etat_dossier)
	if ( isset($_SESSION["filtres"]["statistiques"]["etat_dossier"]) AND ($_SESSION["filtres"]["statistiques"]["etat_dossier"]) != "") {
		if ( $_SESSION["filtres"]["statistiques"]["etat_dossier"] == "0" ) {
			$req .= " AND etat_dossier='".$_SESSION["filtres"]["statistiques"]["etat_dossier"]."'" ;
			$req .= " AND id_imputation != ''" ;
		}
		else {
			$req .= " AND etat_dossier='".$_SESSION["filtres"]["statistiques"]["etat_dossier"]."'" ;
		}
	}


	if ( isset($_SESSION["filtres"]["statistiques"]["region"] ) AND ($_SESSION["filtres"]["statistiques"]["region"] != "") ) {
		$req .= " AND ref_pays.region='". $_SESSION["filtres"]["statistiques"]["region"] ."' " ;
	}
	if ( isset($_SESSION["filtres"]["statistiques"]["pays"] ) AND ($_SESSION["filtres"]["statistiques"]["pays"] != "") ) {
		$req .= " AND candidat.pays='". $_SESSION["filtres"]["statistiques"]["pays"] ."' " ;
	}
	if ( isset($_SESSION["filtres"]["statistiques"]["limiter"]) AND ($_SESSION["filtres"]["statistiques"]["limiter"] == "diplomes") )
	{
		$req .= " AND dossier.diplome='Oui' AND ref_ancien!='0' " ;
	}
	if ( isset($_SESSION["filtres"]["statistiques"]["limiter"]) AND ($_SESSION["filtres"]["statistiques"]["limiter"] == "imputes") )
	{
		$req .= " AND id_imputation IS NOT NULL " ;
	}

	if ( $jointure == "REGION" ) {
		$req .= "GROUP BY nom_region" ;
	}
	else {
		$req .= "GROUP BY $colonne" ;
	}

	//echo $req ;
	$resultat = mysqli_query($cnx, $req) ;
	while ( $ligne = mysqli_fetch_assoc($resultat) ) {
		if ( $jointure == "PAYS" ) {
			$liste[$ligne["nom_pays"]] = $ligne["N"] ;
		}
		else if ( $jointure == "REGION" ) {
			$liste[$ligne["nom_region"]] = $ligne["N"] ;
		}
		else {
			$liste[$ligne["$colonne"]] = $ligne["N"] ;
		}
	}
	return $liste;
}

function agesCandidat($cnx, $id_session=0)
{
	$liste = array() ;
	$req  = "SELECT ((DATEDIFF(date_deb, naissance)) DIV 365.25) AS age, id_imputation
		FROM session, dossier
			LEFT JOIN ref_pays ON dossier.pays_residence=ref_pays.code
			LEFT JOIN ref_region ON ref_pays.region=ref_region.id
		LEFT JOIN imputations ON dossier.id_dossier=imputations.ref_dossier
		WHERE dossier.ref_session=session.id_session " ;
	if ( $id_session != 0 ) {
		$req .= " AND dossier.ref_session=$id_session " ;
	}
	else {
		if ( isset($_SESSION["filtres"]["statistiques"]["annee"]) ) {
			$req .= " AND session.annee=".$_SESSION["filtres"]["statistiques"]["annee"] ." " ;
		}
		if ( intval($_SESSION["id"]) > 3 ) {
			$req .= " AND session.id_session IN (".$_SESSION["liste_toutes_promotions"].") " ;
		}
	}
	if ( isset($_SESSION["filtres"]["statistiques"]["region"] ) AND ($_SESSION["filtres"]["statistiques"]["region"] != "") ) {
		$req .= " AND ref_pays.region='". $_SESSION["filtres"]["statistiques"]["region"] ."' " ;
	}
	if ( isset($_SESSION["filtres"]["statistiques"]["pays"]) AND ($_SESSION["filtres"]["statistiques"]["pays"] != "") ) {
		$req .= " AND candidat.pays='". $_SESSION["filtres"]["statistiques"]["pays"]."' " ;
	}
	// Etat
	if ( isset($_SESSION["filtres"]["statistiques"]["etat"]) AND ($_SESSION["filtres"]["statistiques"]["etat"] != "") ) {
		if ( $_SESSION["filtres"]["statistiques"]["etat"] == "I" ) {
			$req .= " AND id_imputation IS NOT NULL " ;
		}
		else if ( $_SESSION["filtres"]["statistiques"]["etat"] == "P" ) {
			$req .= " AND id_imputation IS NULL " ;
		}
	}
	// Résultat (etat_dossier)
	if ( isset($_SESSION["filtres"]["statistiques"]["etat_dossier"]) AND ($_SESSION["filtres"]["statistiques"]["etat_dossier"]) != "") {
		if ( $_SESSION["filtres"]["statistiques"]["etat_dossier"] == "0" ) {
			$req .= " AND etat_dossier='".$_SESSION["filtres"]["statistiques"]["etat_dossier"]."'" ;
			$req .= " AND id_imputation != ''" ;
		}
		else {
			$req .= " AND etat_dossier='".$_SESSION["filtres"]["statistiques"]["etat_dossier"]."'" ;
		}
	}
	$req .= "ORDER BY age" ;

	$resultat = mysqli_query($cnx, $req) ;
	while ( $ligne = mysqli_fetch_assoc($resultat) ) {
		if ( isset($liste[$ligne["age"]]) ) {
			$liste[$ligne["age"]] += 1 ;
	  }
	  else {
			$liste[$ligne["age"]] = 1 ;
		}
	}
	return $liste;
}

function tranchesAges($ages) {
	$tranches[""] = 0 ; // Pour l'affichage
	$tranches["Age &lt; 20"] = 0 ; // Pour l'affichage
	while ( list($key, $val) = @each($ages) ) {
		if ( is_numeric($key) ) {
			if ( intval($key) < 20 ) {
				$tranche = "Age &lt; 20" ;
			}
			else if ( (intval($key) >= 20) AND (intval($key)<25) ) {
				$tranche = "20 &le; Age &lt; 25" ;
			}
			else if ( (intval($key) >= 25) AND (intval($key)<30) ) {
				$tranche = "25 &le; Age &lt; 30" ;
			}
			else if ( (intval($key) >= 30) AND (intval($key)<35) ) {
				$tranche = "30 &le; Age &lt; 35" ;
			}
			else if ( (intval($key) >= 35) AND (intval($key)<40) ) {
				$tranche = "35 &le; Age &lt; 40" ;
			}
			else if ( (intval($key) >= 40) AND (intval($key)<50) ) {
				$tranche = "40 &le; Age &lt; 50" ;
			}
			else {
				$tranche = "Age &ge; 50" ;
			}
			if ( isset($tranches[$tranche]) ) {
				$tranches[$tranche] += $val ; 
			}
			else {
				$tranches[$tranche] = $val ;
			}
		}
		else {
			if ( isset($tranches[""]) ) {
				$tranches[""] += $val ; 
			}
			else {
				$tranches[""] = $val ;
			}
		}
	}
	return $tranches;
}


function fusionStats($tab_c, $tab_autresc)
{
	while ( list($key, $val) = each($tab_autresc) ) {
		if ( array_key_exists ($key, $tab_c) ) {
			$tab_c[$key] += $val ;
		}
		else {
			$tab_c[$key] = $val ;
		}
	}
//	ksort($tab_c) ;
	return $tab_c ;
}

function afficheStats($caption, $tableau) 
{
	$total = 0 ;
	while ( list($key, $val) = @each($tableau) ) {
		$total += $val ;
	}

	@reset($tableau) ;

	echo "<div class='conteneur'>\n" ;
	echo "<table class='stats'>\n" ;
	echo "<caption>$caption</caption>\n" ;
	echo "<tbody>\n" ;
	while ( list($key, $val) = @each($tableau) ) {
		if ( $val != 0 ) {
			echo "<tr>\n" ;
			echo "<th>$key</th>\n" ;
			echo "<td>$val</td>\n" ;
			echo "<td>" ;
			if ( $total != 0 ) {
				printf("%.2f", (($val/$total)*100)) ;
			}
			else {
				echo "0.00" ;
			}
			echo "&nbsp;%</td>\n" ;
			echo "</tr>\n" ;
		}
	}
	echo "</tbody>\n" ;
	echo "<tfoot>\n" ;
	echo "<tr>\n" ;
	echo "<th>Total</th>\n" ;
	echo "<th>$total</th>\n" ;
	echo "<th class='nob'></th>\n" ;
	echo "</tr>\n" ;
	echo "</tfoot>\n" ;
	echo "</table>\n" ;
	echo "</div>\n" ;
}

function statistiques($cnx, $caption, $colonne)
{
	$c = statCandidat($cnx, $colonne) ;
	$ac = statAutrescandidats($cnx, $colonne) ;
	echo "<tr>\n" ;
	echo "<td>\n" ;
	afficheStats($caption, $c) ;
	echo "</td>\n" ;
	echo "<td>\n" ;
	afficheStats($caption, $ac) ;
	echo "</td>\n" ;
	echo "<td>\n" ;
	afficheStats($caption, fusionStats($c, $ac)) ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
}
function statistiq($cnx, $caption, $colonne)
{
	$c = statCandidat($cnx, $colonne) ;
	echo "<tr>\n" ;
	echo "<td>\n" ;
	afficheStats($caption, $c) ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
}



// $id = 0 : tous
// $id = 
function afficheDetails($cnx, $id=0)
{
	global $TAB_BUREAUX;
	global $TAB_PAYS;
	echo "<br />" ;
	echo "<table class='pres'>\n<tbody>\n<tr>\n<td>" ;
			afficheStats("Genre", statCandidat($cnx, "genre", $id)) ;
		echo "</td><td>" ;
			afficheStats("Age", tranchesAges(agesCandidat($cnx, $id))) ;
		echo "</td><td>" ;
			$c = statCandidat($cnx, "situation_actu", $id) ;
			@natsort($c) ;
			afficheStats("Situation actuelle", @array_reverse($c)) ;
		echo "</td><td>" ;
			$c = statCandidat($cnx, "ident_nature", $id) ;
			@natsort($c) ;
			afficheStats("Pièce d'identité", @array_reverse($c)) ;
	echo "</td>\n</tr>\n</tbody>\n</table>\n\n" ;
	
	
	echo "<table class='pres'>\n<tbody>\n<tr>\n<td>" ;
			$c = statCandidat($cnx, "pays_residence", $id, "REGION") ;
			@natsort($c) ;
			afficheStats("Région (pays de résidence)", array_reverse($c)) ;
		echo "</td><td>" ;
			$c = statCandidat($cnx, "pays_naissance", $id, "REGION") ;
			@natsort($c) ;
			afficheStats("Région (pays de naissance)", array_reverse($c)) ;
		echo "</td><td>" ;
			$c = statCandidat($cnx, "pays_nationalite", $id, "REGION") ;
			@natsort($c) ;
			afficheStats("Région (Nationalité)", array_reverse($c)) ;
	echo "</td>\n</tr>\n" ;
	echo "<tr><td colspan='3'>&nbsp;</td></tr>\n" ;
	echo "<tr>\n<td>" ;
			$c = statCandidat($cnx, "pays_residence", $id, "PAYS") ;
			@natsort($c) ;
			afficheStats("Pays de résidence", @array_reverse($c)) ;
		echo "</td><td>" ;
			$c = statCandidat($cnx, "pays_naissance", $id, "PAYS") ;
			@natsort($c) ;
			afficheStats("Pays de naissance", @array_reverse($c)) ;
		echo "</td><td>" ;
			$c = statCandidat($cnx, "pays_nationalite", $id, "PAYS") ;
			@natsort($c) ;
			afficheStats("Nationalité", @array_reverse($c)) ;
	echo "</td>\n</tr>\n</tbody>\n</table>\n\n" ;

	echo "<br />" ;

	echo "<table class='pres'>\n<tbody>\n<tr>\n<td>" ;
			afficheStats("Lieu de paiement", statCandidat($cnx, "lieu_paiement", $id)) ;
		echo "</td><td>" ;
			afficheStats("Lieu d'examen", statCandidat($cnx, "lieu_examen", $id)) ;
	echo "</td>\n</tr>\n</tbody>\n</table>\n\n" ;
}





function filtreStatsAnnee($cnx)
{
	echo "<tr>\n" ;
	echo "<th rowspan='5'>Limiter à : </th>\n" ;
	echo "<th>Année : </th>\n" ;
	echo "<td><select name='stats_annee'>\n" ;
	$req = "SELECT DISTINCT(annee) FROM session " ;
	if ( intval($_SESSION["id"]) > 3 ) {
		$req .= " WHERE id_session IN
			(".$_SESSION["liste_toutes_promotions"].")" ;
	}
	$req .= " ORDER BY annee DESC" ;
//	echo $req ;
	$res = mysqli_query($cnx, $req) ;
	$i = 0 ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		// Pour fixer l'année à la derniere annee si elle n'est pas fixee
		if ( $i == 0 ) {
			$derniere_annee = $enr["annee"] ;
		}
		echo "<option value='".$enr["annee"]."'" ;
		if ( isset($_SESSION["filtres"]["statistiques"]["annee"]) AND ($_SESSION["filtres"]["statistiques"]["annee"] == $enr["annee"]) ) {
			echo " selected='selected'" ;
		}
		echo ">".$enr["annee"]."</option>" ;
		$i++ ;
	}
	echo "</select></td>\n" ;
	echo "</tr>\n" ;
	if ( !isset($_SESSION["filtres"]["statistiques"]["annee"]) )
	{
		$_SESSION["filtres"]["statistiques"]["annee"] = $derniere_annee ;
	}
}

function filtreStatsPays($cnx)
{
	echo "<tr>\n" ;
	echo "<th>Pays de résidence : </th>\n" ;
	echo "<td>" ;
	echo selectPays($cnx, "stats_pays",
		( isset($_SESSION["filtres"]["statistiques"]["pays"]) ? $_SESSION["filtres"]["statistiques"]["pays"] : "" )
		) ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
}

function filtreStatsRegion($cnx)
{
	echo "<tr>\n" ;
	echo "<th>Région de résidence : </th>\n" ;
	echo "<td>" ;
	echo selectRegion($cnx, "stats_region",
		( isset($_SESSION["filtres"]["statistiques"]["region"]) ? $_SESSION["filtres"]["statistiques"]["region"] : "" )
		) ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
}

include_once("inc_etat_inscrit.php") ;
function filtreStatsEtat()
{
	echo "<tr>\n" ;
	echo "<th>État : </th>\n" ;
	echo "<td>" ;
	if ( isset($_SESSION["filtres"]["statistiques"]["etat"]) ) {
		liste_etat_inscrit("stats_etat", $_SESSION["filtres"]["statistiques"]["etat"], TRUE, TRUE) ;
	}
	else {
		liste_etat_inscrit("stats_etat", "", TRUE, TRUE) ;
	}
	echo "</td>\n" ;
	echo "</tr>\n" ;
}

include_once("inc_etat_dossier.php") ;
function filtreStatsResultat()
{
	echo "<tr>\n" ;
	echo "<th>Résultat : </th>\n" ;
	echo "<td>" ;
	if ( isset($_SESSION["filtres"]["statistiques"]["etat_dossier"]) ) {
		liste_etats("stats_etat_dossier", $_SESSION["filtres"]["statistiques"]["etat_dossier"], TRUE, TRUE, TRUE) ;
	}
	else {
		liste_etats("stats_etat_dossier", "", TRUE, TRUE, TRUE) ;
	}
	echo " &nbsp; <span class='s'>(Implique État = Inscrit.)</span>" ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
}

?>
