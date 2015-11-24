<?php
function controleCourrielValide($email)
{
	$email = trim($email) ;
	$valide = TRUE ;
	if	(
			( substr_count($email, "@") != 1 )
			OR ( substr_count($email, " ") > 0 )
			OR ( !ereg("^(.+)@(.+)\\.(.+)$", $email) )
		)
	{
		$valide = FALSE ;
	}
	if ( $valide ) {
		$tab = explode("@", $email) ;
		if ( !checkdnsrr($tab[1]) ) {
			$valide = FALSE ;
		}
	}
	return $valide ;
}

function generationMotDePasse()
{
	$chaine = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$mdp ="" ;
	srand((double)microtime()*1000000);
	for ($i=0 ; $i<8 ; $i++) {
		$mdp .= $chaine[rand()%strlen($chaine)];
	}
	return($mdp) ;
}

function controleMotDePasse($str)
{
	global $i18nErreurMdp ;
	if ( strlen($str) < 5 ) {
		return $i18nErreurMdp ;
	}
	else {
		return "" ;
	}
}
function controleUrlAffiliation($str)
{
	global $i18nErreurUrlAffiliationHttp ;
	global $i18nErreurUrlAffiliation ;
	$str = trim($str) ;
	if ( $str == "" ) {
		return "" ;
	}
	else {
		$erreurs = "" ;
		if ( substr($str, 0, 7) != "http://" ) {
			$erreurs = "<li>".$i18nErreurUrlAffiliationHttp."</li>\n" ;
		}
		else {
			$tr = strtr($str, '><"*', '    ') ;
			if ( substr_count($tr, " ") > 0 ) {
				$erreurs = "<li>".$i18nErreurUrlAffiliation."</li>\n" ;
			}
			if ( strlen($str) < 13 ) {
				$erreurs = "<li>".$i18nErreurUrlAffiliation."</li>\n" ;
			}
		}
		return $erreurs ;
	}
}

