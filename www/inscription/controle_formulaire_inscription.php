<?php

function messageDoublon($id_dossier, $email)
{
	$msgd  = "<div class='erreur'>\n" ;
	$msgd .= "<p>Vous avez déjà déposé un dossier d'inscription pour cette formation.</p>\n" ;
	$msgd .= "<p>Vous pouvez le <a target='_blank' href='/inscription/'>mettre à jour</a> "
	. "en utilisant le numéro de dossier et le mot de passe qui ont été envoyés à&nbsp;:<br />"
	. "<code>" . $email."</code>.</p>\n" ;
	$msgd .= "<p>Si vous ne disposez pas de votre mot de passe :</p>\n" ;
	$msgd .= "<ol>"
	. "<li>Commencez par vérifier que le message qui vous a été envoyé n'a pas été considéré comme un pourriel "
		. "(<i>spam</i>) par votre messagerie. "
		. "<span class='normal'>(Expéditeur de ce message : <code>".EMAIL_FROMNAME." &lt;".EMAIL_FROM."&gt;</code>)</span></li>"
	. "<li>Sinon, si vous avez perdu votre mot de passe, envoyez un courriel à <a href='mailto:".EMAIL_CONTACT."'>".EMAIL_CONTACT."</a> "
		. "en précisant les informations suivantes :\n"
//		. "<div class='normal'>- Vos nom, prénom et date de naissance</div>\n"
		. "<div class='normal'>- Adresse électronique : <code>".$email."</code></div>\n"
		. "<div class='normal'>- Numéro de dossier : <code>".$id_dossier."</code></div>\n"
//		. "Précisez aussi"
		. "</li>"
	. "</ol>\n" ;
	$msgd .= "</div>" ;

	return $msgd ;
}



$erreur_doublon = FALSE ;
$erreur_saisie = FALSE ;
$erreurs = FALSE ;
$erreur_saisie1 = $erreur_saisie2 = $erreur_saisie3 = $erreur_saisie4 = "" ;

$message_doublon = "" ;
$message_erreur = "" ;

//
// Controle des doublons
// Pas pour les mises à jour
//
if ( $_POST['formulaire'] != "maj" ) 
{
	//$date_naissance = $T["annee_n"] . "-" . $T["mois_n"] . "-" . $T["jour_n"] ;
	/*
	if ( ($tabInscription["idmooc"] == "1") AND isset($T["id_mooc"]) AND ($T["id_mooc"]!="") )
	{
		$req_d = "SELECT id_dossier, id_mooc FROM dossier
			WHERE ref_session=$id_session
			AND (email='".mysqli_real_escape_string($cnx, trim($T["email"]))."'
				OR id_mooc='".mysqli_real_escape_string($cnx, trim($T["id_mooc"]))."')" ;
		$res_d = mysqli_query($cnx, $req_d) ;
		$nbre_d = mysqli_num_rows($res_d) ;
		if ( intval($nbre_d) != 0 )
		{
			$enr_d = mysqli_fetch_assoc($res_d) ;
			$id_dossier_doublon = $enr_d["id_dossier"] ;
			$id_mooc_doublon = $enr_d["id_mooc"] ;
			$message_doublon = messageDoublon($id_dossier_doublon, $T["email"]) ;
			$erreur_doublon = TRUE ;
		}
	}
	else
	{
	*/
		$req_d = "SELECT id_dossier, id_mooc FROM dossier
			WHERE ref_session=$id_session
			AND email='".mysqli_real_escape_string($cnx, trim($T["email"]))."'" ;
		$res_d = mysqli_query($cnx, $req_d) ;
		$nbre_d = mysqli_num_rows($res_d) ;
		if ( intval($nbre_d) != 0 )
		{
			$enr_d = mysqli_fetch_assoc($res_d) ;
			$id_dossier_doublon = $enr_d["id_dossier"] ;
			$id_mooc_doublon = $enr_d["id_mooc"] ;
			$message_doublon = messageDoublon($id_dossier_doublon, $T["email"]) ;
			$erreur_doublon = TRUE ;
		}
	/*
	}
	*/
}

