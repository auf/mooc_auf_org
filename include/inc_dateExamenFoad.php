<?php
// ($local == TRUE) => lien sur chaque nombre
// ($local == FALSE) => Style différent ( class distant)
function dateExamenFoad($cnx, $date, $local=TRUE)
{
	$req  = "SELECT examens.*, atelier.intitule, session.annee, session.id_session," ;

	if ( isset($_SESSION["filtres"]["examens"]["lieu"]) AND ($_SESSION["filtres"]["examens"]["lieu"]!="") ) {
		$req .= "(SELECT COUNT(id_imputation) FROM imputations, dossier
			WHERE id_dossier=ref_dossier AND id_session=session.id_session
			AND imputations.lieu='".$_SESSION["filtres"]["examens"]["lieu"]."'
			) AS I, " ;
	}
	else {
		$req .= "(SELECT COUNT(id_imputation) FROM imputations, dossier
			WHERE id_dossier=ref_dossier AND id_session=session.id_session
			) AS I, " ;
	}

	if ( isset($_SESSION["filtres"]["examens"]["pays"]) AND ($_SESSION["filtres"]["examens"]["pays"]!="") ) {
		$req .= "(SELECT COUNT(id_dossier) FROM dossier, candidat
			WHERE dossier.id_candidat=candidat.id_candidat
			AND etat_dossier='Payant établissement'
			AND candidat.pays='".mysqli_real_escape_string($cnx, $_SESSION["filtres"]["examens"]["pays"])."'
			AND id_session=session.id_session) AS PE " ;
	}
	else {
		$req .= "(SELECT COUNT(id_dossier) FROM dossier
			WHERE etat_dossier='Payant établissement'
			AND id_session=session.id_session) AS PE " ;
	}

	$req .= "FROM examens, session, atelier
		WHERE atelier.id_atelier=session.id_atelier
		AND examens.ref_session=session.id_session
		AND date_examen='".$date."'
		ORDER BY groupe, niveau, intitule" ;
	$res = mysqli_query($cnx, $req) ;
	$nbExamens = mysqli_num_rows($res) ;

	// Totaux I et PE
	$I = $PE = 0 ;

	if ( $nbExamens != 0 )
	{
		echo "<table class='tableau examens foad" ;
		if ( $local ) {
			echo " local" ;
		}
		else {
			echo " distant" ;
		}
		echo "'>\n" ;
		echo "<thead><tr class='noprint'>\n" ;
		echo "<th>Promotion FOAD</th>\n" ;
		echo "<th class='help' title=\"Nombre théorique d'imputés\">N</th>\n" ;
		echo "<th class='help' title='Nombre théorique de Payant établissement'>PE</th>\n" ;
		echo "<th>Commentaire</th>\n" ;
		if ( $local AND ( intval($_SESSION["id"]) < 2 ) ) {
			echo "<th class='invisible'></th>\n" ;
		}
		echo "</tr></thead>\n" ;
		while ( $enr = mysqli_fetch_assoc($res) )
		{
			echo "<tr>\n" ;
			// En gras pour le responsable
			if ( $local AND (intval($_SESSION["id"]) > 9) AND in_array($enr["id_session"], $_SESSION["tableau_toutes_promotions"]) ) {
				echo "<td>" . $enr["annee"] . " - <strong>" . $enr["intitule"] . "</strong></td>\n" ;
			}
			else {
				echo "<td>" . $enr["annee"] . " - " . $enr["intitule"] . "</td>\n" ;
			}
	
			// Nombre d'imputés
			if	(	$local AND
					( (intval($_SESSION["id"]) < 9) OR in_array($enr["id_session"], $_SESSION["tableau_toutes_promotions"]) )
				)
			{
				echo "<td class='r'><a href='/imputations/promotion.php?promotion=".$enr["id_session"]."'>" . $enr["I"] . "</a></td>\n" ;
			}
			else {
				echo "<td class='r'>" . $enr["I"] . "</td>\n" ;
			}
			// Nombre de Payant établissement	
			if	(	$local AND 
					( (intval($_SESSION["id"]) < 9) OR in_array($enr["id_session"], $_SESSION["tableau_toutes_promotions"]) )
				)
			{
				echo "<td class='r'><a href='/candidatures/pe.php?id_session=".$enr["id_session"] ;
				if ( isset($_SESSION["filtres"]["examens"]["pays"]) AND ($_SESSION["filtres"]["examens"]["pays"]!="") ) {
					echo "&pays=".urlencode($_SESSION["filtres"]["examens"]["pays"]) ;
				}
				echo "'>" . $enr["PE"] . "</a></td>\n" ;
			}
			else {
				echo "<td class='r'>" . $enr["PE"] . "</td>\n" ;
			}
	
			$I += intval($enr["I"]) ;
			$PE += intval($enr["PE"]) ;
	
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
			<th class='nvisible2 r'>$I</th>
			<th class='nvisible2 r'>$PE</th>" ;
		//echo "<td class='invisible l'>".strval($I + $PE)."</td>" ;
		echo "<td class='invisible l'></td>" ;
		if ( ( intval($_SESSION["id"]) < 2 ) ) {
			echo "<td class='invisible'></td>" ;
		}
		echo "</tr>\n" ;
		echo "</table >\n" ;
	}
}
