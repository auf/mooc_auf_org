<?php
include("inc_session.php") ;

include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_date.php") ;

include("inc_cnf.php") ;

// Export
if ( isset($_SESSION["filtres"]["imputations"]["action"]) AND ($_SESSION["filtres"]["imputations"]["action"] == "Exporter") )
{
	unset($_SESSION["filtres"]["imputations"]["action"]) ;

	define("FORMAT_REEL",   1); // #,##0.00
	define("FORMAT_ENTIER", 2); // #,##0
	define("FORMAT_TEXTE",  3); // @

	$cfg_formats[FORMAT_ENTIER] = "FF0";
	$cfg_formats[FORMAT_REEL]   = "FF2";
	$cfg_formats[FORMAT_TEXTE]  = "FG0";

	$champs = array(
		array('groupe', 'Domaine', FORMAT_TEXTE, 'L', 20),						//  0
		array('id_session', 'Id promotion', FORMAT_TEXTE, 'L', 8),				//  1
		array('intitule', 'Formation', FORMAT_TEXTE, 'L', 36),					//  2
		array('intit_ses', 'Promotion', FORMAT_TEXTE, 'L', 12),					//  3
		array('universite', 'Université', FORMAT_TEXTE, 'L', 21),				//  4
		array('institution', 'Etablissement', FORMAT_TEXTE, 'L', 30),			//  5
		array('id_institution', 'code_etab', FORMAT_TEXTE, 'L', 8),				//  6
		array('genre', 'Genre', FORMAT_TEXTE, 'L', 8),							//  7
		array('nom', 'Nom de famille', FORMAT_TEXTE, 'L', 15),					//  8
		array('prenom', 'Prénoms', FORMAT_TEXTE, 'L', 15),						//  9
		array('naissance', 'Naissance', FORMAT_TEXTE, 'L', 10),					// 10
		array('age', 'Age', FORMAT_TEXTE, 'L', 3),								// 11
		array('pays_nationalite', 'Nationalité', FORMAT_TEXTE, 'L', 10),		// 12
		array('lieu_paiement', "Lieu de paiement", FORMAT_TEXTE, 'L', 10),		// 13
		array('montant', 'Montant', FORMAT_TEXTE, 'L', 7),						// 14
		array('monnaie', 'Monnaie', FORMAT_TEXTE, 'L', 7),						// 15
		array('lieu_examen', "Lieu d'examen", FORMAT_TEXTE, 'L', 10),			// 16
		array('imputation', 'Imputation comptable', FORMAT_TEXTE, 'L', 36),		// 17
		array('date_imput', "Date de création", FORMAT_TEXTE, 'L', 10),			// 18
		array('date_maj_imput', "Date de modification", FORMAT_TEXTE, 'L', 10),	// 19
		array('commentaire', "Commentaire", FORMAT_TEXTE, 'L', 40),				// 20
//		array('niveau', 'Niveau', FORMAT_TEXTE, 'L', 8),					//  5
//		array('etat', 'Etat', FORMAT_TEXTE, 'L', 9),						//  7
//		array('bureau', "Bureau", FORMAT_TEXTE, 'L', 5),					// 18
	) ;

	$req = "SELECT groupe, session.id_session, intitule, intit_ses, universite, 
		ref_etablissement.nom AS institution, ref_etablissement.id AS id_institution,
		genre, dossier.nom, prenom, naissance,
		((DATEDIFF(date_deb, naissance)) DIV 365.25) AS age, pays_nationalite,
		lieu_paiement, montant, monnaie, lieu_examen,
		imputations.imputation, date_imput, date_maj_imput,
		imputations.commentaire
		FROM imputations, dossier, session, atelier
		LEFT JOIN ref_etablissement ON ref_etablissement.id=atelier.ref_institution
		WHERE ref_dossier=id_dossier " ;
	if ( !empty($_SESSION["filtres"]["imputations"]["promotion"]) ) {
		$req .= "AND dossier.id_session='".$_SESSION["filtres"]["imputations"]["promotion"]."' " ;
	}
	if ( !empty($_SESSION["filtres"]["imputations"]["lieu_paiement"]) ) {
		$req .= "AND lieu_paiement='".$_SESSION["filtres"]["imputations"]["lieu_paiement"]."' " ;
	}
	if ( !empty($_SESSION["filtres"]["imputations"]["lieu_examen"]) ) {
		$req .= "AND lieu_examen='".$_SESSION["filtres"]["imputations"]["lieu_examen"]."' " ;
	}
	$req .= " AND dossier.ref_session=session.id_session
		AND atelier.id_atelier=session.ref_atelier
		ORDER BY groupe, intitule, intit_ses, nom" ;
// AND session.imputations='Oui'
	//echo $req ;
	$res = mysqli_query($cnx, $req) ;

	$nb_lignes  = mysqli_num_rows($res) ;
	$nb_colonnes = count($champs) ;

	// en-tête du fichier SYLK
	$flux  = "ID;PASTUCES-phpInfo.net\n" ; // ID;Pappli
	$flux .= "\n" ;
	// formats
	$flux .= "P;PGeneral\n" ;
	$flux .= "P;P#,##0.00\n" ; // P;Pformat_1 (reels)
	$flux .= "P;P#,##0\n" ;	// P;Pformat_2 (entiers)
	$flux .= "P;P@\n" ;		// P;Pformat_3 (textes)
	$flux .= "\n" ;
	// polices
	$flux .= "P;EArial;M200\n";
	$flux .= "P;EArial;M200\n";
	$flux .= "P;EArial;M200\n";
	$flux .= "P;FArial;M200;SB\n";
	$flux .= "\n";
	// nb lignes * nb colonnes :  B;Yligmax;Xcolmax
	$flux .= "B;Y".($nb_lignes+1) ;
	$flux .= ";X". $nb_colonnes ;
	$flux .= "\n";

	// récupération des infos de formatage des colonnes
	for ($cpt = 0; $cpt < $nb_colonnes; $cpt++)
	{
		$num_format[$cpt] = $champs[$cpt][2] ;
		$format[$cpt] = $cfg_formats[$num_format[$cpt]].$champs[$cpt][3] ;
	}
	// largeurs des colonnes
	for ($cpt = 1; $cpt <= $nb_colonnes; $cpt++)
	{
		// F;Wcoldeb colfin largeur
		$flux .= "F;W".$cpt." ".$cpt." ".$champs[$cpt-1][4]."\n";
	}
	$flux .= "F;W".$cpt." 256 8\n"; // F;Wcoldeb colfin largeur
	$flux .= "\n";
	// en-tête des colonnes (en gras --> SDM4)
	for ($cpt = 1; $cpt <= $nb_colonnes; $cpt++)
	{
		$flux .= "F;SDM4;FG0C;".($cpt == 1 ? "Y1;" : "")."X".$cpt."\n";
		$flux .= "C;N;K\"".$champs[$cpt-1][1]."\"\n";
	}
	$flux .= "\n";
	// Données utiles
	$ligne = 2;
	while ($enr = mysqli_fetch_row($res))
	{
/*
		$lieu = $enr[8] ;
		$promotion = $enr[0] ;
*/
		// parcours des champs
		for ($cpt = 0; $cpt < $nb_colonnes; $cpt++)
		{
			// format
			$flux .= "F;P".$num_format[$cpt].";".$format[$cpt] ;
			$flux .= ($cpt == 0 ? ";Y".$ligne : "").";X".($cpt+1)."\n";
			// valeur
			if ($num_format[$cpt] == FORMAT_TEXTE)
			{
				// Dates à formater
				if ( ($cpt==10) OR ($cpt==18) OR ($cpt==19) ) {
					$flux .= "C;N;K\"".mysql2date($enr[$cpt])."\"\n";
				}
				// Nom
				else if ($cpt == 8) {
					$flux .= "C;N;K\"".str_replace(';', ';;', strtoupper($enr[$cpt]))."\"\n";
				}
				// Prénom
				else if ($cpt == 9) {
					$flux .= "C;N;K\"".str_replace(';', ';;', ucwords(strtolower($enr[$cpt])))."\"\n";
				}
				// Bureau
				else if ($cpt == 18) {
					$flux .= "C;N;K\"".str_replace(';', ';;', $implantationBureau[$enr[$cpt-1]])."\"\n";
				}
				// Commentaire : enlever les retours à la ligne
				else if ($cpt == 28) {
					$flux .= "C;N;K\"".str_replace(';', ';;', str_replace("\r\n", " ", $enr[$cpt])   )."\"\n";
				}
				else {
					$flux .= "C;N;K\"".str_replace(';', ';;', $enr[$cpt])."\"\n";
				}
			}
			else
				$flux .= "C;N;K".$enr[$cpt]."\n";
		}
		$flux .= "\n" ;
		$ligne++ ;
	}
	// fin du fichier
	$flux .= "E\n";

	// UTF-8
	if ( isset($_SESSION["filtres"]["imputations"]["latin1"]) AND ($_SESSION["filtres"]["imputations"]["latin1"] == "latin1") ) {
		$flux = utf8_decode($flux) ;
	}

	//Nom de fichier
	$datecourante = date("Y-m-d", time()) ;
	$fichier = "Imputations" . "__" . $datecourante ;
/*
	if ( !empty($_SESSION["filtres"]["imputations"]["promotion"]) ) {
		$fichier .= "__" . $promotion ;
	}
	if ( !empty($_SESSION["filtres"]["imputations"]["lieu"]) ) {
		$fichier .= "__" . $lieu ;
	}
*/
	$fichier .= ".xls" ;
	$fichier = strtr($fichier, "éèêàâôû", "eeeaaou") ;
	$fichier = strtr($fichier, "',", "__") ;

	header("Content-disposition: filename=$fichier") ;
	if ( isset($_SESSION["filtres"]["imputations"]["latin1"]) AND ($_SESSION["filtres"]["imputations"]["latin1"] == "latin1") ) {
		header('Content-type: application/vnd.ms-excel; ; charset=iso-8859-1') ;
	}		   
	else {  
		header('Content-type: application/vnd.ms-excel; ; charset=UTF-8') ;
	}
	header('Pragma: no-cache') ;
	header('Expires: 0') ;
	echo $flux ;
}

