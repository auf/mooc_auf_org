<?php
// Nombre d'imputations pour un numéro dossier
function imputation_nombre($cnx, $id_dossier)
{
	$req = "SELECT COUNT(id_imputation) AS N FROM imputations
		WHERE ref_dossier='".$id_dossier."'" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;
	$N = intval($enr["N"]) ;
	return $N ;
}

// Recupere les donnees du dossier a imputer dans un tableau
function imputation_dossier($cnx, $id_dossier)
{
	// Le tableau à retourner, y compris  avec les erreurs.
	$imputation = array() ;

	$N = imputation_nombre($cnx, $id_dossier) ;
	if ( $N > 0 ) {
		$imputation["erreur"] = "<p class='erreur'>Déjà imputé</p>" ;
	}

	$req = "SELECT intitule, universite,
		session.*, dossier.*, imputations.*
		FROM atelier, session, dossier
		LEFT JOIN imputations ON dossier.id_dossier=imputations.ref_dossier
		WHERE id_dossier=".$id_dossier."
		AND dossier.ref_session=session.id_session
		AND session.ref_atelier=atelier.id_atelier" ;
	$res = mysqli_query($cnx, $req) ;

	if ( mysqli_num_rows($res) != 1 ) {
		$imputation["erreur"] = "<p class='erreur'>Numéro de dossier inexistant</p>" ;
		return $imputation ;
	}

	$imputation = mysqli_fetch_assoc($res) ;

	$tab_naissance = explode("-", $imputation["naissance"]) ;
	$imputation["annee_n"] = $tab_naissance[0] ;
	$imputation["mois_n"] = $tab_naissance[1] ;
	$imputation["jour_n"] = $tab_naissance[2] ;

	return $imputation ;
}

function imputation_imputation($cnx, $id_imputation)
{
	$imputation = array() ;

	$req = "SELECT atelier.universite, atelier.intitule,
		session.*,
		dossier.*,
		imputations.*
		FROM imputations, dossier, session, atelier
		WHERE id_imputation=".$id_imputation."
		AND imputations.ref_dossier=dossier.id_dossier
		AND dossier.ref_session=session.id_session
		AND session.ref_atelier=atelier.id_atelier" ;
	$res = mysqli_query($cnx, $req) ;
	if ( mysqli_num_rows($res) != 1 ) {
		$imputation["erreur"] = "<p class='erreur'>Numéro d'imputation inexistant</p>" ;
		return $imputation ;
	}
	$imputation = mysqli_fetch_assoc($res) ;

	$tab_naissance = explode("-", $imputation["naissance"]) ;
	$imputation["annee_n"] = $tab_naissance[0] ;
	$imputation["mois_n"] = $tab_naissance[1] ;
	$imputation["jour_n"] = $tab_naissance[2] ;

	return $imputation ;
}


function formate_erreurs($erreurs)
{
	$string = "" ;
	if ( count($erreurs) != 0 )
	{
		$string  = "<ul class='erreur'>\n" ;
		foreach($erreurs as $erreur) {
			$string .= "<li>$erreur</li>\n" ;
		}
		$string .= "</ul>\n" ;
	}
	return $string ;
}

/*
function verif_imputation_correction($enr)
{
    if ( $enr["genre"] == "" ) {
        $erreurs[] = "Le champ «&nbsp;Genre&nbsp;» est obligatoire." ;
    }
    if ( $enr["nom"] == "" ) {
        $erreurs[] = "Le champ «&nbsp;Nom de famille&nbsp;» est obligatoire." ;
    }
    if ( $enr["prenom"] == "" ) {
        $erreurs[] = "Le champ «&nbsp;Prénoms&nbsp;» est obligatoire." ;
    }
    if (
        ( $enr["annee_n"] == "" )
        OR ( $enr["mois_n"] == "" )
        OR ( $enr["jour_n"] == "" )
        )
    {
        $erreurs[] = "Erreur dans la date de naissance." ;
    }
    if ( $enr["pays_nationalite"] == "" ) {
        $erreurs[] = "Le champ «&nbsp;Nationalité&nbsp;» est obligatoire." ;
    }

    return formate_erreurs($erreurs) ;
}
*/

