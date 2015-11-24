<?
include("inc_session.php") ;

// Redirection vers l'unique promotion d'un selectionneur
/*
if ( count($_SESSION["tableau_promotions"]) == 1 ) {
	header("Location: /inscrits/inscrits.php?id_session="
		.$_SESSION["tableau_promotions"][0]) ;
	exit ;
}
*/

include("inc_html.php") ;

$titre = "Gestion des inscrits" ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;


include("inc_mysqli.php") ;
$cnx = connecter() ;                                                             


if ( intval($_SESSION["id"]) <= 3 )
{
	require_once("inc_groupe.php");

	echo "<form action='criteres.php' method='post'>\n" ;
	echo "<table class='formulaire'>\n" ;

    $req = "SELECT MAX(annee) FROM session" ;
    $res = mysqli_query($cnx, $req) ;
    $row = mysqli_fetch_row($res) ;
    $derniere_annee = $row[0] ;

    if ( !isset($_SESSION["filtres"]["inscrits"]["annee"]) ) {
        $_SESSION["filtres"]["inscrits"]["annee"] = $derniere_annee ;
    }

	echo "<tr>\n" ;
	echo "<th>Année&nbsp;: </th>\n" ;
	echo "<td><select name='c_annee'>\n" ;
	$req = "SELECT DISTINCT(annee) FROM session ORDER BY annee DESC" ;
	$res = mysqli_query($cnx, $req) ;
	    while ( $enr = mysqli_fetch_assoc($res) ) {
	    echo "<option value='".$enr["annee"]."'" ;
	    if ( $_SESSION["filtres"]["inscrits"]["annee"] == $enr["annee"] ) {
	        echo " selected='selected'" ;
	    }
	    echo ">".$enr["annee"]."</option>" ;
	}
	echo "</select></td>\n" ;
	echo "</tr>\n" ;
	echo "<tr>\n" ;
	echo "<th>Domaine : </th>\n" ;
	echo "<td>" ;
	if ( isset($_SESSION["filtres"]["inscrits"]["groupe"]) ) {
		echo select_groupe($_SESSION["filtres"]["inscrits"]["groupe"]) ;
	}
	else {
		echo select_groupe("") ;
	}
	echo "</td>\n" ;
	echo "</tr>\n" ;

	/*
	*/
	if ( ($_SESSION["id"] == "02") OR ($_SESSION["id"] == "03") )
	{
		require_once("inc_pays.php") ;
		echo "<tr>\n" ;
		echo "<th>Pays de résidence&nbsp;:\n" ;
		echo "<div style='font-size: smaller'>Limiter aux formations pour lesquelles il y a au moins un inscrit dans ce pays</div>" ;
		echo "</th>\n" ;
		echo "<td>" ;
		$req = "SELECT DISTINCT dossier.pays_residence, ref_pays.code AS code, ref_pays.nom AS nom FROM dossier
			LEFT JOIN ref_pays ON dossier.pays_residence=ref_pays.code
		    ORDER BY nom" ;
		echo selectPays($cnx, "c_pays", 
			( isset($_SESSION["filtres"]["inscrits"]["pays"]) ? $_SESSION["filtres"]["inscrits"]["pays"] : "" ),
			$req) ;
		echo "</td>\n" ;
		echo "</tr>\n" ;
	}

	echo "<tr>\n" ;
	echo "<td colspan='2'><div class='c'>" ;
	echo "<input class='b' type='submit' value='Actualiser' /></div></td>\n" ;
	echo "</tr>\n" ;
	echo "</table>\n" ;
	echo "</form>" ;
}


