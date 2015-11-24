<?php
include("inc_session.php") ;

// S'il s'agit d'un sélectionneur avec une seule promotion,
// redirection sur la page suivante
if ( isset($_SESSION["tableau_toutes_promotions"]) AND (count($_SESSION["tableau_toutes_promotions"]) == 1) ) {
	header("Location: promotion.php?promotion="
		.$_SESSION["tableau_toutes_promotions"][0]) ;
	exit ;
}

include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_html.php") ;
$titre = "Messagerie <span>(inscrits)</span>" ;
echo $dtd1 ;
echo "<title>".strip_tags($titre)."</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;


if ( intval($_SESSION["id"]) < 3 )
{
	require_once("inc_groupe.php");

	$req = "SELECT MAX(annee) FROM session" ;
	$res = mysqli_query($cnx, $req) ;
	$row = mysqli_fetch_row($res) ;
	$derniere_annee = $row[0] ;

	if ( !isset($_SESSION["filtres"]["messagerie"]["annee"]) ) {
		$_SESSION["filtres"]["messagerie"]["annee"] = $derniere_annee ;
	}

	echo "<form method='post' action='criteres.php'>\n" ;
	echo "<table class='formulaire'>\n<tbody>\n" ;
	echo "<tr>\n" ;
	echo "<th>Année&nbsp;: </th>\n" ;
	echo "<td><select name='m_annee'>\n" ;
	$req = "SELECT DISTINCT(annee) FROM session ORDER BY annee DESC" ;
	$res = mysqli_query($cnx, $req) ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
	    echo "<option value='".$enr["annee"]."'" ;
	    if ( $_SESSION["filtres"]["messagerie"]["annee"] == $enr["annee"] ) {
	        echo " selected='selected'" ;
	    }
	    echo ">".$enr["annee"]."</option>" ;
	}
	echo "</select></td>\n" ;
	echo "</tr>\n" ;
	echo "<tr>\n" ;
	echo "<th>Domaine : </th>\n" ;
	echo "<td>" ;
	echo select_groupe(
		( isset($_SESSION["filtres"]["messagerie"]["groupe"]) ? $_SESSION["filtres"]["messagerie"]["groupe"] : "" )
		) ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
	echo "<tr>\n" ;
	echo "<td colspan='2'><div class='c'>" ;
	echo "<input class='b' type='submit' value='Actualiser' /></div></td>\n" ;
	echo "</tr>\n" ;
	echo "</tbody>\n</table>\n" ;
	echo "</form>" ;
	echo "<br />" ;
}


if ( intval($_SESSION["id"]) < 3 )
{
	$requete = "SELECT id_session, groupe, intitule, intit_ses, annee
		FROM session, atelier WHERE
		atelier.id_atelier=session.ref_atelier
		AND session.annee=".$_SESSION["filtres"]["messagerie"]["annee"]." ";
	if ( isset($_SESSION["filtres"]["messagerie"]["groupe"]) AND ($_SESSION["filtres"]["messagerie"]["groupe"] != "") ) {
		$requete .= " AND groupe='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["messagerie"]["groupe"])."' " ;
	}
	$requete .= " ORDER BY annee DESC, groupe, niveau, intitule" ;
}
else
{
	$requete = "SELECT id_session, groupe, intitule, intit_ses, annee
		FROM session , atelier , atxsel
		WHERE session.ref_atelier=atelier.id_atelier
		AND session.annee >= 2006
		AND atelier.id_atelier=atxsel.id_atelier
		AND atxsel.id_sel='".$_SESSION["id"]."'
		ORDER BY annee DESC, groupe, niveau, intitule" ;
}
$resultat = mysqli_query($cnx, $requete) ;

if ( mysqli_num_rows($resultat) == 0 )
	echo "<p class='erreur'>Pas d'inscription active actuellement !</p>" ;
