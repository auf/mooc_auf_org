<?php
include("inc_session.php") ;
if ( intval($_SESSION["id"]) != 0 ) {
	header("Location: /bienvenue.php") ;
}

$titre = "Promotions" ;
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

?>
<p class='c'><strong><a
	href="promotion.php?operation=add">Nouvelle promotion</a></strong></p>

<?php

include("inc_date.php");

include("inc_mysqli.php");
$cnx = connecter() ;

$req = "SELECT annee, COUNT(annee) AS N FROM session
	GROUP BY annee ORDER BY annee DESC" ;
$res = mysqli_query($cnx, $req) ;
while ( $row = mysqli_fetch_assoc($res) ) {
	$nbPromosParAnnee[$row["annee"]] = $row["N"] ;
	$navPromos[] .= "<a href='#promos".$row["annee"]."'>".$row["annee"]."</a>" ;
}
$naviPromos = $navPromos[0] ;
for ( $i=1 ; $i < count($navPromos) ; $i++ )
{
	$naviPromos .= " <span class='sep'>-</span> " ;
	$naviPromos .=  $navPromos[$i] ;
}

$requete = "SELECT intitule, groupe, session.*
	FROM session, atelier
	WHERE atelier.id_atelier=session.id_atelier
	ORDER BY annee DESC, groupe, niveau, intitule" ;
$result = mysqli_query($cnx, $requete);

function debutTableau($annee, $nb, $naviPromos) {
	echo "\n\n\n\n\n" ;
	echo "<p class='c' id='promos".$annee."'>". $naviPromos ."</p>\n\n" ;
	echo "<table class='stats'>\n";
	echo "<thead>\n" ;
	echo "<tr>\n" ;
	echo "\t<th class='invisible'></th>\n";
	echo "\t<th><img src='/img/promotions/candidatures.png' width='22' height='126' alt='Candidatures' title='Candidatures' /></th>\n";
	echo "\t<th><img src='/img/promotions/evaluations.png' width='22' height='126' alt='Évaluations' title='Évaluations' /></th>\n";
	echo "\t<th><img src='/img/promotions/imputations.png' width='22' height='126' alt='Imputations' title='Imputations' /></th>\n";
	echo "\t<th colspan='2' style='font-size: 1.5em'>$nb promotions en $annee</th>\n";
	echo "</tr>\n" ;
	echo "</thead>\n" ;
	echo "<tbody>\n" ;
}
$fin_tableau = "</tbody>\n</table>\n" ;

$i=0 ;
$etat0 = TRUE ;
$annee_precedente = "aucune" ;
$groupe = "" ;
while($row=mysqli_fetch_assoc($result))
{
	if ( $row["annee"] != $annee_precedente ) {
		if ( !$etat0 ) {
			echo $fin_tableau ;
		}
		$etat0 = FALSE ;
		debutTableau($row["annee"],
			$nbPromosParAnnee[$row["annee"]],
			$naviPromos	) ;
		$annee_precedente = $row["annee"] ;
	}

	$lien = "?session=".$row["id_session"] ;

    if ( $groupe != $row["groupe"] ) {
        $groupe = $row["groupe"] ;
        echo "<tr>" ;
		echo "<td class='invisible'></td>" ;
		echo "<td style='background: #ccc' colspan='4' class='r'>" ;
        echo "<b style='font-size: 110%;'>$groupe</b></td>" ;
		echo "<td style='background: #ccc'>Dates d'examens</td>" ;
		echo "</tr>" ;
    }

	echo "<tr id='".$row["id_session"]."'>\n" ;

	// Suppression
	$req = "SELECT COUNT(id_dossier) FROM dossier, candidat
		WHERE id_session=".$row["id_session"] ;
	$req .= " AND dossier.id_candidat=candidat.id_candidat" ;
	$res = mysqli_query($cnx, $req) ;
	$ligne = mysqli_fetch_row($res) ;
	$N = $ligne[0] ;
	if ( intval($N) == 0 ) 
	{
		echo "<td><a href='supprimer_promotion.php".$lien."'>Supprimer</a></td>\n" ;
	}
	else {
		echo "<td class='invisible'></td>\n" ;
	}

	echo "\t<td class='c ".$row["candidatures"]."'>".$row["candidatures"]."</td>\n" ;
	echo "\t<td class='c ".$row["evaluations"]."'>".$row["evaluations"]."</td>\n" ;
	echo "\t<td class='c ".$row["imputations"]."'>".$row["imputations"]."</td>\n" ;

	echo "\t<td class='l'><strong style='font-size: 100%'>" ;
	echo "<a class='bl' href='promotion.php".$lien."&operation=modif'>" ;
	echo $row["intitule"]."</a></strong>\n" ;
	echo "<div style='font-size: 90%'>" ;
	echo "Promotion <strong>".$row["intit_ses"]."</strong>" ;
	echo " du ". mysql2datealpha($row["date_deb"]) ;
	echo " au ". mysql2datealpha($row["date_fin"])."</div>\n" ;
	echo $row["imputation"] ;
	echo " <span class='sep'>-</span> " ;
	echo $row["tarifP"] . "&euro;" ;
	echo " <span class='sep'>-</span> " ;
	echo $row["tarifA"] . "&euro;" ;
	echo "</td>\n" ;

	echo "\t<td class='c'>" ;
/*
	echo "<div style='font-size: smaller;'>" ;
	echo "<a href='/statistiques/promotion.php".$lien."'>Statistiques</a>" ;
	echo "</div>\n" ;
	if ( $row["evaluations"] == "Oui" ) {
		echo "<a href='/candidatures/candidatures.php?id_session=" ;
		echo $row["id_session"] . "'>Candidatures</a></div>\n" ;
	}
*/
	echo "<div style='text-align: right;'><a class='bl' href='/examens/examen.php$lien'>" ;
	echo "<strong>+</strong></a></div>" ;
	echo "</td>\n" ;


	echo "</tr>\n" ;

        $i++;
    }

    echo "</table>\n";
	
//--Fin affichage liste des sessions---//


deconnecter($cnx) ;
echo $end ;
?>	
