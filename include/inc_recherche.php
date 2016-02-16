<?php
include_once("inc_etat_dossier.php") ;
include_once("inc_identite.php") ;
include_once("inc_etat_inscrit.php") ;
require_once("inc_formations.php") ;
require_once("inc_promotions.php") ;
include_once("inc_cnf.php") ;
require_once("inc_pays.php") ;
require_once("inc_date.php") ;
/*****************************************************************************/
function resultat_recherche($T)
{
	global $ETAT_DOSSIER ;
	global $ETAT_DOSSIER_IMG_CLASS ;
	$res = "<div class='res'>" ;

	// Pays, naissance
	$res .= "<div style='float: right'>\n" ;
		$res .= $T["nom_pays"] ;
	$res .= "</div>\n" ;

	// Civilite, nom, prenom
	$res .= "<div>" ;
	$res .= identite($T, TRUE) ;
	if ( $T["id_mooc"] != "" ) {
		$res .= " <span class='sep'>-</span> " ;
		$res .= "" . $T["id_mooc"] . ""  ;
	}
	$res .= " <span class='sep'>-</span> " ;
	$res .= $T["age"] . " ans" ;
	$res .= " <span class='sep'>-</span> " ;
	$res .= $T["email"] ;
	// Envoi mail
	if ( intval($_SESSION["id"]) == 0 ) {
		$res .= " <sup><a class='critere' href='email.php?id_dossier=" ;
		$res .= $T["id_dossier"] ;
		$res .= "' title='Corriger cette adresse email
et/ou envoyer un rappel de numéro de dossier et de mot de passe à cette adresse' class='help'>" ;
		$res .= "@</a></sup>" ;
		}
	$res .= "</div>" ;

	// Année, formation
	$res .= "<div>" ;
	$res .= "<strong>".$T["annee"]."</strong>" ;
	$res .= " <span class='sep'>-</span> " ;
	$res .= $T["intitule"] ;
	$res .= " <span class='sep'>-</span> " ;
	$res .= $T["intit_ses"] ;
	$res .= " <a title='Gestion des inscrits'" ;
	$res .= " href='/inscrits/inscrits.php?id_session=".$T["id_session"]."'>&#10138;</a>" ;


	if ( strval($T["id_imputation"]) != "" ) {
		//$res .= " <span class='sep'>-</span> " ;
		$res .= "<br \>" ;
		$res .= " <span class='paye'>".LABEL_INSCRIT."</span> " ;
		$res .= " <span class='s'>à</span> " ;
		$res .= $T["lieu_paiement"] ;
		$res .= " <span class='s'>examen à</span> " ;
		$res .= $T["lieu_examen"] ;
		if ( $T["etat_dossier"] == "1" ) {
			$res .= " <span class='sep'>-</span> " ;
			$res .= "<span class='c ".$ETAT_DOSSIER_IMG_CLASS[$T["etat_dossier"]]."'>" ;
			$res .= $ETAT_DOSSIER[$T["etat_dossier"]] . "</span>" ;
		}
	}
	$res .= "</div>" ;

	// Candidature, imputation
	$res .= "<div>" ;
	$lien_candidature = "<a target='_blank' href='/inscrits/inscrit.php?id_dossier=".$T["id_dossier"]."'>Dossier d'inscription</a>" ;
	$lien_dossier = "<a target='_blank' href='/inscrits/autre.php?id_dossier=".$T["id_dossier"]."'>Voir dossier</a>" ;

	// Candidature / Voir dossier
	if ( $T["evaluations"] == "Oui" ) {
		$res .= "<strong>$lien_candidature</strong>" ;
	}
	else {
		$res .= $lien_candidature ;
	}

	//
	// Imputation / Imputé
	//
	if ( intval($_SESSION["id"]) < 3 ) {
		$imputable = TRUE ;
	}
	else {
		$imputable = FALSE ;
	}
	// 1ère année
	if ( strval($T["id_imputation"]) == "" )
	{
		if 	( $imputable AND (dateOuiNon($T["imputations_deb"], $T["imputations_fin"]) == 'Oui') )
		{
			$res .= " <span class='sepp'>-</span> " ;
			$res .= "<a target='_blank' href='/imputations/imputer.php?id_dossier=" ;
			$res .= $T["id_dossier"] ;
			$res .= "'><strong>". LIEN_IMPUTER ."</strong></a>" ;
		}
	}
	else
	{
		$res .= " <span class='sepp'>-</span> " ;
		$res .= "<a target='_blank' href='/imputations/attestation.php?id=" ;
		$res .= $T["id_imputation"] ;
		if ( dateOuiNon($T["imputations_deb"], $T["imputations_fin"]) == 'Oui' ) {
			$res .= "'><strong>".LIEN_IMPUTATION."</strong></a>" ;
		}
		else {
			$res .= "'>".LIEN_IMPUTATION."</a>" ;
		}
	}

	// Ancien
	if ( $T["ref_ancien"] != 0 ) {
		$res .= " <span class='sepp'>-</span> " ;
		$res .= "<a target='_blank' href='/anciens/ancien.php?id_ancien=" ;
		$res .= $T["ref_ancien"] ;
		$res .= "'>".LIEN_ANCIEN."</a>" ;
	}

	$res .= "</div>" ;

	$res .= "</div>\n\n" ; // class='res'

//	print_r($T) ;
	echo $res ;
}
/*****************************************************************************/
/*****************************************************************************/
function liste_tri_recherche($name, $value)
{
	$TRI = array(
		"" => "",
		"session.annee DESC" => "Année",
		"intitule" => "Formation",
		"intit_ses" => "Inscription (Intitulé)",
		"date_deb" => "Inscription (Date de début de la formation)",
		"lieu_paiement" => "Lieu de paiement",
		"lieu_examen" => "Lieu d'examen",
		"etat_dossier" => "Résultat",
		"id_mooc" => "Identifiant MOOC",
		"nom" => "Nom de famille",
		"email" => "Adresse électronique",
		"nom_pays" => "Pays de résidence",
		"date_maj DESC" => "Date (de mise à jour) de la pré-inscription",
		"date_maj_imput DESC" => "Date (de mise à jour) de l'inscription",
	) ;

	echo "<select name='$name'>\n" ;
	while ( list($key, $val) = each($TRI) )
	{
		echo "<option value='$key'" ;
		if ( $value == $key ) {
			echo " selected='selected'" ;
		}
		echo ">$val</option>\n" ;
	}
	echo "</select>" ;
}
/*****************************************************************************/
/*****************************************************************************/

