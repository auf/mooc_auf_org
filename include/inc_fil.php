<?php

require_once("inc_erreurs.php") ;
require_once("inc_institutions.php") ;
function formulaire_fil_nouveau($cnx, $T)
{
	$form = "" ;
	$form .= "<form method='post' action='fil_nouveau.php'>\n" ;
	// $form .= "<input type='hidden' name='etape' value='1' />\n" ;
	$form .= "<table class='formulaire'>\n" ;

	$form .= "<tr><td colspan='2' class=''>" ;
/*
	$form .= "<h3 style='margin: 0;'>Étape 1</h3>\n" ;
	$form .= "<h3 style='margin: 0 0 0 1em;'>Étape 1</h3>\n" ;
	$form .= "<li></li>\n" ;
	$form .= "<li>Le choix d'une année est obligatoire (les fils sont triés par année).</li>\n" ;
	$form .= "<li>Le choix d'une institution est facultatif.</li>\n" ;
	$form .= "<li>Le choix d'un institution Le titre du fil, défini à l'étape suivante, est par défaut le nom de l'institution choisie</li>\n" ;
	$form .= "<li></li>\n" ;
*/
	$form .= "<ul>\n" ;
	$form .= "<li><strong>Année</strong> et <strong>Titre</strong> sont les deux seuls champs obligatoires.</li>\n" ;
	$form .= "<li>Le choix d'une <strong>Institution</strong> permet de remplir le <strong>Titre</strong> avec le nom de l'institution.
<br />Il suffit pour cela de laisser le <strong>Titre</strong> vide.</li>\n" ;
	$form .= "<li>Le choix de l'<strong>Année</strong> et de l'<strong>Institution</strong> associent automatiquement toutes les promotions <br />correspondantes au fil de messages (celles-ci peuvent ensuite être éditées).</li>\n" ;
//	$form .= "<li>Une promotion ne peut pas être concernée par plus d'un fil.</li>\n" ;
	$form .= "</ul>\n" ;
	$form .= "</td></tr>\n" ;


	$annee_courante = intval(date("Y", time())) ;
	// Au lieu de proposer un choix vide, on choisit par défaut l'année courante
	if ( empty($T["annee"]) OR ($T["annee"]==0) ) {
		$T["annee"] = $annee_courante ;
	}
	$form .= "<tr>\n" ;
	$form .= "<th>Année&nbsp;: </th>\n" ;
	$form .= "<td><select name='annee'>\n" ;
	for ( $i=($annee_courante+1) ; $i >= 2011 ; $i-- ) {
		$form .= "<option value='".$i."'" ;
		if ( $T["annee"] == $i ) {
			$form .= " selected='selected'" ;
		}
		$form .= ">".$i."</option>" ;
	}
	$form .= "</select></td>\n" ;
	$form .= "</tr>\n" ;

	$form .= "<tr>\n" ;
		$form .= "<th>Institution&nbsp;:</th>\n" ;
		$form .= "<td>\n" ;
		$form .= listeInstitutions($cnx, "ref_institution",
			( isset($T["ref_institution"]) ? $T["ref_institution"] : "" ),
			"formations"
		) ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;

	$form .= "<tr>\n" ;
		$form .= "<th>Titre&nbsp;:</th>\n" ;
		$form .= "<td>\n" ;
		$form .= "<textarea name='titre' rows='1' cols='70'>" ;
		$form .= ( isset($T["titre"]) ? $T["titre"] : "" ) ;
		$form .= "</textarea>" ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;

	$form .= "<tr>\n" ;
		$form .= "<th>Commentaire&nbsp;:</th>\n" ;
		$form .= "<td>\n" ;
		$form .= "<textarea name='commentaire' rows='2' cols='70'>" ;
		$form .= ( isset($T["commentaire"]) ? $T["commentaire"] : "" ) ;
		$form .= "</textarea>" ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;

	$form .= "<tr>\n<td colspan='2'>" ;
	$form .= "<p class='c'><input type='submit' name='submit' " ;
	$form .= "value='Enregistrer' style='font-weight: bold;' /></p>\n" ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "</table>\n" ;
	$form .= "</form>\n" ;
	echo $form ;
}

