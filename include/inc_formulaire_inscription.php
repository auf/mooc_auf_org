<?php

$SECTION_CANDIDATURE = array(
	"1" => "Identifiant d'inscription au MOOC",
	"2" => "Informations personnelles",
	"3" => "Pièce d'identité",
	"4" => "Signature",
	"fichiers" => "Fichiers joints",
	"commentaires" => "Évaluations<span class='noprint'>, état du dossier</span>",
) ;

$CANDIDATURE = array(
// 1
	"id_mooc" => array(
		"Identifiant&nbsp;MOOC",
		"Identifiant MOOC"),
	"pwd_mooc" => array(
		"Mot de passe MOOC",
		""),
// 2.
	"email" => array(
		"Adresse&nbsp;électronique",
		"Adresse électronique"),
	"genre" => array(
		"Genre",
		""),
	"nom" => array(
		"Nom de famille",
		""),
	"prenom" => array(
		"Prénom(s)",
		""),
	"naissance" => array(
		"Date de naissance",
		""),
	"lieu_naissance" => array(
		"Lieu de naissance",
		""),
	"pays_naissance" => array(
		"Pays de naissance",
		""),
	"pays_nationalite" => array(
		"Nationalité",
		""),
	"pays_residence" => array(
		"Pays de résidence",
		""),
	"situation_actu" => array(
		"Situation actuelle"),
		"",
	"sit_autre" => array(
		"Si «&nbsp;Autre&nbsp;», précisez",
		""),
// 3
	"ident_nature" => array(
		"Nature",
		"Nature"),
	"ident_autre" => array(
		"Si «&nbsp;Autre&nbsp;», précisez",
		""),
	"ident_numero" => array(
		"Numéro",
		""),
	"ident_date" => array(
		"Date&nbsp;de&nbsp;délivrance",
		"Date de délivrance"),
	"ident_lieu" => array(
		"Lieu de délivrance",
		""),
// Utilisé dans les exports
// Autres champs
// dont champs de la table imputations
	"age" => array(
		"Age au début de la formation"),

	"id_dossier" => array(
		"Numéro de dossier"),
	"date_inscrip" => array(
		"Date de dépôt de l'inscription"),
	"date_maj" => array(
		"Date de mise à jour de l'inscription"),

	"id_imputation" => array(
		"Numéro de l'imputation"),
	"date_imput" => array(
		"Date d'imputation"),
    "date_maj_imput" => array(
		"Date de mise à jour de l'imputation ou du lieu d'examen"),
    "lieu_paiement" => array(
		"Lieu de paiement"),
    "montant" => array(
		"Montant"),
    "monnaie" => array(
		"Monnaie"),
    "imputation" => array(
		"Imputation"),
    "lieu_examen" => array(
		"Lieu d'examen"),

    "etat_dossier" => array(
		"Résultat"),
    "date_maj_etat" => array(
		"Date de mise à jour du résultat"),

    "institution" => array(
		"Institution principale"),
    "ref_institution" => array(
		"Numéro de l'institution principale"),
    "universite" => array(
		"Institution(s)"),
    "groupe" => array(
		"Domaine"),
    "ref_discipline" => array(
		"Numéro de la discipline"),
    "intitule" => array(
		"Formation"),
    "intit_ses" => array(
		"Inscription"),
    "id_session" => array(
		"Numéro de l'inscription"),
	/*
    "" => array(
		""),
    "" => array(
		""),
	*/
) ;

function libelc($champ)
{
	global $CANDIDATURE ;
	if ( $CANDIDATURE[$champ][1] != "" ) {
		return str_replace("&nbsp;", " ", strip_tags($CANDIDATURE[$champ][1])) ;
	}
	else {
		return str_replace("&nbsp;", " ", strip_tags($CANDIDATURE[$champ][0])) ;
	}
}
function longueurc($champ)
{
	global $CANDIDATURE ;
	return $CANDIDATURE[$champ][2] ;
}

$GENRE = array(
	"",
	"Femme",
	"Homme",
) ;
$SITUATION = array(
	""            => "",
	"Etudiant"    => "Étudiant(e)",
	"Enseignant"  => "Enseignant(e) ou formateur  (secteur public et privé)",
	"Public"      => "Employé(e) (fonctionnaire, contractuel ou stagiaire) du secteur public ou de la fonction publique",
	"Privé"       => "Employé(e) (ou stagiaire) du secteur privé (services, industries...)",
	"Associatif"  => "Employé(e) (ou stagiaire) d'une association, ONG, coopération ou organisation internationale",
//	"Associatif"  => "Employé(e) (ou stagiaire) d'une association, d'une ONG, d'une coopération ou d'une organisation internationale",
	"Indépendant" => "Travailleur indépendant",
	"Retraite"    => "Retraité(e)",
	"Chomage"     => "En recherche d'emploi",
	"Autre"       => "Autre",
) ;
$IDENTITE = array(
	""          => "",
	"Passeport" => "Passeport",
	"Nationale" => "Carte nationale d'identité",
	"Autre"     => "Autre",
) ;
$JOUR = array(
	"",
	"01","02","03","04","05","06","07","08","09","10",
	"11","12","13","14","15","16","17","18","19","20",
	"21","22","23","24","25","26","27","28","29","30",
	"31") ;

$MOIS = array(
	"" => "",
	"01" => "janvier",
	"02" => "février",
	"03" => "mars",
	"04" => "avril",
	"05" => "mai",
	"06" => "juin",
	"07" => "juillet",
	"08" => "août",
	"09" => "septembre",
	"10" => "octobre",
	"11" => "novembre",
	"12" => "décembre",
) ;

$ANNEE_COURANTE = intval(date("Y", time())) ;

$ANNEE_NAISSANCE = array() ;
$ANNEE_NAISSANCE[] = "" ;
for ( $i = ($ANNEE_COURANTE - 90) ; $i <= ($ANNEE_COURANTE - 10) ; $i++ ) {
	$ANNEE_NAISSANCE[] = $i ;
}

$ANNEE_DELIVRANCE = array() ;
$ANNEE_DELIVRANCE[] = "" ;
for ( $i = ($ANNEE_COURANTE - 30) ; $i <= $ANNEE_COURANTE ; $i++ ) {
	$ANNEE_DELIVRANCE[] = $i ;
}

$CONSIGNES = "
<div id='consignesForm'>
<p>Complétez le formulaire d'inscription ci-dessous, et cliquez sur le bouton «&nbsp;<strong>Valider</strong>&nbsp;» en bas de page.<br />
Vous pourrez ensuite, le cas échéant, joindre un ou plusieurs fichiers à votre dossier d'inscription dans la page suivante.</p>
</div>
";

// Consignes par défaut
$CONSIGNES_ID_MOOC = "Indiquez ici l'identifiant d'inscription au MOOC" ;
$CONSIGNES_IDENTITE = "Une pièce d'identité officielle vous sera demandée pour passer l'examen de certification." ;
$CONSIGNES_PJ = "" ;
?>