function verif_imputation($cnx, $imputation)
{
	// Doublons ?
	$req = "SELECT * FROM imputations WHERE ref_dossier='".$imputation["id_dossier"]."'" ;
	$res = mysqli_query($cnx, $req) ;
	if ( mysqli_num_rows($res) != 0 ) {
		$erreurs[] = "Déjà inscrit !" ;
	}


	if ( $imputation["lieu_paiement"] == "" ) {
		$erreurs[] = "Le champ «&nbsp;Lieu de paiement&nbsp;» "
			."est obligatoire." ;
	}
	//
	// Montant
	//
	if ( $imputation["montant"] == "" ) {
		$erreurs[] = "Le champ «&nbsp;Montant acquitté&nbsp;» est obligatoire." ;
	}
	if (
		( $imputation["montant"] != "" ) AND
		!is_numeric($imputation["montant"]) 
		)
	{
		$erreurs[] = "Erreur dans le «&nbsp;Montant acquitté&nbsp;»." ;
	}
	if ( $imputation["monnaie"] == "" ) {
		$erreurs[] = "Le choix d'une monnaie pour le «&nbsp;Montant acquitté&nbsp;» est obligatoire." ;
	}


	if ( $imputation["lieu_examen"] == "" ) {
		$erreurs[] = "Le champ «&nbsp;Lieu d'examen&nbsp;» "
			."est obligatoire." ;
	}
	
	return formate_erreurs($erreurs) ;
}

require_once("inc_traitements_caracteres.php") ;
function calcule_imputation($tableau)
{
	$code  = $tableau["code_imputation"] ;
	$code .= "/" ;
	$code .= $tableau["id_dossier"] ;
	$code .= "/" ;
	$code .= substr($tableau["prenom"], 0, 1) ;
	$code .= "." ;
	$nom = strtr($tableau["nom"], array(
		"'" => "-",
		" " => "-"
	) ) ;
	$code .= $nom ;

	$code = strtoupper($code) ;
	$code = sansDiacritiques($code) ;

	$code = substr($code, 0, 36) ;

	return $code ;
}

function tarif_developpement($cnx, $T)
{
	$req = "SELECT developpement FROM ref_pays
		WHERE code='".$T["pays_residence"]."'" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;
	if ( $enr["developpement"] == "Elevé" ) {
		return $enr["developpement"] . ", " . $T["tarif1"] . " EUR" ;
	}
	else if ( $enr["developpement"] == "Intermédiaire" ) {
		return $enr["developpement"] . ", " . $T["tarif2"] . " EUR" ;
	}
	else if ( $enr["developpement"] == "Faible" ) {
		return $enr["developpement"] . ", " . $T["tarif3"] . " EUR" ;
	}
	else {
		return "<span class='erreur'>Niveau de développement et tarif indéterminés (".$T["pays_residence"].")</span>" ;
	}
}


require_once("inc_formulaire_inscription.php") ;
require_once("fonctions_formulaire_inscription.php") ;

// Première partie (non editable) d'un formulaire d'imputation
// $cnx pour tarif variable
function formulaire_imputation_session($cnx, $T)
{
	$form  = "" ;
	$form .= "<form method='post' action='imputation.php?".$_SERVER["QUERY_STRING"]."'>\n" ;
	$form .= "<table class='formulaire'>\n" ;
	$form .= "<tr>\n<td colspan='2' class='invisible'>" ;
		$form .= "<strong>Imputation pour :</strong>" ;
		$form .= "</td>\n</tr>\n" ;
	$form .= "<tr>\n" ;
		$form .= "<th>Institution&nbsp;:</th>\n" ;
		$form .= "<td>\n" ;
		$form .= $T["universite"] ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;
	$form .= "<tr>\n" ;
		$form .= "<th>Formation&nbsp;:</th>\n" ;
		$form .= "<td>" ;
		$form .= $T["intitule"] ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;
	$form .= "<tr>\n" ;
		$form .= "<th>Inscription&nbsp;:</th>\n" ;
		$form .= "<td>\n" ;
		$form .=  $T["annee"] . " - " . $T["intit_ses"] . "" ;
		$form .= "</td>\n" ;
	$form .= "</tr>\n" ;
	if ( $T["ects"] != "0" ) {
		$form .= "<tr>\n" ;
			$form .= "<th>ECTS&nbsp;:</th>\n" ;
			$form .= "<td>\n" ;
			$form .=  $T["ects"] ;
			$form .= "</td>\n" ;
		$form .= "</tr>\n" ;
	}
	// Tarif variable
	if ( ($T["tarif1"] != "0") AND ($T["tarif2"] != "0") AND ($T["tarif3"] != "0") ) {
		$form .= "<tr>\n" ;
			$form .= "<th>Tarif&nbsp;:</th>\n" ;
			$form .= "<td>\n" ;
			$form .= tarif_developpement($cnx, $T) ;
			$form .= "</td>\n" ;
		$form .= "</tr>\n" ;
	}
	// tarif unique
	else {
		$form .= "<tr>\n" ;
			$form .= "<th>Tarif unique&nbsp;:</th>\n" ;
			$form .= "<td>\n" ;
			$form .= $T["tarif"] ;
			$form .= " EUR" ;
			$form .= "</td>\n" ;
		$form .= "</tr>\n" ;
	}
	echo $form ;
}