if	(
		( ($_SESSION["id"] == "02") OR ($_SESSION["id"] == "03") )
		AND ( !empty($_SESSION["filtres"]["inscrits"]["pays"]) ) 
	)
{
	$requete = "SELECT session.*,
		(SELECT COUNT(*) FROM dossier WHERE ref_session=id_session) AS T,
		(SELECT COUNT(*) FROM dossier LEFT JOIN imputations ON ref_dossier=id_dossier
		WHERE ref_session=id_session AND ref_dossier IS NULL) AS P,
		(SELECT COUNT(*) FROM dossier LEFT JOIN imputations ON ref_dossier=id_dossier
		WHERE ref_session=id_session AND ref_dossier IS NOT NULL) AS I
		FROM dossier, session, atelier WHERE
		atelier.id_atelier=session.ref_atelier
		AND session.id_session=dossier.ref_session
		AND dossier.pays_residence='".$_SESSION["filtres"]["inscrits"]["pays"]."'
		AND annee='".$_SESSION["filtres"]["inscrits"]["annee"]."' " ;
	if ( $_SESSION["filtres"]["inscrits"]["groupe"] != "" ) {
		$requete .= " AND groupe='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["inscrits"]["groupe"])."' " ;
	}
	$requete .= " ORDER BY annee DESC, groupe, intitule" ;
		//AND (evaluations='Oui' OR imputations='Oui')
}
else if ( intval($_SESSION["id"]) < 4 ) {
	$requete = "SELECT intitule, groupe, id_session, intit_ses, annee,
		(SELECT COUNT(*) FROM dossier WHERE ref_session=id_session) AS T,
		(SELECT COUNT(*) FROM dossier LEFT JOIN imputations ON ref_dossier=id_dossier
			WHERE ref_session=id_session AND ref_dossier IS NULL) AS P,
		(SELECT COUNT(*) FROM dossier LEFT JOIN imputations ON ref_dossier=id_dossier
			WHERE ref_session=id_session AND ref_dossier IS NOT NULL) AS I
		FROM session, atelier WHERE
		atelier.id_atelier=session.ref_atelier
		AND annee='".$_SESSION["filtres"]["inscrits"]["annee"]."' " ;
	if ( isset($_SESSION["filtres"]["inscrits"]["groupe"]) AND ($_SESSION["filtres"]["inscrits"]["groupe"] != "") ) {
		$requete .= " AND groupe='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["inscrits"]["groupe"])."' " ;
	}
	$requete .= " ORDER BY annee DESC, groupe, niveau, intitule" ;
		//AND (evaluations='Oui' OR imputations='Oui')
}
else {
	$requete = "SELECT intitule, groupe, id_session, intit_ses, annee,
		(SELECT COUNT(*) FROM dossier WHERE ref_session=id_session) AS P,
		(SELECT COUNT(*) FROM dossier LEFT JOIN imputations ON ref_dossier=id_dossier
			WHERE ref_session=id_session AND ref_dossier IS NOT NULL) AS I
		FROM session , atelier , atxsel
		WHERE session.ref_atelier=atelier.id_atelier
		AND atelier.id_atelier=atxsel.id_atelier
		AND atxsel.id_sel='".$_SESSION["id"]."'
		ORDER BY annee DESC, niveau, intitule" ; 
		//AND evaluations='Oui'
}
//echo $requete ;
$resultat = mysqli_query($cnx, $requete) ;

function debut_tableau($annee, $id)
{
	echo "<table class='tableau'>\n" ;
//	if ( $id < 4 ) {
		echo "<caption style='font-size: 1.4em;'>Année $annee</caption>\n" ;
//	}
	echo "<thead>\n<tr style='height: 7em;'>
	<th>Formation - Inscription</span></th>
	<th class='vertical'><div><span>Total</span></div></th>
	<th class='vertical'><div><span>Pré-inscrits</span></div></th>
	<th class='vertical'><div><span>Inscrits</span></div></th>
	</tr>\n</thead>\n<tbody>
" ;
} 
$fin_tableau = "</tbody>\n</table>\n" ;

if ( mysqli_num_rows($resultat) == 0 )
	if ( !empty($_SESSION["filtres"]["inscrits"]["pays"]) ) {
		echo "<p class='c'>Aucun candidat pour ce pays (de résidence).</p>" ;
	}
	else {
		echo "<p class='erreur c'>Aucune inscription avec des inscrits actuellement !</p>" ;
	}
else
{
	$i = 1 ;
	$etat0 = TRUE ;
	$annee_precedente = "aucune" ;
	$groupe = "" ;
	while ( $ligne = mysqli_fetch_assoc($resultat) )
	{
		if ( $ligne["annee"] != $annee_precedente ) {
			if ( !$etat0 ) {
				echo $fin_tableau ;
				$etat0 = FALSE ;
			}
			debut_tableau($ligne["annee"], intval($_SESSION["id"]) ) ;
			$annee_precedente = $ligne["annee"] ;
		}

		if ( intval($_SESSION["id"]) < 4 ) {
			if ( $groupe != $ligne["groupe"] ) {
				$groupe = $ligne["groupe"] ;
				echo "<tr class='groupe'><td colspan='4'>$groupe</td></tr>\n" ;
			}
		}
		echo "<tr>" ;
		echo "<td><a href='/inscrits/inscrits.php?id_session=" ;
		echo $ligne["id_session"] ;
		echo "' class='bl'>" ;
		echo $ligne["intitule"] ;
		echo " - ".$ligne["intit_ses"]."</a></td>\n" ;
		echo "<td class='r'>".$ligne["T"]."</td>\n" ;
		echo "<td class='r'>".$ligne["P"]."</td>\n" ;
		echo "<td class='r'>".$ligne["I"]."</td>\n" ;
		echo "</tr>\n" ;
		$i++ ;
	}
	echo "</tbody>\n</table>\n" ;
}

echo $end ;
deconnecter($cnx) ;

?>