function controle_fil_nouveau($T)
{
	$erreurs = array() ;

	if ( (trim($T["titre"] == "")) AND (intval($T["ref_institution"]) == 0) ) {
		$erreurs[] = "Le titre est obligatoire (et ne peut pas être rempli automatiquement par le nom d'une institution, car aucune n'est sélectionnée)." ;
	}
	
	return erreurs_string($erreurs) ;
}

function fil_tab($cnx, $id_fil)
{
	$fil = array() ;

	// fil
	$req = "SELECT fils.*, ref_etablissement.nom AS nom_etablissement FROM fils
		LEFT JOIN ref_etablissement ON ref_etablissement.id=fils.ref_institution
		WHERE id_fil=". $id_fil ;
	$res = mysqli_query($cnx, $req) ;
	if ( mysqli_num_rows($res) != 1 )
	{
		return $fil ;
	}
	$enr = mysqli_fetch_assoc($res) ;
	$fil = $enr ;

	// sessions
	$req = "SELECT ref_session, intit_ses, annee, intitule FROM fils_sessions
		LEFT JOIN session ON fils_sessions.ref_session=session.id_session
		LEFT JOIN atelier ON session.id_atelier=atelier.id_atelier
		WHERE fils_sessions.ref_fil=".$id_fil."
		ORDER BY niveau, intitule" ;
	$res = mysqli_query($cnx, $req) ;
	$nb_sessions = mysqli_num_rows($res) ;
	$fil["nb_sessions"] = $nb_sessions ;
	$tab_sessions = array() ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		//$tab_sessions[] = $enr["ref_session"] ;
		$fil["sessions"][$enr["ref_session"]] = array(
			"annee" => $enr["annee"],
			"intitule" => $enr["intitule"],
			"intit_ses" => $enr["intit_ses"],
		) ;
	}
	//$fil["tab_sessions"] = $tab_sessions ;

	// individus
	$req = "SELECT ref_individu, nom, prenom, cnf, actif, courriel FROM fils_individus
		LEFT JOIN individus ON fils_individus.ref_individu=individus.id_individu
		WHERE fils_individus.ref_fil='".$id_fil."' " ;
//	$req .= " AND individus.actif='1' " ;
	$req .= " ORDER BY nom" ;
	$res = mysqli_query($cnx, $req) ;
	$nb_individus = mysqli_num_rows($res) ;
	$fil["nb_individus"] = $nb_individus ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		if ( $enr["actif"] == 1 ) {
			$inactif = "" ;
		}
		else {
			$inactif = " <span class='actif0'>inactif</span>" ;
		}
		$fil["individus"][$enr["ref_individu"]] = array(
			"nom" => $enr["nom"],
			"prenom" => $enr["prenom"],
			"cnf" => $enr["cnf"],
			"courriel" => $enr["courriel"],
			"actif" => $enr["actif"],
			"individu" => strtoupper($enr["nom"]) . " " . $enr["prenom"]
				. " (" . $enr["cnf"] . ")" .$inactif,
		);
	}

	return $fil ;
}

