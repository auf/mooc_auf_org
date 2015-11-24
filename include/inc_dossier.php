<?php
include_once("inc_guillemets.php") ;
include_once("inc_etat_dossier.php") ;
global $ETAT_DOSSIER_IMG_CLASS ;

function estDefini($champ) {
	if ( $champ == "-------" ) {
		return FALSE;
	}
	else if ( $champ == "" ) {
		return FALSE;
	}
	else {
		return TRUE;
	}
}

//
// Mise en page
//
function intitule_champ($champ)
{
	global $CANDIDATURE ;
	if ( !isset($CANDIDATURE[$champ][1]) OR ($CANDIDATURE[$champ][1] == "") ) {
		$str = $CANDIDATURE[$champ][0] ;
	}
	else {
		$str  = "<span class='help' title=\"" ;
		$str .= strip_tags($CANDIDATURE[$champ][0]) ."\">" ;
		$str .= $CANDIDATURE[$champ][1] ;
		$str .= "</span>" ;
	}
	return $str ;
}
function affiche_tr($champ, $t, $space=FALSE)
{
	echo "<tr>\n\t<th><span class='chp'>" ;
	if ( $space ) {
		echo str_replace(" ", "&nbsp;", intitule_champ($champ)) ;
	}
	else {
		echo intitule_champ($champ) ;
	}
	echo "&nbsp;:</span></th>\n " ;
	echo "\t<td>" ;
	echo $t[$champ] ;
	echo "</td>\n</tr>\n" ;
}
function affiche_p($champ, $t, $newline=FALSE)
{
	echo "<p>" ;
	echo "<span class='chp'>" ;
	echo intitule_champ($champ) ;
	echo "</span><br />" ;
	if ( $newline == FALSE ) {
		echo $t[$champ] ;
	}
	else {
		if ( $champ == "cv" ) {
			echo "<span class='cv'>" ;
			echo nl2br($t[$champ]) ;
			echo "</span>" ;
		}
		else {
			echo nl2br($t[$champ]) ;
			//echo "\n\n<br />\n\n" ;
			//echo $t[$champ] ;
		}
	}
	echo "</p>\n" ;
}
function affiche_p1($champ, $t, $newline=FALSE)
{
	echo "<p>" ;
	echo "<span class='chp1'>" ;
	echo intitule_champ($champ) ;
	echo "</span><br />" ;
	if ( $newline == FALSE ) {
		echo $t[$champ] ;
	}
	else {
		echo nl2br($t[$champ]) ;
	}
	echo "</p>\n" ;
}
function affiche_p1alter($intitule, $champ, $t, $newline=FALSE)
{
	echo "<p>" ;
	echo "<span class='chp1'>" ;
	echo intitule_champ($intitule) ;
	echo "</span><br />" ;
	if ( $newline == FALSE ) {
		echo $t[$champ] ;
	}
	else {
		echo nl2br($t[$champ]) ;
	}
	echo "</p>\n" ;
}