function controleAdhesion($cnx, $tab, $i18nChamps)
{
	global $i18nEmailsDifferents ;
	global $i18nChampsCategorie ;
	global $i18nChampsCategorieChercheurOui ;
	global $i18nChampsCategorieChercheurNon ;
	global $i18nChampsNiveau5 ;
	global $i18nChampsNiveauD ;
	global $i18nEmailParrainInvalide ;
	global $i18nEmailsParrainDifferents ;
	global $i18nPrecisionsNecessairesNon ;
	global $i18nPrecisionsNecessairesOui ;
	global $i18nParrainObligatoire ;
	global $i18nCourrielParrainObligatoire ;
	$erreurs = "" ;
	if ( !is_array($tab["langues"]) ) {
		$erreurs .= "<li>".i18nErreurObligatoire($i18nChamps["langues"]["libelle"])."</li>\n" ;
	}
	$erreurs .= controleObligatoire($tab["genre"], $i18nChamps["genre"]["libelle"]) ;
	$erreurs .= controleObligatoire($tab["nom"], $i18nChamps["nom"]["libelle"]) ;
	$erreurs .= controleObligatoire($tab["prenoms"], $i18nChamps["prenoms"]["libelle"]) ;
	if ( ($tab["jour"] == "") OR ($tab["mois"] == "") OR ($tab["annee"] == "") ) {
		$erreurs .= "<li>".i18nErreurObligatoire($i18nChamps["naissance"]["libelle"])."</li>\n" ;
	}
	// courriel
	$erreurEmailVide = controleObligatoire($tab["courriel"], $i18nChamps["courriel"]["libelle"]) ;
	if ( $erreurEmailVide != "" ) {
		$erreurs .= $erreurEmailVide ;
	}
	else {
		$erreurEmailInvalide = controleCourrielValide($tab["courriel"]) ;
		if ( ! $erreurEmailInvalide ) {
			$erreurs .= "<li>".i18nErreurCourrielInvalide($i18nChamps["courriel"]["libelle"])."</li>\n" ;
		}
		else {
			$erreurEmailDoublon = controleCourrielDoublon($cnx, $tab) ;
			if ( $erreurEmailDoublon != "" ) {
				$erreurs .= "<li>".$erreurEmailDoublon."</li>\n" ;
			}
			else {
				if ( $tab["courriel"] != $tab["courrielBis"] ) {
					$erreurs .= "<li>".i18nErreurCourrielsDifferents($i18nChamps["courriel"]["libelle"])."</li>\n" ;
				}
			}
		}
	}
	// nationalite
	$erreurs .= controleObligatoire($tab["nationalite"], $i18nChamps["nationalite"]["libelle"]) ;
	$erreurs .= controleObligatoire($tab["refPays"], $i18nChamps["refPays"]["libelle"]) ;
	$erreurs .= controleObligatoire($tab["diplome"], $i18nChamps["diplome"]["libelle"]) ;
	$erreurs .= controleObligatoire($tab["niveau"], $i18nChamps["niveau"]["libelle"]) ;
	$erreurs .= controleObligatoire($tab["fonction"], $i18nChamps["fonction"]["libelle"]) ;
	$erreurs .= controleObligatoire($tab["affiliation"], $i18nChamps["affiliation"]["libelle"]) ;
	$erreurs .= controleUrlAffiliation($tab["urlAffiliation"]) ;
	$erreurs .= controleObligatoire($tab["categorie"], $i18nChamps["categorie"]["libelle"]) ;
	$erreurs .= controleObligatoire($tab["discipline"], $i18nChamps["discipline"]["libelle"]) ;
	// Les publications ne sont pas obligatoires
	/*
	if ( $tab["categorie"] == $i18nChampsCategorieChercheurOui ) {
		$erreurs .= controleObligatoire($tab["publications"], $i18nChamps["publications"]["libelle"]) ;
	}
	*/
	if ( $tab["categorie"] == $i18nChampsCategorieChercheurNon )
	{
		if ( trim($tab["parrain"]) == "" ) {
			$erreurs .= "<li>".$i18nParrainObligatoire."</li>\n" ; ;
		}
		// courrielParrain
		$erreurEmailVide = "" ;
		if ( trim($tab["courrielParrain"]) == "" ) {
			$erreurEmailVide = $i18nCourrielParrainObligatoire ;
		}
		if ( $erreurEmailVide != "" ) {
			$erreurs .= $erreurEmailVide ;
		}
		else {
			$erreurEmailInvalide = controleCourrielValide($tab["courrielParrain"]) ;
			if ( ! $erreurEmailInvalide ) {
				$erreurs .= "<li>".i18nErreurCourrielInvalide($i18nChamps["courrielParrain"]["libelle"])."</li>\n" ;
			}
			else {
				if ( $tab["courrielParrain"] != $tab["courrielParrainBis"] ) {
					$erreurs .= "<li>".i18nErreurCourrielsDifferents($i18nChamps["courrielParrain"]["libelle"])."</li>\n" ;
				}
			}
		}
	}
	if ( ($tab["categorie"] == $i18nChampsCategorieChercheurNon) AND ($tab["pratique"] == "") AND ($tab["motivation"] == "") ) {
		$erreurs .= "<li>".$i18nPrecisionsNecessairesNon."</li>\n" ;
	}
	if ( ($tab["categorie"] == $i18nChampsCategorieChercheurOui)
		AND (trim($tab["pratique"]) == "")
		AND (trim($tab["publications"]) == "")
		AND ($tab["niveau"] != $i18nChampsNiveau5)
		AND ($tab["niveau"] != $i18nChampsNiveauD) ) {
		$erreurs .= "<li>".$i18nPrecisionsNecessairesOui."</li>\n" ;
	}
	
	return $erreurs ;
}

