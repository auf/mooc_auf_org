<?php
// ($local == TRUE) => lien sur chaque nombre
// ($local == FALSE) => Style différent ( class distant)
function dateExamenMooc($cnx, $date, $local=TRUE)
{
	$req  = "SELECT examens.*, atelier.intitule,
		session.annee, session.intit_ses, session.id_session," ;

	if ( isset($_SESSION["filtres"]["examens"]["lieu"]) AND ($_SESSION["filtres"]["examens"]["lieu"]!="") ) {
		$req .= "(SELECT COUNT(id_imputation) FROM imputations, dossier
			WHERE id_dossier=ref_dossier AND ref_session=session.id_session
			AND imputations.lieu_examen='".$_SESSION["filtres"]["examens"]["lieu"]."'
			) AS I " ;
	}
	else {
		$req .= "(SELECT COUNT(id_imputation) FROM imputations, dossier
			WHERE id_dossier=ref_dossier AND ref_session=session.id_session
			) AS I " ;
	}

	$req .= "FROM examens, session, atelier
		WHERE atelier.id_atelier=session.ref_atelier
		AND examens.ref_session=session.id_session
		AND examens.date_examen='".$date."'
		ORDER BY groupe, niveau, intitule" ;
	//echo $req ;
	$res = mysqli_query($cnx, $req) ;
	$nbExamens = mysqli_num_rows($res) ;

	// Total I (imputes/inscrits)
	$I = 0 ;

	if ( $nbExamens != 0 )
	{
		echo "<table class='tableau examens mooc" ;
		if ( $local ) {
			echo " local" ;
        }
		else {
			echo " distant" ;
        }
		echo "'>\n" ;
		echo "<thead><tr class='noprint'>\n" ;
		echo "<th>Inscription MOOC</th>\n" ;
		echo "<th class='help' title=\"Nombre théorique d'inscrits\">N</th>\n" ;
		echo "<th>Commentaire</th>\n" ;
		if ( $local AND ( intval($_SESSION["id"]) < 2 ) ) {
			echo "<th class='invisible'></th>\n" ;
		}
		echo "</tr></thead>\n" ;
		while ( $enr = mysqli_fetch_assoc($res) )
		{
			echo "<tr>\n" ;
			// En gras pour le responsable
			if ( $local AND  (intval($_SESSION["id"]) > 9) AND in_array($enr["id_session"], $_SESSION["tableau_toutes_promotions"]) ) {
				echo "<td><strong>" . $enr["annee"] . " - " . $enr["intitule"] . " - " . $enr["intit_ses"] . "</strong></td>\n" ;
			}
			else {
				echo "<td>" . $enr["annee"] . " - " . $enr["intitule"] . " - " . $enr["intit_ses"] . "</td>\n" ;
			}
	
			// Nombre d'imputés
			if ( $local AND ( (intval($_SESSION["id"]) < 9) OR in_array($enr["id_session"], $_SESSION["tableau_toutes_promotions"]) ) ) {
				echo "<td class='r'><a href='/imputations/promotion.php?promotion=".$enr["id_session"]."'>" . $enr["I"] . "</a></td>\n" ;
			}
			else {
				echo "<td class='r'>" . $enr["I"] . "</td>\n" ;
			}
			$I += intval($enr["I"]) ;
	
			echo "<td>" . $enr["commentaire"] . "</td>\n" ;
	
			if ( $local AND ( intval($_SESSION["id"]) < 2 ) ) {
				echo "<td class='noprint'>" ;
				echo "<span style='font-size: smaller;'><a href='examen.php?action=maj&amp;id_examen=".$enr["id_examen"]."'>" ;
				echo "Modifier</a></span>" ;
				echo "</td>\n" ;
			}
			echo "</tr>\n" ;
		}
		// Totaux
		echo "<tr>
			<td class='invisible r'></td>
			<th class='nvisible2 r'>$I</th>" ;
		echo "<td class='invisible l'></td>" ;
		if ( ( intval($_SESSION["id"]) < 2 ) ) {
			echo "<td class='invisible'></td>" ;
		}
		echo "</tr>\n" ;
		echo "</table >\n" ;
	}
}
?>