function affiche_fil($fil)
{
	$str  = "" ;
	$str .= "<table class='formulaire'>\n" ;

	$str .= "<tr id='ancre_fil'>\n" ;
		$str .= "<th>Année&nbsp;: </th>\n" ;
		$str .= "<td>".$fil["annee"]."</td>\n" ;
			$str .= "<td rowspan='2'><strong><a href='fil.php?id_fil=" ;
			$str .= $fil["id_fil"]."&action=fil'>Modifier</a></strong></td>\n" ;
			/*
			$str .= "<td rowspan='2'>" ;
			$str .= "Modifier l'année ou l'institution" ;
			$str .= "</td>\n" ;
			*/
	$str .= "</tr>\n" ;
	$str .= "<tr>\n" ;
		$str .= "<th>Institution&nbsp;:</th>\n" ;
		$str .= "<td>".$fil["nom_etablissement"]."</td>\n" ;
	$str .= "</tr>\n" ;

	$str .= "<tr><td colspan='3' class='separation'></td></tr>\n" ;

	$str .= "<tr id='ancre_meta'>\n" ;
		$str .= "<th>Titre&nbsp;:</th>\n" ;
		$str .= "<td>".$fil["titre"]."</td>\n" ;
			$str .= "<td rowspan='2'><strong><a href='fil.php?id_fil=" ;
			$str .= $fil["id_fil"]."&action=meta'>Modifier</a></strong></td>\n" ;
	$str .= "</tr>\n" ;
	$str .= "<tr>\n" ;
		$str .= "<th>Commentaire&nbsp;:</th>\n" ;
		$str .= "<td>".$fil["commentaire"]."</td>\n" ;
	$str .= "</tr>\n" ;

	$str .= "<tr><td colspan='3' class='separation'></td></tr>\n" ;

	$str .= "<tr id='ancre_sessions'>\n" ;
		if ( $fil["nb_sessions"] > 1 ) { $s = "s" ; } else { $s = "" ; }
		$str .= "<th>".$fil["nb_sessions"]." promotion".$s."&nbsp;:</th>\n" ;
		$str .= "<td>" ;
		if ( $fil["nb_sessions"] != 0 ) {
			$sessions = $fil["sessions"] ;
			while ( list($key, $val) = each($sessions) ) {
				$str .= $val["intitule"] . "<br />" ;
			}
		}
		$str .= "</td>\n" ;
			$str .= "<td><strong><a href='fil.php?id_fil=" ;
			$str .= $fil["id_fil"]."&action=sessions'>Modifier</a></strong></td>\n" ;
	$str .= "</tr>\n" ;

	$str .= "<tr><td colspan='3' class='separation'></td></tr>\n" ;

	$str .= "<tr id='ancre_individus'>\n" ;
		if ( $fil["nb_individus"] > 1 ) { $s = "s" ; } else { $s = "" ; }
		$str .= "<th>".$fil["nb_individus"]." destinataire".$s."&nbsp;:</th>\n" ;
		$str .= "<td>" ;
		if ( $fil["nb_individus"] != 0 ) {
			$individus = $fil["individus"] ;
			while ( list($key, $val) = each($individus) ) {
				$str .= $val["individu"] ;
				$str .= "<br />" ;
			}
		}
		$str .= "</td>\n" ;
			$str .= "<td><strong><a href='fil.php?id_fil=" ;
			$str .= $fil["id_fil"]."&action=individus'>Modifier</a></strong></td>\n" ;
	$str .= "</tr>\n" ;

	$str .= "</table>\n" ;

	echo $str ;
}

require_once("inc_institutions.php") ;
function formulaire_fil_fil($cnx, $T)
{
	$form = "" ;
	echo "<form method='post' action='fil.php?".$_SERVER["QUERY_STRING"]."'>\n" ;
	$form .= "<table class='formulaire'>\n" ;

	$form .= "<tr><td colspan='2' class=''>" ;
	$form .= "<p class='c'>Le changement de l'<strong>Année</strong> et/ou de l'<strong>Institution</strong> <br />modifie automatiquement les promotions correspondantes.</p>" ;
	$form .= "</td></tr>\n" ;

	$form .= "<tr>\n" ;
	$form .= "<th>Année&nbsp;: </th>\n" ;
	$form .= "<td><select name='annee'>\n" ;
	$annee_courante = intval(date("Y", time())) ;
	for ( $i=($annee_courante+1) ; $i >= 2011 ; $i-- ) {
		$form .= "<option value='".$i."'" ;
		if ( $T["annee"] == $i ) {
			$form .= " selected='selected'" ;
		}
		$form .= ">".$i."</option>" ;
	}
	$form .= "</select></td>\n" ;
	$form .= "</tr>\n" ;

	$form .= "<tr>\n" ;
		$form .= "<th>Institution&nbsp;:</th>\n" ;
		$form .= "<td>\n" ;
		$form .= listeInstitutions($cnx, "ref_institution", $T["ref_institution"], "formations") ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;

	$form .= "<tr>\n<td colspan='2'>" ;
	if ( $fil["nb_sessions"] != 0 ) {
		$form .= "<p class='c erreur'>Suppression autoimatique des promotions liées au fil en cas de changement.</p> " ; // FIXME afficher nombre
	}
	$form .= "<p class='c'><input type='submit' name='submit' " ;
	$form .= "value='Enregistrer' style='font-weight: bold;' /></p>\n" ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "</table>\n" ;
	$form .= "</form>\n" ;
	echo $form ;
}

