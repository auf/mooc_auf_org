<?php
include("inc_session.php") ;
include("inc_html.php") ;
include("inc_mysqli.php") ;
include_once("inc_formations.php");
include_once("inc_date.php");

$annee_courante = intval(date("Y", time())) ;
$ANNEE = array() ;
for ( $i=2014 ; $i <=($annee_courante+2) ; $i++ ) {
	$ANNEE[] = $i ;
} 

function radioBool($nom, $checked)
{
	echo "<label class=''>" ;
	echo "<input type='radio' id='$nom' name='$nom' value='1'" ;
	if ( $checked == '1' ) {
		echo " checked='checked'" ;
	}
	echo "> Oui</label>\n" ;
	echo "<label class='' style='padding-left: 2em;'>" ;
	echo "<input type='radio' id='$nom' name='$nom' value='0'" ;
	if ( $checked == '0' ) {
		echo " checked='checked'" ;
	}
	echo "> Non</label>\n" ;
}

function liste_der($tab, $nom_ld, $selected)
{
	echo "<select name=\"$nom_ld\">\n" ;
	while ( list($champ,$valeur) = each($tab) )
	{
		echo "<option value=\"$valeur\"" ;
		if ( $valeur == $selected ) {
			echo " selected='selected'" ;
		}
		echo ">$valeur</option>\n" ;
	}
	echo "</select>" ;
}

$cnx = connecter() ;

// Formulaire envoye, traitement du formulaire : controle de validite
if ( isset($_POST["formulaire"]) )
{  
	include("controle_promotion.php") ;
}

// si formulaire envoye et aucune erreur detectee
if ( isset($_POST["formulaire"]) AND $verif_saisie=="ok" )
{  
	include("save_promotion.php");
}