//
// Recherche des autres candidatures
//
function recherche_idem($cnx, $id_dossier, $email)
{
	$alerte = "" ;

	global $etat_dossier_img_class ;
	echo "<div class='recherche'><div>" ;

	$nom = mysqli_real_escape_string($cnx, trim($nom)) ;

	$req = "SELECT id_dossier FROM dossier WHERE id_dossier!='$id_dossier'
		AND (
			email='$email'
--			OR ( (naissance='$naissance') AND (nom LIKE '".$nom."%') )
		)" ;

//	echo $req ;

	$res = mysqli_query($cnx, $req) ;
	$N = @mysqli_num_rows($res) ;
	if ( $N > 0 )
	{
		$liste_id_dossier = "" ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
			$liste_id_dossier .= $enr["id_dossier"] . ", " ;
		}
		$liste_id_dossier = substr($liste_id_dossier, 0, -2) ;

		if ( $N > 1 ) {
			$s = "s" ;
		}
		else {
			$s = "" ;
		}
		echo "<p class='s'>$N autre".$s." inscription".$s." pour la même adresse électronique&nbsp;:</p>" ;
		echo "<ul class='s'>\n" ;

		$req = "SELECT dossier.etat_dossier, dossier.id_dossier, dossier.date_inscrip,
			session.*,
			atelier.intitule,
			(SELECT id_imputation FROM imputations WHERE ref_dossier=id_dossier)
			AS id_imputation
			FROM atelier, session, dossier
			WHERE dossier.ref_session=session.id_session
			AND session.ref_atelier=atelier.id_atelier
			AND dossier.id_dossier IN ($liste_id_dossier)
			ORDER BY annee DESC, dossier.date_inscrip DESC" ;
		//echo $req ;

		$res = mysqli_query($cnx, $req) ;
		while ( $enr = mysqli_fetch_assoc($res) ) {
			echo "<li>" ;
			/*
			if ( $enr["evaluations"] == "Oui" ) {
				echo "<strong>".$enr["annee"]."</strong> - " ;
			}
			else {
				echo $enr["annee"]." - " ;
			}
			*/
			/*
			if ( $enr["imputations"] == "Oui" ) {
				echo "<strong>" ;
			}
			echo "<span class='".$etat_dossier_img_class[$enr["etat_dossier"]]."'>" ;
			echo $enr["etat_dossier"] ;
			echo "</span>" ;
			if ( $enr["id_imputation"] != "" ) {
				echo " <strong class='paye'>".LABEL_INSCRIT."</strong>" ;
			}
			if ( $enr["imputations"] == "Oui" ) {
				echo "</strong>" ;
			}
			if ( $enr["diplome"] == "Oui" ) {
				echo " <span class='diplome'>".LABEL_DIPLOME." ".$enr["anneed"]."</span>" ;
			}
			echo " - <a target='_blank' " ;
			echo "href='/candidatures/autre.php?id_dossier=".$enr["id_dossier"]."'>" ;
			echo $enr["intitule"] ;
			echo "</a>" ;
			echo " (".mysql2datenum($enr["date_inscrip"]).")" ;
			*/
			echo $enr["intitule"] ;
			echo " - " ;
			echo $enr["annee"] ;
			echo " - " ;
			echo "<a target='_blank' " ;
			echo "href='/inscrits/autre.php?id_dossier=".$enr["id_dossier"]."'>" ;
			echo $enr["intit_ses"] ;
			echo "</a>" ;
			echo "</li>\n" ;
		}
		echo "</ul>\n" ;
	}
	else {
		echo "<p class='s'>Pas d'autre inscription pour la même adresse électronique.</p>\n" ;
	}
	
	echo "</div></div>\n" ;
}

//
// Commentaires, et état du dossier
//

/*
Les commentaires des sélectionneurs peuvent avoir un champ ref_selecteur
correspondant à un sélectionneur qui a été supprimé de la base.
*/

// Retourne la liste (tableau) des sélectionneurs d'une formation
function liste_selecteurs($id_session, $cnx)
{
	$req = "SELECT codesel, nomsel, prenomsel
		FROM selecteurs, atxsel, atelier, session
		WHERE selecteurs.codesel=atxsel.id_sel
		AND atxsel.id_atelier=atelier.id_atelier
		AND atelier.id_atelier=session.ref_atelier
		AND session.id_session=$id_session" ;
	$res = mysqli_query($cnx, $req) ;
	$selecteurs = array() ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		$selecteurs[] = $enr ;
	}
	return $selecteurs ;
}

function comment_auf($T, $id, $cnx, $formulaire=TRUE)
{
	$req = "SELECT * FROM comment_auf
		WHERE ref_dossier=".$T["id_dossier"] ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;

	if ($formulaire  AND ( ($T["evaluations"]=="Oui") OR ($_SESSION["id"]=="00") ) )
	{
		echo "<tr class='noprint'>\n" ;
		echo "<th>AUF&nbsp;:</th>\n" ;
		echo "<td colspan='2'>" ;
		if ( intval($id) < 3 ) {
			echo "<input type='hidden' name='id_comment_auf' " ;
			echo "value='".$enr["id_comment_auf"]."' />" ;
			echo "<textarea name='comment_auf' cols='70' rows='4'>" ;
		}
		echo $enr["commentaire"] ;
		if ( intval($id) < 3 ) {
			echo "</textarea>" ;
		}
		echo "</td>\n" ;
		echo "</tr>\n" ;

		echo "<tr class='printonly'>\n" ;
		echo "<th>AUF&nbsp;:</th>\n" ;
		echo "<td>" ;
		echo $enr["commentaire"] ;
		echo "</td>\n" ;
		echo "</tr>\n" ;
	}
	else {
		echo "<tr>\n" ;
		echo "<th>AUF&nbsp;:</th>\n" ;
		echo "<td>" ;
		echo nl2br($enr["commentaire"]) ;
		echo "</td>\n" ;
		echo "</tr>\n" ;
	}
}