else
{
	echo "<table class='tableau'>\n" ;
	echo "<thead>\n<tr>\n" ;
	echo "\t<th colspan='3'>Courriels<br />envoyés</th>\n" ;
	echo "\t<th rowspan='2'>Formation - inscription" ;
	echo "<p class='normal' style='color: #000;'>Cliquez sur une inscription pour<br />" ;
	echo "afficher la liste des courriels envoyés<br />" ;
	echo "ou pour envoyer un nouveau courriel.</p>" ;
	echo "</th>\n" ;
	echo "</tr>\n" ;
	echo "<tr>\n" ;
	echo "<th class='help'><img width='22' height='135' src='/img/utilisateurs/Responsables.gif' alt='Responsables' title='Responsables' /></th>\n" ;
	echo "<th class='help'><img width='22' height='135' src='/img/utilisateurs/GestionnairesAUF.gif' alt='Bourses' title='mooc-clom-scp@auf.org ou mooc-clom-bac@auf.org ou mooc-clom-bao@auf.org' /></th>\n" ;
	echo "<th class='help'><img width='22' height='135' src='/img/utilisateurs/Administrateur.gif' alt='Administrateur' title='Administrateur mooc-clom@auf.org' /></th>\n" ;
	echo "</tr>\n" ;
	echo "</thead>\n<tbody>\n" ;

	$i = 1 ;
	$groupe = "" ;
	$annee = "" ;
	while ( $ligne = mysqli_fetch_assoc($resultat) )
	{
		if ( $annee != $ligne["annee"] ) {
			$annee = $ligne["annee"] ;
			echo "<tr><td colspan='5' class='r annee'>" ;
			echo "<b style='font-size: 100%;'>$annee</b></td></tr>" ;
		}
		if ( intval($_SESSION["id"]) < 3 ) {
			if ( $groupe != $ligne["groupe"] ) {
				$groupe = $ligne["groupe"] ;
				echo "<tr><td colspan='5' class='r' style='background: #ccc'>" ;
				echo "<b style='font-size: 100%;'>$groupe</b></td></tr>" ;
			}
		}
		echo "<tr>" ;

		$req = "SELECT expediteur, COUNT(expediteur) AS N FROM courriels
			WHERE ref_session=".$ligne["id_session"]."
			GROUP BY expediteur" ;
		$res = mysqli_query($cnx, $req) ;
		$selectionneurs = 0 ;
		$bourses = 0 ;
		$administrateur = 0 ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
			if ( $enr["expediteur"] == "mooc-clom@auf.org" ) {
				$administrateur += intval($enr["N"]) ;
			}
			else if ( 
				( $enr["expediteur"] == "mooc-clom-scp@auf.org" )
				OR ( $enr["expediteur"] == "mooc-clom-bac@auf.org" )
				OR ( $enr["expediteur"] == "mooc-clom-bao@auf.org" )
			)
			{
				$bourses += intval($enr["N"]) ;
			}
			else {
				$selectionneurs += intval($enr["N"]) ;
			}
		}
		if ( $selectionneurs == 0 ) {
			echo "<td class='r'>0</td>\n" ;
		}
		else {
			echo "<td class='r'><strong>$selectionneurs</strong></td>\n" ;
		}
		if ( $bourses == 0 ) {
			echo "<td class='r'>0</td>\n" ;
		}
		else {
			echo "<td class='r'><strong>$bourses</strong></td>\n" ;
		}
		if ( $administrateur == 0 ) {
			echo "<td class='r'>0</td>\n" ;
		}
		else {
			echo "<td class='r'><strong>$administrateur</strong></td>\n" ;
		}

		echo "<td><a class='bl' href='promotion.php?promotion=".$ligne["id_session"]."'>" ;
		echo $ligne["intitule"] . " - " . $ligne["intit_ses"] ;
		echo "</a></td>\n" ;

//		echo "<td><a href='session.php?promotion=".$ligne["id_session"]."'>" ;

		echo "</tr>\n" ;
		$i++ ;
	}
	echo "</tbody>\n</table>\n" ;
}

echo $end ;

deconnecter($cnx) ;
?>