function formulaire_fil_meta($T)
{
	$form = "" ;
	echo "<form method='post' action='fil.php?".$_SERVER["QUERY_STRING"]."'>\n" ;
	$form .= "<table class='formulaire'>\n" ;

	$form .= "<tr>\n" ;
		$form .= "<th>Titre&nbsp;:</th>\n" ;
		$form .= "<td>\n" ;
		$form .= "<textarea name='titre' rows='1' cols='70'>" ;
		$form .= $T["titre"] ;
		$form .= "</textarea>" ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;

	$form .= "<tr>\n" ;
		$form .= "<th>Commentaire&nbsp;:</th>\n" ;
		$form .= "<td>\n" ;
		$form .= "<textarea name='commentaire' rows='2' cols='70'>" ;
		$form .= $T["commentaire"] ;
		$form .= "</textarea>" ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;

	$form .= "<tr>\n<td colspan='2'>" ;
	$form .= "<p class='c'><input type='submit' name='submit' " ;
	$form .= "value='Enregistrer' style='font-weight: bold;' /></p>\n" ;
	$form .= "</td>\n</tr>\n" ;

	$form .= "</table>\n" ;
	$form .= "</form>\n" ;
	echo $form ;
}

function controle_fil_meta($T)
{
	$erreurs = array() ;

	if ( (trim($T["titre"] == "")) AND (intval($T["ref_institution"]) == 0) ) {
		$erreurs[] = "Le titre est obligatoire" ;
	}
	
	return erreurs_string($erreurs) ;
}

function formulaire_fil_sessions($cnx, $fil)
{
	if ( $fil["nb_sessions"] != 0 ) {
		$sessions = $fil["sessions"] ;
	}
	else {
		$sessions = array() ;
	}

	// sessions max
	$potentiel = array() ;
	if ( $fil["ref_institution"] != 0 )
	{
		$req = "SELECT id_session, intit_ses, annee, intitule
			FROM session, atelier
			WHERE session.id_atelier=atelier.id_atelier
			AND annee=".$fil["annee"]."
			AND ref_institution=".$fil["ref_institution"]."
			ORDER BY niveau, intitule" ;
		$res = mysqli_query($cnx, $req) ;
		$nb_potentiel = mysqli_num_rows($res) ;
		$fil["nb_potentiel"] = $nb_potentiel ;
		while ( $enr = mysqli_fetch_assoc($res) )
		{
			$potentiel[$enr["id_session"]] = array(
				"annee" => $enr["annee"],
				"intitule" => $enr["intitule"],
				"intit_ses" => $enr["intit_ses"],
			) ;
		}
	}

	$form = "" ;
	echo "<form method='post' action='fil.php?".$_SERVER["QUERY_STRING"]."'>\n" ;
	$form .= "<table class='formulaire hover'>\n" ;
	$form .= "<tbody>\n" ;
	if ( count($potentiel) != 0 )
	{
		while ( list($key, $val) = each($potentiel) )
		{
			if ( array_key_exists($key, $sessions) ) {
				$checked = " checked='checked'" ;
				$class = " class='aex'" ;
			}
			else {
				$checked = "" ;
				$class = "" ;
			}
			$form .= "<tr".$class.">\n<th><input type='checkbox' name='promos[]' " ;
			$form .= "id='p".$key."' value='$key'".$checked." /></th>\n" ;
			$form .= "<td><label class='bl' for='p".$key."'>" ;
			$form .= $val["intitule"] ;
			$form .= "</label></td>\n" ;
			$form .= "</tr>\n" ;
		}
	}
	else
	{
		$form .= "<tr><td colspan='2'>Aucune promotion.</td></tr>" ;
	}
	$form .= "</tbody>\n" ;
	$form .= "<tfoot>\n" ;
	$form .= "<tr>\n<td colspan='2'>" ;
	$form .= "<p class='c'><input type='submit' name='submit' " ;
	$form .= "value='Enregistrer' style='font-weight: bold;' /></p>\n" ;
	$form .= "</td>\n</tr>\n" ;
	$form .= "</tfoot>\n" ;
	$form .= "</table>\n" ;
	$form .= "</form>\n" ;
	echo $form ;
}