// Ajout d'un paramètre pour exporter numéro de dossier et mdp
function formulaire_recherche($cnx, $export="")
{
	echo "<form method='post' action='/recherche/session.php'>" ;
	echo "<form method='post' action='/recherche/session.php'>" ;
	echo "<table class='formulaire'>\n<tbody\n" ;
	// 
	echo "<tr>\n" ;
	echo "<th>Année&nbsp;: </th>\n" ;
	echo "<td colspan='2'><select name='rechercher_annee'>\n" ;
	echo "<option value=''></option>\n" ;
	$req = "SELECT DISTINCT(annee) FROM session " ;
	if ( intval($_SESSION["id"]) > 3 ) {
		$req .= " WHERE id_session IN (".$_SESSION["liste_toutes_promotions"].")" ;
	}
	$req .= " ORDER BY annee DESC" ;
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
		echo "<option value='".$enr["annee"]."'" ;
		if ( isset($_SESSION["filtres"]["recherche"]["annee"]) AND ($_SESSION["filtres"]["recherche"]["annee"] == $enr["annee"]) ) {
			echo " selected='selected'" ;
		}
		echo ">".$enr["annee"]."</option>" ;
	}
	echo "</select></td>\n" ;
	echo "</tr>\n" ;
	// 
	
	if ( intval($_SESSION["id"]) < 9 )
	{
		require_once("inc_institutions.php") ;
		echo "<tr>\n" ;
		echo "<th>Institution principale : </th>\n" ;
		echo "<td colspan='2'>" ;
		liste_institutions($cnx, "rechercher_ref_institution",
			( isset($_SESSION["filtres"]["recherche"]["ref_institution"]) ? $_SESSION["filtres"]["recherche"]["ref_institution"] : "" ),
			"formations"
			) ;
		echo "</td>\n" ;
		echo "</tr>\n" ;
	
		echo "<tr>\n" ;
		echo "<th>Formation : </th>\n" ;
		echo "<td colspan='2'>" ;
		liste_formations($cnx,
			"rechercher_formation",
			( isset($_SESSION["filtres"]["recherche"]["formation"]) ? $_SESSION["filtres"]["recherche"]["formation"] : "" )) ;
		/*
		$formForma = chaine_liste_formations("rechercher_formation",
			( isset($_SESSION["filtres"]["recherche"]["formation"]) ? $_SESSION["filtres"]["recherche"]["formation"] : "" ),
			"", $cnx) ;
		echo $formForma["form"] ;
		echo $formForma["script"] ;
		*/
		echo "</td>\n" ;
		echo "</tr>\n" ;
	}
	else
	{
		echo "<tr>\n" ;
		echo "<th>Formation : </th>\n" ;
		echo "<td colspan='2'>" ;
		liste_formations($cnx,
			"rechercher_formation",
			( isset($_SESSION["filtres"]["recherche"]["formation"]) ? $_SESSION["filtres"]["recherche"]["formation"] : "" ),
			( isset($_SESSION["liste_toutes_promotions"]) ? $_SESSION["liste_toutes_promotions"] : "" )) ;
		echo "</td>\n" ;
		echo "</tr>\n" ;
	}
	
	
	echo "<tr>\n" ;
	echo "<th>Inscription : </th>\n" ;
	echo "<td colspan='2' style='width: 50em;'>" ;
	if ( intval($_SESSION["id"]) < 9 )
	{
		echo liste_promotions("rechercher_promo",
			( isset($_SESSION["filtres"]["recherche"]["promo"]) ? $_SESSION["filtres"]["recherche"]["promo"] : "" ),
			$cnx, TRUE) ;
		/*
		$req = "SELECT id_session, annee, groupe, niveau, intitule, intit_ses
			FROM atelier, session
			WHERE session.ref_atelier=atelier.id_atelier
			ORDER BY annee DESC, groupe, niveau, intitule" ;
		$formPromo = chaine_liste_promotions("rechercher_promo",
			( isset($_SESSION["filtres"]["recherche"]["promo"]) ? $_SESSION["filtres"]["recherche"]["promo"] : "" ),
			$req, $cnx) ;
		echo $formPromo["form"] ;
		echo $formPromo["script"] ;
		*/
	}
	else {
		echo liste_promotions("rechercher_promo",
			( isset($_SESSION["filtres"]["recherche"]["promo"]) ? $_SESSION["filtres"]["recherche"]["promo"] : "" ),
			$cnx, TRUE) ;
	}
	echo "</td>\n</tr>\n" ;
	
	echo "<tr><td colspan='3' style='padding: 1px; background: #777; height: 1px;'></td></tr>" ;
	
	//if ( intval($_SESSION["id"]) < 3 ) {
	//}
		echo "<tr>\n" ;
		echo "<th>État : </th>\n" ;
		echo "<td colspan='2'>" ;
		liste_etat_inscrit("rechercher_etat",
			( isset($_SESSION["filtres"]["recherche"]["etat"]) ? $_SESSION["filtres"]["recherche"]["etat"] : "" ),
			TRUE) ;
		echo "</td>\n" ;
	echo "</tr>\n" ;
	// 
	echo "<tr>\n" ;
	echo "<th>Lieu de paiement : </th>\n" ;
	echo "</td>\n<td>" ;
	echo listeCnf("rechercher_lieu_paiement",
		( isset($_SESSION["filtres"]["recherche"]["lieu_paiement"]) ? $_SESSION["filtres"]["recherche"]["lieu_paiement"] : "" ),
	        TRUE) ;
	echo "</td>\n" ;
	echo "<td rowspan='3' class='s'>Ne concerne que les inscrits.<br /><br /> (Implique État = Inscrit.)</td>\n" ;
	echo "</tr>\n" ;
	// 
	echo "<tr>\n" ;
	echo "<th>Lieu d'examen : </th>\n<td>" ;
	echo listeCnf("rechercher_lieu_examen",
		( isset($_SESSION["filtres"]["recherche"]["lieu_examen"]) ? $_SESSION["filtres"]["recherche"]["lieu_examen"] : "" ),
	        TRUE) ;
	echo "</td>\n</tr>\n" ;
	//
	echo "<tr>\n" ;
	echo "<th>Résultat : </th>\n" ;
	echo "<td>" ;
	if ( isset($_SESSION["filtres"]["recherche"]["etat_dossier"]) ) {
		liste_etats("rechercher_etat_dossier", $_SESSION["filtres"]["recherche"]["etat_dossier"], TRUE, TRUE, TRUE) ;
	}
	else {
		liste_etats("rechercher_etat_dossier", "", TRUE, TRUE, TRUE) ;
	}
	echo "</td>\n" ;
	echo "</tr>\n" ;
	
	
	
	//
	echo "<tr><td colspan='3' style='padding: 1px; background: #777; height: 1px;'></td></tr>" ;
	// 
	echo "<tr>\n" ;
	echo "<th>Genre : </th>\n" ;
	echo "<td colspan='2'><select name='rechercher_genre'>" ;
	echo "<option value=''></option>\n" ;
	echo "<option value='Femme'" ;
	if ( isset($_SESSION["filtres"]["recherche"]["genre"]) AND ($_SESSION["filtres"]["recherche"]["genre"] == "Femme") ) {
		echo " selected='selected'" ;
	}
	echo ">Femme</option>\n" ;
	echo "<option value='Homme'" ;
	if ( isset($_SESSION["filtres"]["recherche"]["genre"]) AND ($_SESSION["filtres"]["recherche"]["genre"] == "Homme") ) {
		echo " selected='selected'" ;
	}
	echo ">Homme</option>\n" ;
	echo "</td></select>\n" ;
	echo "</tr>\n" ;
	// 
	echo "<tr>\n" ;
	echo "<th>Identifiant MOOC : </th>\n" ;
	echo "<td><input type='text' name='rechercher_id_mooc' size='30' " ;
	if ( isset($_SESSION["filtres"]["recherche"]["id_mooc"]) ) {
		echo "value=\"".$_SESSION["filtres"]["recherche"]["id_mooc"]."\"" ;
	}
	echo "/></td>\n" ;
	echo "<td rowspan='4' style='font-size: smaller;'>" ;
	echo "Recherche d'une partie de l'identifiant MOOC,<br />
	du nom de famille, du prénom, ou de l'adresse électronique.<br /><br />
	Recherche insensible<br />
	- à la casse <span class='aide' title='Majuscules et minuscules sont équivalentes'>?</span><br />
	- et aux caractères diacritiques <span class='aide' title='Lettres accentuées et ç cédille'>?</span>.
	" ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
	// 
	echo "<tr>\n" ;
	echo "<th class='help'>Nom de famille&nbsp;: </th>\n" ;
	echo "<td><input type='text' name='rechercher_nom' size='30' " ;
	if ( isset($_SESSION["filtres"]["recherche"]["nom"]) ) {
		echo "value=\"".$_SESSION["filtres"]["recherche"]["nom"]."\"" ;
	}
	echo "</td>\n" ;
	echo "</tr>\n" ;
	//
	echo "<tr>\n" ;
	echo "<th class='help'>Prénom&nbsp;: </th>\n" ;
	echo "<td><input type='text' name='rechercher_prenom' size='30' " ;
	if ( isset($_SESSION["filtres"]["recherche"]["prenom"]) ) {
		echo "value=\"".$_SESSION["filtres"]["recherche"]["prenom"]."\"" ;
	}
	echo "</td>\n" ;
	echo "</tr>\n" ;
	//
	echo "<tr>\n" ;
	echo "<th class='help'>Adresse électronique&nbsp;: </th>\n" ;
	echo "<td><input type='text' name='rechercher_email' size='30' " ;
	if ( isset($_SESSION["filtres"]["recherche"]["email"]) ) {
		echo "value=\"".$_SESSION["filtres"]["recherche"]["email"]."\"" ;
	}
	echo "</td>\n" ;
	echo "</tr>\n" ;
	//
	echo "<tr>\n" ;
	echo "<th title='Pays de résidence' class='help'>Pays de résidence&nbsp;: </th>\n" ;
	echo "<td colspan='2'>" ;
	//liste_pays("rechercher_pays", ( isset($_SESSION["filtres"]["recherche"]["pays"]) ? $_SESSION["filtres"]["recherche"]["pays"] : "" ), TRUE) ;
	echo selectPays($cnx, "rechercher_pays",
		( isset($_SESSION["filtres"]["recherche"]["pays"]) ? $_SESSION["filtres"]["recherche"]["pays"] : "" )
		) ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
	//
	// Gestionnaires AUF
	if ( intval($_SESSION["id"]) < 3 )
	{
		echo "<tr>\n" ;
		echo "<th>Région de résidence&nbsp;: </th>\n" ;
		echo "<td colspan='2'>" ;
		echo selectRegion($cnx, "rechercher_region",
			( isset($_SESSION["filtres"]["recherche"]["region"]) ? $_SESSION["filtres"]["recherche"]["region"] : "" )
			) ;
		echo "</td>\n" ;
		echo "</tr>\n" ;
	}
	//
	echo "<tr><td colspan='3' style='padding: 1px; background: #777; height: 1px;'></td></tr>" ;
	//
	echo "<tr>\n" ;
	echo "<th>Trier par&nbsp;: </th>\n" ;
	echo "<td colspan='2'>" ;
	// Tri 1
	liste_tri_recherche("rechercher_tri1",
		(isset($_SESSION["filtres"]["recherche"]["tri1"]) ? $_SESSION["filtres"]["recherche"]["tri1"] : "")
	) ;
	echo ", " ;
	// Tri 2
	liste_tri_recherche("rechercher_tri2",
		(isset($_SESSION["filtres"]["recherche"]["tri2"]) ? $_SESSION["filtres"]["recherche"]["tri2"] : "")
	) ;
	echo ", " ;
	// Tri 3
	liste_tri_recherche("rechercher_tri3",
		(isset($_SESSION["filtres"]["recherche"]["tri3"]) ? $_SESSION["filtres"]["recherche"]["tri3"] : "")
	) ;
	echo "</td>\n" ;
	echo "</tr>\n" ;
	//
	echo "<tr><td colspan='3' style='padding: 1px; background: #777; height: 1px;'></td></tr>" ;
	
	echo "</tr>\n<td colspan='3'>" ;


	echo "<p class='c' style='float: left; width: 60%;'>" ;
	echo "<span style='font-size: smaller; padding-right: 1em;'><a href='reinitialiser.php'>Réinitialiser</a></span>\n" ;
	if ( isset($_SESSION["filtres"]["recherche_precedente"]) ) {
		echo "<span style='font-size: smaller; padding-right: 1em;'><a href='precedente.php'>Recherche précédente</a></span>\n" ;
	}
	echo "<input class='b' type='submit' name='Rechercher' value='Rechercher' />\n" ;
	echo "</p>\n" ;

	echo "<p class='r' style='margin-left: 1em;'>" ;
	echo "<input class='b' type='submit' name='Exporter' value='Exporter'" ;
	/*
	if ( !isset($_SESSION["filtres"]["recherche"]["ok"]) ) {
		echo "disabled='disabled'" ;
	}
	*/
	echo " />" ;
	echo "<label for='latin1' style='padding-left: 1em;'>" ;
	echo "<input type='checkbox' id='latin1' name='latin1' value='latin1' />" ;
	echo " &nbsp;Exporter en ISO-8859-1</label>" ;
	/*
	echo "<br />" ;
	echo "<span style='float: right; clear: right; font-size: smaller; text-align: right;'>" ;
	echo "<a href='/exports/'>Choix des champs à exporter</a>" ;
	echo "</span>" ;
	*/
	echo "</p>\n" ;


	echo "</td></tr>\n" ;
	
	echo "</tbody>\n</table>\n" ;
}
?>