// Pas d'export : affichage
else
{
	if	(
		( !isset($_SESSION["filtres"]["imputations"]["annee"]) OR ($_SESSION["filtres"]["imputations"]["annee"] == "") )
		AND 
		( !isset($_SESSION["filtres"]["imputations"]["promotion"]) OR ($_SESSION["filtres"]["imputations"]["promotion"] == "0") )
		)
	{
		$req ="SELECT MAX(annee) FROM session, dossier, imputations
			WHERE ref_dossier=id_dossier
			AND dossier.ref_session=session.id_session" ;
		$res = mysqli_query($cnx, $req) ;
		$row = mysqli_fetch_row($res) ;
		$_SESSION["filtres"]["imputations"]["annee"] = $row[0] ;
	}
/*
	// Imputation 2ème année : On ne peut plus supprimer le choix d'année quand on a choisi une promo
	if ( isset($_SESSION["filtres"]["imputations"]["promotion"]) AND ($_SESSION["filtres"]["imputations"]["promotion"] != "0") ) {
		unset($_SESSION["filtres"]["imputations"]["annee"]) ;
	}
*/

	include("inc_html.php") ;
	$titre = "Imputations (listes et exports)" ;
	echo $dtd1 ;
	echo "<title>$titre</title>\n" ;
	echo $htmlJquery ;
	echo $htmlMakeSublist ;
	echo $dtd2 ;
	include("inc_menu.php") ;
	echo "<div class='noprint'>" ;
	echo $debut_chemin ;
	echo "<a href='/bienvenue.php'>Accueil</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "<a href='/imputations/statistiques.php'>Imputations (statistiques)</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo $titre ;
	echo "</div>" ;
	echo $fin_chemin ;
	


	echo "<form action='criteres.php' method='post'>" ;
	echo "<input type='hidden' name='redirect' value='".$_SERVER["SCRIPT_NAME"]."' />\n" ;
	echo "<table class='formulaire'>\n" ;
	echo "<tbody>\n" ;

	include("inc_promotions.php") ;
	require_once("inc_formations.php") ;
	echo "<tr>\n" ;
	echo "<th rowspan='4'>Limiter à&nbsp;:</th>\n" ;
	echo "<th>Année&nbsp;:</th>\n" ;
	echo "<td><select name='i_annee'>\n" ;
	echo "<option value=''></option>\n" ;

	$req = "SELECT DISTINCT annee FROM session
		ORDER BY annee DESC" ;
	$res = mysqli_query($cnx, $req) ;
	while ( $enr = mysqli_fetch_assoc($res) ) {
		echo "<option value='".$enr["annee"]."'" ;
		if ( isset($_SESSION["filtres"]["imputations"]["annee"]) AND ($_SESSION["filtres"]["imputations"]["annee"] == $enr["annee"]) ) {
			echo " selected='selected'" ;
		}
		echo ">".$enr["annee"]."</option>" ;
	}

	echo "</select></td>\n" ;
	echo "</td>\n" ;
	echo "</tr>\n" ;

	echo "<tr>\n" ;
	echo "<th>Promotion&nbsp;:</th>\n<td style='width: 50em'>" ;
	if ( intval($_SESSION["id"]) < 4 ) {
		$req = "SELECT id_session, annee, groupe, niveau, intitule, intit_ses
			FROM atelier, session
			WHERE session.ref_atelier=atelier.id_atelier
			ORDER BY annee DESC, groupe, niveau, intitule, intit_ses" ;
		echo liste_promotions("i_promotion", $_SESSION["filtres"]["imputations"]["promotion"], $cnx, TRUE, TRUE) ;
		/*
		$formPromo = chaine_liste_promotions("i_promotion",
			( isset($_SESSION["filtres"]["imputations"]["promotion"]) ? $_SESSION["filtres"]["imputations"]["promotion"] : "" ),
			$req, $cnx) ;
		echo $formPromo["form"] ;
		echo $formPromo["script"] ;
		*/
	}
	else {
		echo liste_promotions("i_promotion", $_SESSION["filtres"]["imputations"]["promotion"], $cnx, TRUE, TRUE) ;
	}
	echo "</td>\n</tr>\n" ;

	include("inc_form_select.php") ;
	echo "<tr>\n<th>Lieu de paiement&nbsp;:</th>\n<td>" ;
	form_select_1($CNF, "i_lieu_paiement",
		( isset($_SESSION["filtres"]["imputations"]["lieu_paiement"]) ? $_SESSION["filtres"]["imputations"]["lieu_paiement"] : "" )
		) ;
	echo "</td>\n</tr>\n" ;

	echo "<tr>\n<th>Lieu d'examen&nbsp;:</th>\n<td>" ;
	form_select_1($CNF, "i_lieu_examen",
		( isset($_SESSION["filtres"]["imputations"]["lieu_examen"]) ? $_SESSION["filtres"]["imputations"]["lieu_examen"] : "" )
		) ;
	echo "</td>\n</tr>\n" ;

function liste_tri($name, $value)
{
	$TRI = array(
		"" => "",
		"nom" => "Nom",
		"civilite" => "Civilité",
		"date_imput" => "Date de paiement",
		"date_maj_imput" => "Date de mise à jour",
		"lieu_paiement" => "Lieu de paiement",
		"lieu_examen" => "Lieu d'examen",
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

	echo "<tr>\n" ;
	echo "<th>Trier par&nbsp;:</th>\n" ;
	echo "<td colspan='2'>" ;
	liste_tri("i_tri",
		( isset($_SESSION["filtres"]["imputations"]["tri"]) ? $_SESSION["filtres"]["imputations"]["tri"] : "" )
	) ;
	echo "</td>\n" ;

	echo "<tr>\n<td colspan='3'>" ;
	echo "<div class='c' style='padding: 3px 0;'>";
	echo "<div style='float: right'>\n" ;
	echo "<input type='submit' style='font-weight: bold; margin-right: 1em;' " ;
	echo " name='action' value='Exporter' />" ;
	echo "<label for='latin1'><input type='checkbox' id='latin1' name='latin1' value='latin1' /> &nbsp;Exporter en ISO-8859-1</div>" ;

	echo "<a class='reinitialiser' href='reinitialiser.php?redirect=".urlencode($_SERVER["SCRIPT_NAME"])."'>".LABEL_REINITIALISER."</a>"
	    . BOUTON_ACTUALISER ;

	echo "</div>\n" ;
	echo "</td>\n</tr>\n" ;

	echo "</tbody>\n" ;
	echo "</table>\n" ;
	echo "</form>" ;




	$req = "SELECT dossier.*, imputations.*,
		groupe, niveau, intitule, intit_ses
		FROM imputations, dossier, session, atelier
		WHERE  ref_dossier=id_dossier " ;
	if ( !empty($_SESSION["filtres"]["imputations"]["annee"]) ) {
		$req .= "AND annee='".$_SESSION["filtres"]["imputations"]["annee"]."' " ;
	}
	if ( !empty($_SESSION["filtres"]["imputations"]["lieu"]) ) {
		$req .= "AND lieu='".$_SESSION["filtres"]["imputations"]["lieu"]."' " ;
	}
	if ( !empty($_SESSION["filtres"]["imputations"]["promotion"]) ) {
		$req .= "AND dossier.ref_session='".$_SESSION["filtres"]["imputations"]["promotion"]."' " ;
	}
	if ( intval($_SESSION["id"]) > 3 ) {
		$req .= " AND session.id_session IN (".$_SESSION["liste_toutes_promotions"].") " ;
	}
	$req .= " AND dossier.ref_session=session.id_session
		AND atelier.id_atelier=session.ref_atelier
		ORDER BY groupe, intitule, intit_ses" ;
//		AND session.imputations='Oui'
	if ( !empty($_SESSION["filtres"]["imputations"]["tri"]) ) {
		$req .= ", ". $_SESSION["filtres"]["imputations"]["tri"] ;
	}
//	echo $req ;
	$res = mysqli_query($cnx, $req) ;
	$N = mysqli_num_rows($res);
	
	if ( $N != 0 )
	{
		if ( $N > 1 ) { $s = "s" ; }
		else { $s = "" ; }
		echo "<p class='c'><strong>$N</strong> imputations pour ces critères :</p>" ;
	
		echo "<table class='tableau'>\n" ;
		echo "<thead>\n" ;
		echo "<tr>\n" ;
		echo "<th class='help' title=\"Date d'encaissement\">Date</th>" ;
		echo "<th>Lieu examen</th>" ;
		echo "<th>Lieu paiement</th>" ;
		echo "<th>Genre Nom Prénoms</th>" ;
		echo "<th>Imputation</th>" ;
		echo "</tr>\n" ;
		echo "</thead>\n" ;
		echo "<tbody>\n" ;
		$groupe = "" ;
		$formation = "" ;
		while ( $enr = mysqli_fetch_assoc($res) )
		{
			if ( $groupe != $enr["groupe"] ) {
				$groupe = $enr["groupe"] ;
				echo "<tr><td style='background: #ccc' class='r' colspan='6'>" ;
				echo "<b style='font-size: 120%;'>$groupe</b></td></tr>" ;
			}
			if ( $formation != ( $enr["intitule"]." - ".$enr["intit_ses"]) ) {
				$formation = $enr["intitule"]." - ".$enr["intit_ses"] ;
				echo "<tr><td style='background: #ccc' class='l' colspan='6'>" ;
				echo "<b>$formation</b></td></tr>" ;
			}
			echo "<tr>\n" ;
			echo "<td>".mysql2datenum($enr["date_imput"])."</td>\n" ;
			echo "<td class='c'>".$enr["lieu_examen"] ;
			echo "<td class='c'>".$enr["lieu_paiement"] ;
			// echo " ".$implantationBureau[$enr["lieu"]] ;
			echo "</td>\n" ;
			echo "<td><a class='bl' " ;
				echo "href='attestation.php?id=".$enr["id_imputation"]."'>" ;
				echo $enr["genre"] . " " ;
				echo "<strong>" .strtoupper($enr["nom"]) . "</strong> " 
				. ucwords(strtolower($enr["prenom"])) ;
				echo "</a></td>\n" ;

			echo "<td>".$enr["imputation"]."</td>\n" ;
			echo "</tr>\n" ;
		}
		echo "</tbody>\n" ;
		echo "</table>\n" ;
	}
	else {
		echo "<p class='c'>Aucune imputation pour ces critères.</p>" ;
	}
}





deconnecter($cnx) ;
echo $end ;
?>