require_once("inc_formulaire_inscription.php") ;
require_once("fonctions_formulaire_inscription.php") ;
require_once("inc_pays.php") ;
// Identite
/*
function formulaire_imputation_correction($cnx, $T, $affiche_erreurs=FALSE)
{
    global $GENRE ;
    global $JOUR ;
    global $MOIS ;
    global $ANNEE_NAISSANCE ;


    echo "<tr>\n<td colspan='2' class='invisible'>" ;
        echo "<br />" ;
        echo "<strong>Les informations suivantes doivent être vérifiées et corrigées si nécessaire.</strong>" ;
        if ( $affiche_erreurs ) {
            $erreurs = verif_imputation_correction($T) ;
            echo $erreurs ;
        }
    echo "</td>\n</tr>\n" ;

    echo "<tr>\n<th>" ;
    libelle("genre") ;
    echo "</th>\n<td>" ;
    liste_der1($GENRE, "genre", $T["genre"]) ;
    echo "</td>\n</tr>\n" ;

    echo "<tr>\n<th>" ;
    libelle("nom") ;
    echo "</th>\n<td>" ;
    inputtxt("nom", strtoupper($T["nom"]), 35, 50) ;
    echo "</td>\n</tr>\n" ;

    echo "<tr>\n<th>" ;
    libelle("prenom") ;
    echo "</th>\n<td>" ;
    inputtxt("prenom", ucwords(strtolower($T["prenom"])), 40, 100) ;
    echo "</td>\n</tr>\n" ;

    echo "<tr>\n<th>" ;
    libelle("naissance") ;
    echo "</th>\n<td>" ;
    liste_der1($JOUR, "jour_n", $T["jour_n"]) ;
    echo " / " ;
    liste_der2($MOIS, "mois_n", $T["mois_n"]) ;
    echo " / " ;
    liste_der1($ANNEE_NAISSANCE, "annee_n", $T["annee_n"]) ;
    echo "</td>\n</tr>\n" ;

    echo "<tr>\n<th>" ;
    libelle("pays_nationalite") ;
    echo "</th>\n<td>" ;
    echo selectPays($cnx, "pays_nationalite", $T["pays_nationalite"]) ;
    echo "</td>\n</tr>\n" ;

	echo "<tr>\n<td colspan='2' class='invisible'>" ;
		echo "<br />" ;
		echo "Seuls les individus pré-inscrits peuvent modifier leur dossier d'inscription,<br />
et ils ne peuvent le faire que jusqu'à leur imputation (c'est à dire jusqu'à leur inscription).<br />
Il convient donc de <strong>vérifier leur dossier avec eux avant</strong> de procéder à leur imputation." ;
	echo "</td>\n</tr>\n" ;
}
*/

require_once("inc_form_select.php") ;
require_once("inc_cnf.php") ;
require_once("inc_monnaie.php") ;
require_once("inc_devises.php") ;

// Paiement
function formulaire_imputation_2($cnx, $T, $affiche_erreurs=FALSE)
{
	global $CNF ;
	global $MONNAIE ;

	echo "<tr>\n<td colspan='2' class='invisible'>" ;
		echo "<br />" ;
		echo "<strong>Paiement (en une seule fois)&nbsp;:</strong>" ;
		if ( $affiche_erreurs ) {
			$erreurs = verif_imputation($cnx, $T) ;
			echo $erreurs ;
		}
	echo "</td>\n</tr>\n" ;
	
	echo "<tr>\n<th>" ;
	echo "<label for='lieu_paiement'>Lieu de paiement&nbsp;:</label>" ;
	echo "</th>\n<td>" ;
	form_select_1($CNF, "lieu_paiement", ( isset($T["lieu_paiement"]) ? $T["lieu_paiement"] : "" )) ;
	echo "</td>\n</tr>\n" ;
	
	echo "<tr>\n<th>" ;
	echo "Montant acquitté&nbsp;:</label>" ;
	echo "</th>\n<td>" ;
	echo "<input type='text' name='montant' id='montant' size='12'" ;
	echo " value=\"".$T["montant"]."\" /> " ;
	echo selectDevise($cnx, "monnaie", ( isset($T["monnaie"]) ? $T["monnaie"] : "" )) ;
	//form_select_2($MONNAIE, "monnaie", ( isset($T["monnaie"]) ? $T["monnaie"] : "" )) ;
	echo "</td>\n</tr>\n" ;


	echo "<tr>\n<th>" ;
	echo "<label for='lieu_examen'>Lieu d'examen&nbsp;:</label>" ;
	echo "</th>\n<td>" ;
	form_select_1($CNF, "lieu_examen", ( isset($T["lieu_examen"]) ? $T["lieu_examen"] : "" )) ;
	echo "</td>\n</tr>\n" ;


	echo "<tr>\n<td colspan='2'>" ;
	echo "<p class='c'><input type='submit' name='bouton' " ;
	if ( isset($T["id_imputation"]) ) {
		echo "value='Modifier' " ;
	}
	else {
		echo "value='Enregistrer' " ;
	}
	echo "style='font-weight: bold;' /></p>" ;
	echo "</td>\n</tr>\n" ;
	echo "</table>\n" ;
	echo "</form>\n" ;
}