//
// Identifiant MOOC
//
if ( $tabInscription["idmooc"] == "1" )
{
	if ( trim($T["id_mooc"]) == "" ) {
		$erreur_saisie1 .= obligatoire("id_mooc") ;
	}
}
// Pot de miel
if ( trim($T["pwd_mooc"]) != "" ) {
	$erreur_saisie1 .= obligatoire("pwd_mooc", "n'est pas valide") ;
}
//
// 2. Informations personnelles
//
if ( trim($T["email"]) == "" ) {
	$erreur_saisie2 .= obligatoire("email") ;
}
else if ( !filter_var($T["email"], FILTER_VALIDATE_EMAIL) ) {
	$erreur_saisie2 .= obligatoire("email", "n'est pas valide") ;
}
else if ( $T["email"] != $T["verif_email"] ) {
	$erreur_saisie2 .= obligatoire("email", "ne contient pas deux fois la même adresse") ;
}
if ( !isset($T["genre"]) OR ($T["genre"] == "") ) {
	$erreur_saisie2 .= obligatoire("genre") ;
}
if ( trim($T["nom"]) == "" ) {
	$erreur_saisie2 .= obligatoire("nom") ;
}
if ( trim($T["prenom"]) == "" ) {
	$erreur_saisie2 .= obligatoire("prenom") ;
}
if ( ($T["jour_n"]=="") OR ($T["mois_n"]=="") OR ($T["annee_n"]=="") ) {
	$erreur_saisie2 .= obligatoire("naissance") ;
}
if ( trim($T["lieu_naissance"]) == "" ) {
	$erreur_saisie2 .= obligatoire("lieu_naissance") ;
}
if ( $T["pays_naissance"] == "" ) {
	$erreur_saisie2 .= obligatoire("pays_naissance") ;
}
if ( $T["pays_nationalite"] == "" ) {
	$erreur_saisie2 .= obligatoire("pays_nationalite") ;
}
if ( $T["pays_residence"] == "" ) {
	$erreur_saisie2 .= obligatoire("pays_residence") ;
}
if ( $T["situation_actu"] == "" ) {
	$erreur_saisie2 .= obligatoire("situation_actu") ;
}
if ( ($T["situation_actu"] == "Autre") AND ( trim($T["sit_autre"])=="") ) {
	$erreur_saisie2 .= obligatoire("sit_autre") ;
}
//
// 3. Pièce d'identité
//
if ( $tabInscription["identite"] == "1" )
{
	if ( $T["ident_nature"] == "" ) {
		$erreur_saisie3 .= obligatoire("ident_nature") ;
	}
	if ( ($T["ident_nature"] == "Autre") AND ( trim($T["ident_autre"])=="") ) {
		$erreur_saisie3 .= obligatoire("ident_autre") ;
	}
	if ( trim($T["ident_numero"]) == "" ) {
		$erreur_saisie3 .= obligatoire("ident_numero") ;
	}
	if ( ($T["jour_ident"]=="") OR ($T["mois_ident"]=="") OR ($T["annee_ident"]=="") ) {
		$erreur_saisie3 .= obligatoire("ident_date") ;
	}
	if ( trim($T["ident_lieu"]) == "" ) {
		$erreur_saisie3 .= obligatoire("ident_lieu") ;
	}
}
//
// Signature
//
if ( trim($T["signature"]) == "" ) {
	$erreur_saisie4 .= "<li>Vous devez signer votre candidature
    <span class='erreur_champ'>(nom et prénom)</span>.</li>\n" ;
}
else {
	if ( stristr($T["signature"], $T["nom"]) === FALSE ) {
		$erreur_saisie4 .= "<li>Votre signature doit contenir votre <span class='erreur_champ'>nom de famille</span>.</li>\n" ;
	}
	if ( stristr($T["signature"], $T["prenom"]) === FALSE ) {
		$erreur_saisie4 .= "<li>Votre signature doit contenir votre <span class='erreur_champ'>prénom</span>.</li>\n" ;
	}
}

if ( !isset($T["certifie"]) OR ($T["certifie"] != "1") ) {
	$erreur_saisie4 .= "<li>Vous devez certifier sur l'honneur l'exactitude des informations ci-dessus (en cochant la case). </li>\n" ;
}
if ( !isset($T["accepte"]) OR ($T["accepte"] != "1") ) {
	$erreur_saisie4 .= "<li>Vous devez accepter les conditions générales (en cochant la case). </li>\n" ;
}





if ( ($erreur_saisie1 != "") OR ($erreur_saisie2 != "") OR ($erreur_saisie3 != "") OR ($erreur_saisie4 != "") ) {
	$erreurs = TRUE ;
	$erreur_saisie = TRUE ;
}
if ( $erreur_doublon ) {
	$erreurs = TRUE ;
}

if ( $erreurs ) {
	if ( $erreur_doublon ) {
		$message_erreur .= $message_doublon ;
	}
	else if ( $erreur_saisie ) {
		$message_erreur .= "<div class='erreur'>\n<p>"
		. "Votre inscription ne peut pas être enregistrée car elle est "
		. "incomplète, ou contient une ou plusieurs erreurs "
		. "détaillées ci-dessous, dans chaque section du formulaire.</p>\n</div>\n" ;
	}
}
?>