// si erreur trouvee ou 1ere execution
if	(
		( isset($verif_saisie) AND ($verif_saisie!="ok") )
		OR !isset($_POST["formulaire"])
	)
{
	if ( ($_GET["operation"] == "modif") OR ($_GET["operation"] == 'modifBase') )
	{
		$req = "SELECT intitule, session.* FROM atelier, session
			WHERE id_session=".$_GET["session"]."
			AND session.ref_atelier=atelier.id_atelier" ;
		$res = mysqli_query($cnx, $req) ;
		$row = mysqli_fetch_assoc($res) ;

		$session     = $row["id_session"] ;
		$ref_atelier = $row["ref_atelier"] ;
		$intit_ses   = $row["intit_ses"] ;
		$annee       = intval($row["annee"]) ;
		$ects        = $row["ects"] ;
		$chapeau     = $row["chapeau"] ;
		$inscriptions_deb = mysql2date($row["inscriptions_deb"]) ;
		$inscriptions_fin = mysql2date($row["inscriptions_fin"]) ;
		$imputations_deb  = mysql2date($row["imputations_deb"]) ;
		$imputations_fin  = mysql2date($row["imputations_fin"]) ;
		$date_deb         = mysql2date($row["date_deb"]) ;
		$date_fin         = mysql2date($row["date_fin"]) ;
		$date_examen      = mysql2date($row["date_examen"]) ;

		$idmooc             = $row["idmooc"] ;
		$consignes_idmooc   = $row["consignes_idmooc"] ;
		$identite           = $row["identite"] ;
		$consignes_identite = $row["consignes_identite"] ;
		$pj                 = $row["pj"] ;
		$consignes_pj       = $row["consignes_pj"] ;

		$code_imputation = $row["code_imputation"] ;
		$tarif  = $row["tarif"] ;
		$tarif2 = $row["tarif2"] ;
		$tarif1 = $row["tarif1"] ;
		$tarif3 = $row["tarif3"] ;

		$hidden = "<input type='hidden' name='operation' value='modifBase' />" ;
		$titre = "Modification d'une inscription" ;
	}
	//if ( ( $_GET["operation"] == "add") OR ( $_GET["operation"] == "addBase") )
	else
	{
		$intit_ses   = "" ;
		$ref_atelier = "" ;
		$annee       = "" ;
		$ects        = "0" ;
		$chapeau     = "" ;
		$inscriptions_deb = "" ;
		$inscriptions_fin = "" ;
		$imputations_deb  = "" ;
		$imputations_fin  = "" ;
		$date_deb         = "" ;
		$date_fin         = "" ;
		$date_examen      = "" ;

		$idmooc           = "0" ;
		$consignes_idmooc = "" ;
		$identite         = "0" ;
		$consignes_identite = "" ;
		$pj               = "0" ;
		$consignes_pj     = "" ;

		$code_imputation = "" ;
		$tarif  = "0" ;
		$tarif1 = "0" ;
		$tarif2 = "0" ;
		$tarif3 = "0" ;

		$hidden = "<input type='hidden' name='operation' value='addBase' />" ;
		$titre = "Nouvelle inscription" ;
	}

	echo $dtd1 ;
	echo "<title>$titre</title>\n" ;
	echo $htmlJquery ;
	echo $htmlMakeSublist ;
	echo $htmlDatePick ;
?>
<script type="text/javascript">
$(function() {
	/*
	$('#date_deb,#date_fin').datepick({beforeShow: customRange, 
	    showOn: 'both',showBigPrevNext: true, firstDay: 0}); 
	function customRange(input) {  
	    return {
			minDate: (input.id == 'date_fin' ? $('#date_deb').datepick('getDate') : null),  
	        maxDate: (input.id == 'date_deb' ? $('#date_fin').datepick('getDate') : null)
		};  
	}
	*/

	$('#date_examen').datepick({showOn: 'both',showBigPrevNext: true, firstDay: 0}) ;

	$('#inscriptions_deb,#inscriptions_fin').datepick({beforeShow: customRangeCand, 
	    showOn: 'both',showBigPrevNext: true, firstDay: 0}); 
	function customRangeCand(input) {  
	    return {
			minDate: (input.id == 'inscriptions_fin' ? $('#inscriptions_deb').datepick('getDate') : null),  
	        maxDate: (input.id == 'inscriptions_deb' ? $('#inscriptions_fin').datepick('getDate') : null)
		};  
	}

	$('#imputations_deb,#imputations_fin').datepick({beforeShow: customRangeImpu, 
	    showOn: 'both',showBigPrevNext: true, firstDay: 0}); 
	function customRangeImpu(input) {  
	    return {
			minDate: (input.id == 'imputations_fin' ? $('#imputations_deb').datepick('getDate') : null),  
	        maxDate: (input.id == 'imputations_deb' ? $('#imputations_fin').datepick('getDate') : null)
		};  
	}

	$("input[name=pj]").change(function() {
		var pj = $(this).val();
		if ( pj == 1 ) {
			$("#ligne_consignes_pj").css('display', 'table-row');
		}
		else {
			$("#ligne_consignes_pj").css('display', 'none');
		}
	}); 

	$("input[name=idmooc]").change(function() {
		var idmooc = $(this).val();
		if ( idmooc == 1 ) {
			$("#ligne_consignes_idmooc").css('display', 'table-row');
		}
		else {
			$("#ligne_consignes_idmooc").css('display', 'none');
		}
	}); 

	$("input[name=identite]").change(function() {
		var identite = $(this).val();
		if ( identite == 1 ) {
			$("#ligne_consignes_identite").css('display', 'table-row');
		}
		else {
			$("#ligne_consignes_identite").css('display', 'none');
		}
	}); 
});
</script>
<?php
	echo $dtd2 ;
	include("inc_menu.php") ;
	echo $debut_chemin ;
	echo "<a href='/bienvenue.php'>Accueil</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "<a href='/promotions/index.php" ;
	if ( isset($_GET["session"]) ) {
		echo "#".$_GET["session"] ;
	}
	echo "'>Inscriptions</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo $titre ;
	echo $fin_chemin ;

	if ( $idmooc == "0" ) {
		$consignes_idmooc_display = " display: none;" ;
	}
	if ( $identite == "0" ) {
		$consignes_identite_display = " display: none;" ;
	}
	if ( $pj == "0" ) {
		$consignes_pj_display = " display: none;" ;
	}
?>
<form id="formpromo" action='promotion.php' method='post'>

<table class='formulaire'>
<?php
if ( isset($verif_saisie) AND ($verif_saisie != "ok") ) {
		echo "<tr><td colspan='3' style='background: #fff;'>" ;
		echo $verif_saisie."</td></tr>\n" ;
}
?>
<tr>
	<th>Formation : </th>
	<td colspan='3'><?php
liste_formations($cnx, "ref_atelier", $ref_atelier) ;
/*
$formForma = chaine_liste_formations("ref_atelier", $ref_atelier, "", $cnx) ;
echo $formForma["form"] ;
echo $formForma["script"] ;
*/
	?></td>
</tr><tr>
	<th>Année : </th>
	<td colspan='2'><?php liste_der($ANNEE, "annee", $annee) ; ?></td>
</td>
</tr><tr>
	<th>Intitulé de l'inscription : </th>
	<td colspan='2'><input type="text" name="intit_ses" size="82" maxlength="255" value="<?php
		echo "$intit_ses";
		?>" /></td>
</tr><tr>
	<th class='help' title="ECTS : Système européen de transfert et d’accumulation de crédits
(European Credits Transfer System)">ECTS : </th>
	<td colspan='2'><input type="text" name="ects" size="10" value="<?php
		echo $ects ;
		?>" class='l' /></td>
</tr><tr>
	<th class='help' title="Chapeau : texte introductif">Chapeau : </th>
	<td colspan='2'><textarea name='chapeau' rows='2' cols='80'><?php
		echo $chapeau ;
		?></textarea></td>
	
</tr>
<!--
<tr><td style="padding: 1px; background: #777; height: 1px;" colspan="3"></td></tr>
-->
<tr>
	<th>Pré-inscriptions : </th>
	<td colspan='2'><?php 
		echo "Du " ;
		echo "<input type='text' size='10' id='inscriptions_deb' name='inscriptions_deb'";
		echo " value='". $inscriptions_deb ."'" ;
		echo " />" ;
		echo " au " ;
		echo "<input type='text' size='10' id='inscriptions_fin' name='inscriptions_fin'";
		echo " value='". $inscriptions_fin ."'" ;
		echo " />" ;
	?></td>
</tr><tr>
	<th>Imputations : </th>
	<td colspan='2'><?php 
		echo "Du " ;
		echo "<input type='text' size='10' id='imputations_deb' name='imputations_deb'";
		echo " value='". $imputations_deb ."'" ;
		echo " />" ;
		echo " au " ;
		echo "<input type='text' size='10' id='imputations_fin' name='imputations_fin'";
		echo " value='". $imputations_fin ."'" ;
		echo " />" ;
	?></td>
</tr>
<?php /*
<tr>
	<th>Dates de la formation : </th>
	<td colspan='2'><?php
		echo "Du " ;
		echo "<input type='text' size='10' id='date_deb' name='date_deb'";
		echo " value='". $date_deb ."'" ;
		echo " />" ;
		echo " au " ;
		echo "<input type='text' size='10' id='date_fin' name='date_fin'";
		echo " value='". $date_fin ."'" ;
		echo " />" ;
		?></td>
</tr>
*/ ?>
<tr>
	<th>Date de l'examen : </th>
	<td colspan='2'><?php
		echo "Le " ;
		echo "<input type='text' size='10' id='date_examen' name='date_examen'";
		echo " value='". $date_examen ."'" ;
		echo " />" ;
		echo " <span class='s' style='margin-left: 2em;'>(Les dossiers sont éditables jusqu'à la veille de cette date. Sert aussi au calcul de l'âge.)</span>" ;
		?></td>
</tr>

<tr><td colspan='3' class='invisible'>
	<div style='float: right;'><input class='b' type="submit" value="Enregistrer" /></div>
	<div style='margin-top: 0.9em;'><strong>Paramétrage du formulaire d'inscription</strong></div>
	<div class='s'>Les retours à la ligne dans les consignes sont reproduits tels quels dans les formulaires (sauf premier et dernier qui sont supprimés).</div>
</td></tr>
<tr>
	<th>Identifiant MOOC : </th>
	<td colspan='2'><?php
		radioBool("idmooc", $idmooc) ;
		?></td>
</tr>
<tr id='ligne_consignes_idmooc' style='<?php echo $consignes_idmooc_display; ?>'>
	<th>Consignes<br />Identifiant MOOC :</th>
	<td colspan='2'><textarea name='consignes_idmooc' rows='2' cols='80'><?php
		echo $consignes_idmooc ;
		?></textarea></td>
</tr>
<tr>
	<th>Pièce d'identité : </th>
	<td colspan='2'><?php
		radioBool("identite", $identite) ;
		?></td>
</tr>
<tr id='ligne_consignes_identite' style='<?php echo $consignes_identite_display; ?>'>
	<th>Consignes<br />Pièce d'identité :</th>
	<td colspan='2'><textarea name='consignes_identite' rows='2' cols='80'><?php
		echo $consignes_identite ;
		?></textarea></td>
</tr>
<tr>
	<th>Pièces jointes : </th>
	<td colspan='2'><?php
		radioBool("pj", $pj) ;
		?></td>
</tr>
<tr id='ligne_consignes_pj' style='<?php echo $consignes_pj_display; ?>'>
	<th>Consignes<br />Pièces jointes</th>
	<td colspan='2'><textarea name='consignes_pj' rows='2' cols='80'><?php
		echo $consignes_pj ;
		?></textarea></td>
</tr>
<tr><td colspan='3' class='invisible'>
	<div style='float: right;'><input class='b' type="submit" value="Enregistrer" /></div>
	<div style='margin-top: 0.9em;'><strong>Imputation</strong></div>
</td></tr>
<tr>
	<th>Code d'imputation : </th>
	<td colspan='2'><input type="text" name="code_imputation" size="13" maxlength='13' value="<?php
		echo $code_imputation ;
		?>" /></td>
</tr><tr>
	<th>Tarif unique : </th>
	<td><input type="text" name="tarif" size="7" value="<?php
		echo $tarif ;
		?>" class='r' /> &euro;</td>
	<td colspan='1' class='s' style='vertical-align: top;'>
	Tarif unique.<br />
	Utilisé si la valeur d'un des trois champs suivants est nulle (0).
	</td>
	<!--
	<td rowspan='4'class='s' style='vertical-align: top;'></td>
	-->
</tr><tr>
	<th>Tarif 1 <span class='normal'>(élevé)</span> : </th>
	<td><input type="text" name="tarif1" size="7" value="<?php
		echo $tarif1 ;
		?>" class='r' /> &euro;</td>
	<td colspan='1' rowspan='3' class='s' style='vertical-align: top;'>
	Tarifs appliqués selon le niveau de développement du pays de résidence.<br />
	Utilisés si les 3 valeurs sont non nulles.
	</td>
</tr><tr>
	<th>Tarif 2 <span class='normal'>(intermédiaire)</span> : </th>
	<td><input type="text" name="tarif2" size="7" value="<?php
		echo $tarif2 ;
		?>" class='r' /> &euro;</td>
</tr><tr>
	<th>Tarif 3 <span class='normal'>(faible)</span> : </th>
	<td><input type="text" name="tarif3" size="7" value="<?php
		echo $tarif3 ;
		?>" class='r' /> &euro;</td>

</tr>
</table>

<?php
echo $hidden ;
if ( isset($session) ) {
	?><input type="hidden" name="session" value=<?php echo $session ; ?> /><?php
}
?>
<input type="hidden" name="formulaire" value="OK" />

<p class='c'><input type="submit" value="Enregistrer" /></p>

</form>

<?
//	diagnostic() ;
	echo $end;
}
deconnecter($cnx) ;
?>