function formulaire_imputation($cnx, $T, $affiche_erreurs=FALSE)
{
	echo "<div style='margin: 0 auto;'>\n" ;
	formulaire_imputation_session($cnx, $T) ;
//	formulaire_imputation_correction($cnx, $T, $affiche_erreurs) ;
	formulaire_imputation_2($cnx, $T, $affiche_erreurs) ;
	echo "</div>\n" ;
}

require_once("inc_date.php") ;
require_once("inc_pays.php") ;
// $cnx pour statiquePays()
function attestation($cnx, $enr)
{
	$statiquePays = statiquePays($cnx) ;

	$attestation  = "" ;
	
	$attestation .= "<div class='c'><img src='/img/AUF.png' width='130' height='93' alt='Agence universitaire de la Francophonie' /></div>\n" ;
	$attestation .= "<h2 style='font-weight: normal;' class='c'>" ;
	$attestation .= "Agence Universitaire de la Francophonie<br />\n" ;
	$attestation .= "MOOC\n" ;
	$attestation .= "</h2>\n" ;
	
	$attestation .= "<table class='imput'>\n" ;
	
	$attestation .= "<tbody>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Université&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$enr["universite"]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Formation&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$enr["intitule"]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Inscription&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$enr["annee"]." - ".$enr["intit_ses"]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	// Tarif variable
	if ( ($enr["tarif1"] != "0") AND ($enr["tarif2"] != "0") AND ($enr["tarif3"] != "0") )
	{
		$attestation .= "<tr>\n" ;
			$attestation .= "\t<th>Tarif&nbsp;:</th>\n" ;
			$attestation .= "\t<td>" ;
			$attestation .= tarif_developpement($cnx, $enr) ;
			$attestation .= "<br /><br /></td>\n" ;
		$attestation .= "</tr>\n" ;
	}
	// Tarif unique
	else
	{
		$attestation .= "<tr>\n" ;
			$attestation .= "\t<th>Tarif &nbsp;:</th>\n" ;
			$attestation .= "\t<td>".$enr["tarif"]." EUR<br /><br /></td>\n" ;
		$attestation .= "</tr>\n" ;
	}
	$attestation .= "</tbody>\n" ;
	
	
	
	
	$attestation .= "<tbody>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Genre&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$enr["genre"]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Nom de famille&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".strtoupper($enr["nom"])."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Prénoms&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".ucwords(strtolower($enr["prenom"]))."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Date de naissance&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".mysql2datealpha($enr["naissance"])."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Pays de naissance&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$statiquePays[$enr["pays_naissance"]]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Nationalité&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$statiquePays[$enr["pays_nationalite"]]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Pays de résidence&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$statiquePays[$enr["pays_naissance"]]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "</tbody>\n" ;
	
	
	$attestation .= "<tbody>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Imputation comptable&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$enr["imputation"]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "</tbody>\n" ;
	
	$attestation .= "<tbody>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Date de création&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".mysql2datealpha($enr["date_imput"])."</td>\n" ;
	$attestation .= "</tr>\n" ;
	if ( $enr["date_imput"] != $enr["date_maj_imput"] )
	{
		$attestation .= "<tr>\n" ;
			$attestation .= "\t<th>Date de modification&nbsp;:</th>\n" ;
			$attestation .= "\t<td>".mysql2datealpha($enr["date_maj_imput"])."</td>\n" ;
		$attestation .= "</tr>\n" ;
	}
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Lieu de paiement&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$enr["lieu_paiement"]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "</tr>\n" ;
		$attestation .= "\t<th>Montant acquitté&nbsp;:</span></th>\n" ;
		$attestation .= "\t<td>".floatval($enr["montant"]). " ".$enr["monnaie"]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "<tr>\n" ;
		$attestation .= "\t<th>Lieu d'examen&nbsp;:</th>\n" ;
		$attestation .= "\t<td>".$enr["lieu_examen"]."</td>\n" ;
	$attestation .= "</tr>\n" ;
	$attestation .= "</tbody>\n" ;
	$attestation .= "</table>\n" ;

	return $attestation ;
}

?>