// $erreurs n'est ici pas uune string mais un array
// pour ne pas refaire la requete pour enregistrer e profil dans la session
// en cas d'identification reussie
function controleIdentification($cnx, $tab, $i18nChamps)
{
	global $i18nIdentifiantIncorrect ;
	global $i18nMotDePasseIncorrect ;
	$tabErr = array("erreurs" => "") ;

	if ( ! controleCourrielValide($tab["id"]) ) {
		$tabErr["erreurs"] .= "<p class='erreur'>"
			. $i18nIdentifiantIncorrect ."</p>\n" ;
		return $tabErr ;
	}
/*
	if ( trim($tab["mdp"]) == "" ) {
		$erreurs .= "<p class='erreur'>". $i18nMotDePasseIncorrect ."</p>\n" ;
		return $erreurs ;
	}
*/

	$req = "SELECT * FROM Individus WHERE courriel='".$tab["id"]."'
		AND droits NOT IN ('candidat','attente','refuse','ancien')" ;
	$res = mysqli_query($cnx, $req) ;
	$N = intval(mysqli_num_rows($res)) ;
	if ( $N == 0 ) {
		$tabErr["erreurs"] .= "<p class='erreur'>"
			. $i18nIdentifiantIncorrect ."</p>\n" ;
		return $tabErr ;
	}
	// Le contenu du champ courriel de Individus est cense etre unique
	else {
		$enr = mysqli_fetch_assoc($res) ;
		if ( md5($tab["mdp"]) != $enr["mdp"] ) {
			$tabErr["erreurs"] .= "<p class='erreur'>"
				.$i18nMotDePasseIncorrect."</p>\n" ;
			return $tabErr ;
		}
		else {
			$req = "SELECT langue FROM Langues WHERE refIndividu=".$enr["idIndividu"];
			$res = mysqli_query($cnx, $req) ;
			$enr["langues"] = array() ;
			while ($row = mysqli_fetch_assoc($res) ) {
				$enr["langues"][] = $row["langue"] ;
			}
			$tab = explode("-", $enr["naissance"]) ;
			$enr["jour"] = $tab[2] ;
			$enr["mois"] = $tab[1] ;
			$enr["annee"] = $tab[0] ;
			$enr["courrielBis"] = $enr["courriel"] ;
			$enr["courrielParrainBis"] = $enr["courrielParrain"] ;
			return $enr ;
		}
	}
}
	

// ---------------------------------------------------------
// Affichage
// ---------------------------------------------------------
function retourLigne($str)
{
	$str = trim($str) ;
	$str = str_replace("\r\n", "\n", $str) ;
	$str = str_replace("\f", "\n", $str) ;

	while ( substr_count($str, "\n\n\n") > 0 ) {
		$str = str_replace("\n\n\n", "\n\n", $str) ;
	}

	$tab = explode("\n\n", $str) ;
	$str = "" ;
	foreach($tab as $p) {
		$str .= "<div class='p'>".trim($p)."</div>\n" ;
	}
	return $str ;
}

function afficheChamp($tab, $cle, $html=FALSE)
{
	// $html = 0 (1 ligne pour libelle et valeur)
	//	  ou 1 (1 ligne pour libelle seul)
	// $retourLigne = 0, 1, 2
	global $i18nMembre ;
	global $i18nDeuxPoints ;

	// On supprime les balises HTML...
	$temp = $tab ;
	while ( list($key, $val) = each($temp) ) {
		if ( is_array($val) ) {
			$tab[$key] = $val ;
		}
		else {
			$tab[$key] = strip_tags($val) ;
		}
	}

	// p class='proche' pour un espacement plus faible
	$p = "<p class='proche'>" ;
	if	(
			($cle == "publications")
			OR ($cle == "pratique")
			OR ($cle == "motivation")
		)
	{
		$p = "<p>" ;
	}

	$champ  = "" ;
	if ( trim($tab[$cle]) != "" )
	{
		$val = $tab[$cle] ;
		$sval = surligne($val) ;

		if ( $html ) {
			$champ .= $p ;
			$champ .= "<div class='labelblock'>" ;
			$champ .= $i18nMembre[$tab["langue"]][$cle] ;
			//$champ .= $i18nDeuxPoints ;
			$champ .= "</div>\n" ;
			$champ .= retourLigne($sval) ;
			$champ .= "</p>\n\n" ;
		}
		else {
			$champ .= $p ;
			$champ .= "<span class='label'>" ;
			$champ .= $i18nMembre[$tab["langue"]][$cle] ;
			$champ .= $i18nDeuxPoints ;
			$champ .= "</span> \n" ;
			if ( $cle == "courriel" ) {
				$champ .= "<a href=\"mailto:".$val."\">".$sval."</a>" ;
			}
			else if ( $cle == "langue" ) {
				$champ .= $i18nMembre[$tab["langue"]][$val] ;
			}
			else if ( $cle == "langues" ) {
				foreach($val as $l) {
					$champ .= $i18nMembre[$tab["langue"]][$l].". " ;
				}
			}
			else if ( $cle == "naissance" ) {
				$champ .= afficheDate($val, $tab["langue"]) ;
			}
			else if ( $cle == "parrain" ) {
				$champ .= $val ;
				$champ .= ", <a href=\"mailto:".$tab["courrielParrain"]."\">".surligne($tab["courrielParrain"])."</a>" ;
			}
			else {
				$champ .= $sval ;
			}
			$champ .= "</p>\n\n" ;
		}
	}
	return $champ ;
}

?>