function comment_sel($T, $selecteurs, $id, $cnx, $formulaire=TRUE)
{
	global $etat_dossier_img_class ;

	$plusieurs = FALSE ;
	$nombre = count($selecteurs) ;
	if ( $nombre > 1 ) {
		$plusieurs = TRUE ;
	}

	foreach($selecteurs as $selecteur)
	{
		$req = "SELECT * FROM comment_sel
			WHERE ref_dossier=".$T["id_dossier"]."
			AND ref_selecteur=".$selecteur["codesel"] ;
		$res = mysqli_query($cnx, $req) ;
		$enr = mysqli_fetch_assoc($res) ;

		if ($formulaire  AND ( ($T["evaluations"]=="Oui") OR ($_SESSION["id"]=="00") ) )
		{
			// Formulaire, non imprimé
			echo "<tr class='noprint'>\n" ;
			echo "\t<th>".$selecteur["prenomsel"]." <span class='majuscules'>". $selecteur["nomsel"]."</span>&nbsp;:</th>\n" ;
			if ( $plusieurs ) { echo "<td>" ; } else { echo "<td colspan='2'>" ; }
			if ( $selecteur["codesel"] == $id ) {
				echo "<input type='hidden' name='id_comment_sel' " ;
				echo "value='".$enr["id_comment_sel"]."' />" ;
				echo "<textarea name='comment_sel' cols='70' rows='4'>" ;
			}
			echo $enr["commentaire"] ;
			if ( $selecteur["codesel"] == $id ) {
				echo "</textarea>" ;
			}
			echo "</td>\n" ;
			if ( $plusieurs ) {
				if ( $selecteur["codesel"] == $id ) {
					echo "<td style='vertical-align: top' class='".$etat_dossier_img_class[$enr["etat_sel"]]."'>" ;
					liste_etats("etat_sel", $enr["etat_sel"]) ;
					echo "</td>\n" ;
				}
				else {
					if ( $enr["etat_sel"]!="" ) { 
						echo "<td style='vertical-align: top' class='".$etat_dossier_img_class[$enr["etat_sel"]]."'>".$enr["etat_sel"]."</td>\n" ;
					} else { echo "<td class='nonetudie'>Non étudié</td>\n" ; }
				}
			}
			echo "</tr>\n" ;
	
			// Affiché seulement à l'impression
			echo "<tr class='printonly'>\n" ;
			echo "\t<th>".$selecteur["prenomsel"]." <span class='majuscules'>". $selecteur["nomsel"]."</span>&nbsp;:</th>\n" ;
			if ( $plusieurs ) { echo "<td>" ; } else { echo "<td colspan='2'>" ; }
			echo $enr["commentaire"] ;
			echo "</td>\n" ;
			if ( $plusieurs ) {
				// Si il n'y a pas de commentaire, il n'y a pas d'état, afficher Non étudié
				if ( $enr["etat_sel"]!="" ) { 
					echo "<td style='vertical-align: top' class='".$etat_dossier_img_class[$enr["etat_sel"]]."'>".$enr["etat_sel"]."</td>\n" ;
				} else { echo "<td class='nonetudie'>Non étudié</td>\n" ; }
			}
			echo "</tr>\n" ;
		}
		else {
			echo "<tr>\n" ;
			echo "\t<th>".$selecteur["prenomsel"]." <span class='majuscules'>". $selecteur["nomsel"]."</span>&nbsp;:</th>\n" ;
			if ( $plusieurs ) { echo "<td>" ; } else { echo "<td colspan='2'>" ; }
			echo nl2br($enr["commentaire"]) ;
			echo "</td>\n" ;
			if ( $plusieurs ) {
				// Si il n'y a pas de commentaire, il n'y a pas d'état, afficher Non étudié
				if ( $enr["etat_sel"]!="" ) { 
					echo "<td style='vertical-align: top' class='".$etat_dossier_img_class[$enr["etat_sel"]]."'>".$enr["etat_sel"]."</td>\n" ;
				} else { echo "<td class='nonetudie'>Non étudié</td>\n" ; }
			}
			echo "</tr>\n" ;
		}
	}
}