function formulaire_fil_individus($cnx, $fil)
{
	if ( $fil["nb_individus"] != 0 ) {
		$individus = $fil["individus"] ;
	}
	else {
		$individus = array() ;
	}
	$potentiel = array() ;

	$req = "SELECT * FROM individus
		WHERE actif='1'
		ORDER BY nom" ;
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		$potentiel[$enr["id_individu"]] = array(
			"nom" => $enr["nom"],
			"prenom" => $enr["prenom"],
			"individu" => strtoupper($enr["nom"]) . " " . $enr["prenom"]
				. " (" . $enr["cnf"] . ")",
		) ;
	}

	$form = "" ;
	echo "<form method='post' action='fil.php?".$_SERVER["QUERY_STRING"]."'>\n" ;
	$form .= "<table class='formulaire hover'>\n" ;
	$form .= "<tbody>\n" ;
	while ( list($key, $val) = each($potentiel) )
	{
		if ( array_key_exists($key, $individus) ) {
			$checked = " checked='checked'" ;
			$class = " class='aex'" ;
		}
		else {
			$checked = "" ;
			$class = "" ;
		}
		$form .= "<tr".$class.">\n<th><input type='checkbox' name='destin[]' " ;
		$form .= "id='p".$key."' value='$key'".$checked." /></th>\n" ;
		$form .= "<td><label class='bl' for='p".$key."'>" ;
		$form .= $val["individu"] ;
		$form .= "</label></td>\n" ;
		$form .= "</tr>\n" ;
	}
	$form .= "</tbody>\n" ;
	$form .= "<tfoot>\n" ;
	$form .= "<tr>\n<td colspan='2'>" ;
	$form .= "<p class='c'><input type='submit' name='submit' " ;
	$form .= "value='Enregistrer' style='font-weight: bold;' /></p>\n" ;
	$form .= "</td>\n</tr>\n" ;
	$form .= "</tfoot>\n" ;
	$form .= "</table>\n" ;
	$form .= "</form>\n" ;
	echo $form ;
}

function fil_sessions($cnx, $id_fil, $id_institution, $annee)
{
	$req = "DELETE FROM fils_sessions WHERE ref_fil=".$id_fil ;
	$res = mysqli_query($cnx, $req) ;

	if ( intval($id_institution) != 0 )
	{
		$req = "SELECT id_session FROM session, atelier
			WHERE session.id_atelier=atelier.id_atelier
			AND annee='$annee'
			AND ref_institution='$id_institution'
			ORDER BY niveau, intitule" ;
		$res = mysqli_query($cnx, $req) ;
		if ( mysqli_num_rows($res) != 0 )
		{
			$requete = "INSERT INTO fils_sessions(ref_fil, ref_session) VALUES" ;
			while ( $enr = mysqli_fetch_assoc($res) )
			{
				$requete .= "(".$id_fil.", ".$enr["id_session"].")," ;
			}
			$requete = substr($requete, 0, -1) ;
			$res = mysqli_query($cnx, $requete) ;
		}
	}
}
?>