/*
      _                       _               
   __| |   ___    ___   ___  (_)   ___   _ __ 
  / _` |  / _ \  / __| / __| | |  / _ \ | '__|
 | (_| | | (_) | \__ \ \__ \ | | |  __/ | |   
  \__,_|  \___/  |___/ |___/ |_|  \___| |_|

$formulaire=TRUE : Afficher le formulaire du dossier (pour les sélectionneurs
	mais pas por les vieux dossiers).
$selectionneur=TRUE : Si sélectionneur, afficher autres candidatures
	(ou admin ou bourses ou CNF)
$modification=FALSE : Si (sélectionneur ou) candidat qui modifie son dossier
	utiliser les champs provenant de la BDD
	Sinon utiliser les variables provenant du formulaire en POST
	(Doit être TRUE pour un candidat qui modifie son dossier
$boutons=FALSE : TRUE pour afficher :
	- le bouton pour renvoyer un mail
	- le bouton pour joindre un fichier,
	- les liens pour supprimer les fichiers joints
*/
require_once("inc_pays.php") ;
require_once("inc_etat_dossier.php") ;
require_once("inc_formulaire_inscription.php") ;
function affiche_dossier($cnx, $T, $formulaire=TRUE, $selectionneur=TRUE, $modification=FALSE, $boutons=FALSE)
{
	// Un tableau des code => nom de pays
	// pour éviter plusieurs jointures supplémentaires pour l'affichage des pays d'un dossier
	// et pour tenir compte du fait que ces pays ne sont pas toujours renseignés
	$statiquePays = statiquePays($cnx) ;

	while ( list($key, $val) = each($T) ) {
		$T[$key] = sans_balise($val) ;
	}

	global $SECTION_CANDIDATURE ;
	global $ETAT_DOSSIER_IMG_CLASS;
	global $ETAT_DOSSIER;
	global $SITUATION ;
	global $IDENTITE ;


	if ( isset($_SESSION["authentification"]) AND ($_SESSION["authentification"] == "oui") )
	{
		echo "<div style='float: left; width: 50%;'>\n" ;
		echo "<div class='apercu'>\n" ;
	}
	echo "<div class='dossier_candidature'>\n" ;
	
	echo "<h1>" ;
	echo "<span style='font-size: smaller;'>" ;
	if ( $T["genre"] == "Homme" ) {
		echo "Monsieur" ;
	}
	else if ( $T["genre"] == "Femme" ) {
		echo "Madame" ;
	}
	echo "</span> " ;
	echo " <span class='majuscules'><em>" ;
	echo strtoupper($T["nom"]) ;
	echo "</em></span> " ;
	echo ucwords(strtolower($T["prenom"])) ;
	echo "</h1>\n" ;



//	echo "<div style='float:right'>\n" ;

	/*
	if ( $selectionneur ) {
		echo "<tr>\n" ;
		echo "<th><span class='chp'>&Eacute;tat du dossier&nbsp;:</span></th>\n" ;
		echo "<td><span class='".$etat_dossier_img_class[$T["etat_dossier"]]."'>" ;
		echo "<strong>".$T["etat_dossier"]."</strong>" ;
		if ( ($T["etat_dossier"]=="En attente") AND ($T["classement"]!="") ) {
			echo " (<strong>".$T["classement"]."</strong>)" ;
		}
		echo "</span>" ;
		// Imputation
		if ( isset($T["id_imputation1"]) AND ($T["id_imputation1"] != "") ) {
			echo " <strong class='paye'>".LABEL_INSCRIT."</strong>" ;
		}
		if ( isset($T["diplome"]) AND ($T["diplome"] == "Oui") ) {
			echo " <span class='diplome'>".LABEL_DIPLOME." ".$T["anneed"]."</span>" ;
		}
		echo "</td>\n" ;
		echo "</tr>\n" ;
	}
	*/

	// Lors du premier enregistrement du formulaire, la date d'inscription n'est pas affichée
	// mais elle est bien enregistrée.
	// Une requete pour l'afficher serait superflue.
	if ( isset($T["date_inscrip"]) )
	{
		echo "<table class='donnees' style='margin: 0;'>\n" ;
		echo "<tr>\n" ;
		echo "<th><span class='chp'>Pré-inscription&nbsp;:</span></th>\n" ;
		echo "<td> <span class='s'>le</span> ".mysql2date($T["date_inscrip"]) ;
		if ( $T["date_inscrip"] != $T["date_maj"] ) {
			echo ", <span class='s'>mise à jour le</span> ".mysql2date($T["date_maj"]) ;
		}
		echo "</td>\n" ;
		echo "</tr>\n" ;

		if ( isset($_SESSION["authentification"]) AND ($_SESSION["authentification"] == "oui") )
		{
			if ( isset($T["id_imputation"]) AND ($T["id_imputation"] != "") ) {
				echo "<tr>\n" ;
				echo "<th><span class='chp'>Inscription&nbsp;:</span></th>\n" ;
				echo "<td> <span class='s'>le</span> ".mysql2date($T["date_imput"]) ;
				if ( $T["date_imput"] != $T["date_maj_imput"] ) {
					echo ", <span class='s'>mise à jour le</span> ".mysql2date($T["date_maj_imput"]) ;
				}
				echo " - <strong class='paye'>".LABEL_INSCRIT."</strong>" ;
				if ( $T["etat_dossier"] != "0" ) {
					echo " - " ;
					echo "<span class='".$ETAT_DOSSIER_IMG_CLASS[$T["etat_dossier"]]."'>" ;
					echo $ETAT_DOSSIER[$T["etat_dossier"]] ."</span>" ;
				}
				echo "</td>\n" ;
				echo "</tr>\n" ;
			}
		}
		else
		{
			if ( $T["date_imput"] != "" ) {
				echo "<tr>\n" ;
				echo "<th><span class='chp'>Inscription&nbsp;:</span></th>\n" ;
				echo "<td> <span class='s'>le</span> ".mysql2date($T["date_imput"]) ;
				echo "</td>\n" ;
				echo "</tr>\n" ;
			}
		}
		?></table><?php
	}


	if ( $T["idmooc"] == "1" )
	{
		echo "<h2>".$SECTION_CANDIDATURE["1"]."</h2>\n" ;
		?><table class='donnees'><?php
		affiche_tr("id_mooc", $T) ;
		?></table><?php
	}
	
	
	echo "<h2>".$SECTION_CANDIDATURE["2"]."</h2>\n" ;
	// Traitement de la date de naissance en cas d'affichage à partir d'un POST
	if ( isset($T["naissance"]) AND ($T["naissance"] != "") ) {
		$tab["naissance"] = mysql2datealpha($T["naissance"]) ;
	}
	else {
		$tab["naissance"] = mysql2datealpha($T["annee_n"] ."-". $T["mois_n"] ."-". $T["jour_n"]) ;
	}
	// id_mooc et informations personnelles
	if ( $boutons ) {
		echo "<div style='float: right;'><strong>" ;
		echo "<input class='b' type='submit' name='submit' value='Envoyer un courriel' " ;
		echo "title='Envoyer un (nouveau) courriel contenant
votre numéro de dossier et votre mot de passe
à ".$T["email"]."' " ;
		echo "style='cursor: help;' />";
		echo "</strong></div>" ;
	}
	?><table class='donnees'><?php
	echo "<tr>\n\t<th><span class='chp'>" . intitule_champ("email") . "&nbsp;:</span></th>\n " ;
		echo "\t<td>" ;
		echo "<strong><a href='mailto:".$T["email"]."'>".$T["email"]."</a></strong>" ;
		echo "</td>\n</tr>\n" ;
	affiche_tr("naissance", $tab, TRUE) ;
	affiche_tr("lieu_naissance", $T, TRUE) ;
	echo "<tr>\n\t<th><span class='chp'>" . intitule_champ("pays_naissance") . "&nbsp;:</span></th>\n " ;
		echo "\t<td>" . refPays($T["pays_naissance"], $statiquePays) . "</td>\n</tr>\n" ;
	echo "<tr>\n\t<th><span class='chp'>" . intitule_champ("pays_nationalite") . "&nbsp;:</span></th>\n " ;
		echo "\t<td>" . refPays($T["pays_nationalite"], $statiquePays) . "</td>\n</tr>\n" ;
	echo "<tr>\n\t<th><span class='chp'>" . intitule_champ("pays_residence") . "&nbsp;:</span></th>\n " ;
		echo "\t<td>" . refPays($T["pays_residence"], $statiquePays) . "</td>\n</tr>\n" ;
	echo "<tr>\n\t<th><span class='chp'>" . intitule_champ("situation_actu") . "&nbsp;:</span></th>\n " ;
		echo "\t<td>" . $SITUATION[$T["situation_actu"]] ;
		if ( $T["sit_autre"] != "" ) {
			echo "<br />" . $T["sit_autre"] ;
		}
		echo "</td>\n</tr>\n" ;
	?></table><?php
	
	if ( $T["identite"] == "1" )
	{
		echo "<h2>".$SECTION_CANDIDATURE["3"]."</h2>\n" ;
		if ( isset($T["ident_date"]) AND ($T["ident_date"] != "") ) {
			$tab["ident_date"] = mysql2datealpha($T["ident_date"]) ;
		}
		else {
			$tab["ident_date"] = mysql2datealpha($T["annee_ident"] ."-". $T["mois_ident"] ."-". $T["jour_ident"]) ;
		}
		?><table class='donnees'><?php
		echo "<tr>\n\t<th><span class='chp'>" . intitule_champ("ident_nature") . "&nbsp;:</span></th>\n " ;
			echo "\t<td>" . $IDENTITE[$T["ident_nature"]] ;
			if ( $T["ident_autre"] != "" ) {
				echo "<br />" . $T["ident_autre"] ;
			}
			echo "</td>\n</tr>\n" ;
		affiche_tr("ident_numero", $T) ;
		affiche_tr("ident_date", $tab) ;
		affiche_tr("ident_lieu", $T) ;
		?></table><?php
	}
	
	/*
	include("../candidature/questions.php") ;
	if ( $nombre_questions > 0 )
	{
		echo "<h2>".$SECTION_CANDIDATURE["9"]."</h2>\n" ;

		$req = "SELECT * FROM reponse WHERE id_dossier=".$T["id_dossier"]."
			ORDER BY id_question" ;
	
		//echo $req ;
		$res = mysqli_query($cnx, $req) ;
	
		$i = 1 ;
		foreach($Questions as $question)
		{
			$ligne = mysqli_fetch_assoc($res) ;
			echo "<p><span class='chp'>" ;
			echo $question["texte_quest"] ;
			echo "</span><br />" ;
			echo nl2br($ligne["texte_rep"]) ;
			echo "</p>\n" ;
		}
	}
	*/
	
	if ( $T["pj"] == "1" )
	{
		echo "<h2>".$SECTION_CANDIDATURE["fichiers"]."</h2>\n" ;
	
		if ( $boutons)
		{
			echo "<div style='float: right'>" ;
			echo "<strong><input class='b' type='submit' name='submit' value='Joindre un fichier' /></strong>" ;
			echo "</div>\n" ;
		}


		echo "<div id='pj'>\n" ;
		$req = "SELECT * FROM pj WHERE ref_dossier=".$T["id_dossier"] ;
		$res = mysqli_query($cnx, $req) ;
		if ( @mysqli_num_rows($res) == 0 ) {
			echo "<p>Aucun fichier.</p>\n" ;
		}
		else {
			while ( $row=mysqli_fetch_assoc($res) )
			{
				if ( $row["poubelle"] == 0 )
				{
					if ( ($row["mime"] == "image/jpeg") OR ($row["mime"] == "image/png") )
					{
						$blank = "target='_blank'" ;
					}
					else
					{
						$blank = "" ;
					}

					echo "<div class='pj'>" ;
					$url_image = "/inscription/pj.php?id_pj=".$row["id_pj"]
						. "&ref_dossier=".$row["ref_dossier"]
						. "&fichier=".urlencode($row["fichier"]) ;
					$url_vignette = $url_image . "&taille=vignette" ;
					$url_voir = $url_image . "&action=voir" ;

					if ( ($row["mime"] == "image/jpeg") OR ($row["mime"] == "image/png") )
					{
						echo "<span class='vignette'>" ;
						// L'inscrit dans son formulaire
						if ( $_SERVER["PHP_SELF"] == "/inscription/inscription.php" ) {
							echo "<a title='Voir dans un nouvel onglet ou une nouvelle fenêtre' $blank href='".$url_voir."'>" ;
						}
						else {
							echo "<a class='box' title='Voir' data-pb-captionLink='".$row["fichier"]."' href='".$url_image."'>" ;
						}
						echo "<img src='$url_vignette' alt='".$row["fichier"]."' /></a>\n" ;
						echo "</span>" ;
					}

					echo "<a title='Télécharger' href='$url_image'>" ;
					echo $row["fichier"] ;
					echo "</a>" ;
					if ( ($row["mime"] == "image/jpeg") OR ($row["mime"] == "image/png") )
					{
						if ( $boutons ) {
							echo " <input type='submit' name='".$row["id_pj"]."' " ;
							echo "value='Supprimer ce fichier' />" ;
						}
						echo "<br />" ;
						echo intval($row["taille"]/1024.0)." <acronym title='kilo-octets'>ko</acronym> - " ;
						echo "<span class='s'>".$row["largeur"]."&times;".$row["hauteur"]." <acronym title='pixels'>px</acronym></span>" ;
					}
					else
					{
						echo " &nbsp; " ;
						echo intval($row["taille"]/1024.0)." <acronym title='kilo-octets'>ko</acronym>" ;
						if ( $boutons ) {
							echo " <input type='submit' name='".$row["id_pj"]."' " ;
							echo "value='Supprimer ce fichier' />" ;
						}
					}
					echo "</div>\n" ;
				}
				else {
					echo "<div class='pj'>" ;
					echo $row["fichier"] ;
					echo ", ". intval($row["taille"]/1024.0)."ko (".$row["largeur"]."&times;".$row["hauteur"].")" ;
					echo " <small><i>(supprimé)</i></small>" ;
					echo "</div>\n" ;
				}
			}
			echo "<div style='clear: both;'></div>\n" ;
		}
		echo "</div>\n\n\n" ;
	}

	echo "<h2>".$SECTION_CANDIDATURE["4"]."</h2>\n" ;
	echo "<p class='signature'>Je soussigné" ;
	if ( $T["genre"] == "Femme" ) {
		echo "e" ;
	}
	echo " " . $T["signature"] . "&nbsp;:</p>" ;
	echo "<ul class='signature'>\n" ;
	if ( $T["certifie"] == "1" ) {
		echo "<li>certifie sur l'honneur l'exactitude des informations ci-dessus,</li>\n" ;
	}
	if ( $T["accepte"] == "1" ) {
		echo "<li>accepte les <a class='extern' target='_blank' href='/conditions.php'>conditions générales</a>.</li>\n" ;
	}
	echo "</ul>\n" ;

	echo "</div>\n" ;
	if ( isset($_SESSION["authentification"]) AND ($_SESSION["authentification"] == "oui") )
	{
		echo "</div>\n" ;
		echo "</div>\n\n\n" ;
	}

	if	(
			isset($_SESSION["authentification"]) AND ($_SESSION["authentification"] == "oui")
		)
	{
		echo "<div style='float: right; width: 49%;'>\n" ;

		echo "<div class='encart'>\n" ;
		recherche_idem($cnx, $T["id_dossier"], $T["email"]) ;
		echo "</div>\n" ;

		//echo "<div class='encart'>\n" ;
		require_once("inc_historique.php") ;
		echo historiqueShow($cnx, $T["id_dossier"]) ;
		//echo "</div>\n" ;

		echo "</div>\n" ;
	}


}

function affiche_dossier_formulaire()
{
	//
	// Commentaires etat
	//
	if ( $selectionneur ) {

		// Sélectionneurs
		if ( intval($session["id"]) > 3 ) {
			if (
				in_array($T["id_session"], $session["tableau_promotions"])
				AND ( $formulaire == TRUE ) AND ( ($T["evaluations"]=="Oui") OR ($_SESSION["id"]=="00") ) 
			)
		{
				echo "<h2>".$SECTION_CANDIDATURE["commentaires"]."</h2>\n" ;
			}
			else {
				echo "<h2>Évaluations</h2>\n" ;
			}
		}
		else 
		{
			// CNF
			if ( ( $session["id"] != "02" ) AND ( $session["id"] != "03" ) AND ( $formulaire == TRUE ) AND ($T["evaluations"]=="Oui") ) {
				echo "<h2>".$SECTION_CANDIDATURE["commentaires"]."</h2>\n" ;
			}
			else {
				echo "<h2>Évaluations</h2>\n" ;
			}
		}


		require_once("inc_historique.php") ;
		echo historiqueShow($cnx, $T["id_dossier"]) ;

		echo "<form action='maj_dossier.php' method='post'>" ;
		echo "<input type='hidden' name='id_dossier' value='".$T["id_dossier"]."' />\n" ;
		echo "<input type='hidden' name='id_session' value='".$T["ref_session"]."' />\n" ;
		// Pour l'historique
		echo "<input type='hidden' name='evaluations' value='"
			. ( isset($T["evaluations"]) ? $T["evaluations"] : "" )
			. "' />\n" ;

		echo "<table class='formulaire' style='margin: 0;'>\n" ;
		comment_auf($T, $session["id"], $cnx, $formulaire) ;
		$selecteurs = liste_selecteurs($T["ref_session"], $cnx) ;
		comment_sel($T, $selecteurs, $session["id"], $cnx, $formulaire) ;

		if ($formulaire  AND ( ($T["evaluations"]=="Oui") OR ($_SESSION["id"]=="00") ) )
		{
			// Pas de changement d'état pour CNF ni SCAC
			// Ni si imputation
			if ( 
				($session["id"] != "02") AND ($session["id"] != "03")
				// FIXME bogue
				//AND ( isset($T["ref_dossier"]) AND ($T["ref_dossier"] == "") )
				AND ( ($T["ref_dossier"] == "") )
				)
			{
				echo "<tr class='noprint'>\n" ;
				echo "<th>&Eacute;tat du dossier&nbsp;:</th>\n" ;
				echo "<td colspan='2' class='".$etat_dossier_img_class[$T["etat_dossier"]]."'>" ;
				liste_etats("etat", $T["etat_dossier"]) ;
				echo "</td>\n" ;
				echo "</tr>\n" ;
			}
			// Transfert pour les sélectionneurs avec plusieurs promotions
			// ssi non imputé
			if ( 
				( intval($session["id"]) > 3 )
				AND in_array($T["id_session"], $_SESSION["transferable"])
				AND ( $session["transfert"] == "Oui" )
				AND ( count($session["tableau_promotions"]) > 1 )
				AND ( $T["ref_dossier"] == "" )
				)
			{
				$i = 0 ;
				foreach($session["tableau_promotions"] as $trans) {
					if ( $trans != $T["id_session"] ) {
						if ( $i == 0 ) {
							$liste_transferts = "$trans" ;
						}
						else {
							$liste_transferts .= ", $trans" ;
						}
						$i++ ;
					}
				}
				echo "<tr class='noprint'>\n" ;
				echo "<th>Transférer en&nbsp;:</th>\n" ;
				echo "<td colspan='2'>" ;
				echo "<select name='transfert'>\n" ;
				echo "<option value=''></option>\n" ;
				$req = "SELECT id_session, intitule, intit_ses
					FROM atelier, session
					WHERE session.ref_atelier=atelier.id_atelier
					AND session.id_session IN ($liste_transferts)
					ORDER BY niveau, intitule" ;
				$res = mysqli_query($cnx, $req) ;
				while ( $enr = mysqli_fetch_assoc($res) ) {
					echo "<option value='".$enr["id_session"]."'>" ;
					echo $enr["intitule"] ;
//					echo " (".$enr["intit_ses"].")" ;
					echo "</option>\n" ;
				}
				echo "</select>\n" ;
				echo "</td>\n" ;
				echo "</tr>\n" ;
			}

			// Ni commentaire, no changement etat pour SCAC
			if ( intval($session["id"]) != 3 ) {
				echo "<tr><td colspan='2' style='background: #fff'>" ;
				echo "<p class='noprint c'><input type='submit' style='font-weight: bold;' value='Enregistrer' /></p>\n" ;
				echo "</td></tr>" ;
			}
		}

		echo "</table>\n" ;

		echo "</form>\n" ;
	}




}
?>
