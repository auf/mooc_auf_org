<?php
include("inc_session.php") ;
include("inc_html.php") ;
$titre = "Choix des champs à exporter" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
/*
echo $htmlJquery172
?>
<script language="javascript">
//<![CDATA[ 
function selec(debut, fin) {
	for ( i=debut;i<=fin;i++) {
		casec = $("input[type=\'checkbox\']").get(i) ;
		casec.checked=true;
		casec.parent().parent().addClass("aex");
	}
}
function unselec(debut, fin) {
	for ( i=debut;i<=fin;i++) {
		casec = $("input[type=\'checkbox\']").get(i) ;
		casec.checked=false;
		casec.parent().parent().addClass("aex");
	}
}
$(window).load(function(){
	$("input[type=\'checkbox\']").change(function() {
		if ($(this).is(":checked")) {
			$(this).parent().parent().addClass("aex"); 
		}
		else {
			$(this).parent().parent().removeClass("aex");  
		}
	});
});
//]]>
</script>
*/
?>
<script language="JavaScript">
function selec(debut, fin) {
	for ( i=debut;i<=fin;i++) {
		if (document.forms[0].elements[i].type=="checkbox") {
			document.forms[0].elements[i].checked=true;
		}
	}
}
function unselec(debut, fin) {
	for ( i=debut;i<=fin;i++) {
		if (document.forms[0].elements[i].type=="checkbox") {
			document.forms[0].elements[i].checked=false;
		}
	}
}
</script>
<?php
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/exports/index.php'>Exports : $titre</a>" ;
echo $fin_chemin ;

/*
while (list($key, $val) = each($_POST)) {
   echo "$key => $val<br />" ;
}
*/

include_once("inc_formulaire_inscription.php") ;

function titre_selec($titre, $debut, $fin)
{
	echo "<div><h3>$titre " ;
	echo "<span style='font-size:0.8em; font-weight:normal; padding-left:2em'>" ;
	echo "<span class='fright'><a href='#' onclick='document.getElementById(\"formChamps\").submit();'>Enregistrer</a></span>" ;
	echo "<a href='javascript:selec($debut, $fin)'>Cocher</a> - " ;
	echo "<a href='javascript:unselec($debut, $fin)'>Décocher</a>" ;
	echo "</span></h3>" ;
}

function choix($champ, $flag=FALSE)
{
	global $CANDIDATURE;
	if ( isset($_SESSION["filtres"]["exporter"][$champ]) AND ($_SESSION["filtres"]["exporter"][$champ] == $champ) ) {
		$checked = " checked='checked'" ;
		$class = " class='aex'" ;
	}
	else {
		$checked = "" ;
		$class = "" ;
	}
	echo "<tr" . $class . ">" ;
	echo "<th><input type='checkbox' " ;
	echo "id='$champ' name='$champ' value='$champ'" ;
	echo $checked . " />" ;
	echo "</th><td width='100%'>" ;
	echo " <label class='bl' for='$champ'>" ;
	if ( ($CANDIDATURE[$champ][1] != "")  AND ( $flag!=TRUE ) ) {
		echo $CANDIDATURE[$champ][1] ;
	}
	else {
		echo $CANDIDATURE[$champ][0] ;
	}
	echo "</label>\n" ;
	echo "</td></tr>\n" ;
}

echo "<p>Sélectionnez les champs que vous souhaitez exporter en cochant la case
correspondante (il suffit de cliquer sur la ligne), ou en utilisant les liens
 &nbsp;cocher&nbsp;» et «&nbsp;décocher&nbsp;»&nbsp;;<br />
puis cliquez sur le bouton «&nbsp;Enregistrer&nbsp;» en bas de la page, ou un des liens «&nbsp;Enregistrer&nbsp;» à droite.<br />" ;
if ( intval($_SESSION["id"]) <= 3 ) {
	echo "Certaines cases, avec un libellé en caractères gras, correspondent à plusieurs champs à exporter (mais ne sont exportables que promotion par promotion&nbsp;: ils ne seront pas pris en compte dans un export par année)." ;
}
else {
	echo "Certaines cases, avec un libellé en caractères gras, correspondent à plusieurs champs à exporter." ;
}
if ( isset($_SESSION["filtres"]["exporter"]) AND (count($_SESSION["filtres"]["exporter"]) > 0) ) {
	echo "<br /><span class='aex'>Les champs effectivement enregistrés comme à exporter sont sur fond vert.</span>" ;
}
echo "</p>\n" ;

if ( isset($_SESSION["modif_champs_export"]) AND ($_SESSION["modif_champs_export"] == "ok") ) {
	echo "<p class='msgok'>Les champs que vous voulez exporter ont été enregistrés.<br />
Vous pouvez les modifier, ou revenir à la page précédente en cliquant sur <a href='index.php'>Exports (tableur)</a> ici, ou dans le fil d'ariane, ou dans la navigation permanente.</p>" ;
	unset($_SESSION["modif_champs_export"]) ;
}


echo "<form id='formChamps' method='post' action='choix_champs.php'>\n" ;

echo "<p><strong><a href=\"javascript:selec(0, 27)\">Tout cocher</a> -
<a href=\"javascript:unselec(0, 27)\">Tout décocher</a></strong></p>\n" ;

titre_selec($SECTION_CANDIDATURE["1"], 0, 0) ;
echo "<table class='formulaire hover' width='100%'>\n" ;
	choix("id_mooc") ;
echo "</table>\n" ;

titre_selec($SECTION_CANDIDATURE["2"], 1, 11) ;
echo "<table class='formulaire hover' width='100%'>\n" ;
	choix("email") ;
	choix("genre") ;
	choix("nom") ;
	choix("prenom") ;
	choix("naissance") ;
	choix("age") ;
	choix("pays_naissance") ;
	choix("pays_nationalite") ;
	choix("pays_residence") ;
	choix("situation_actu") ;
	choix("sit_autre") ;
echo "</table>\n" ;

titre_selec($SECTION_CANDIDATURE["3"], 12, 16) ;
echo "<table class='formulaire hover' width='100%'>\n" ;
	choix("ident_nature") ;
	choix("ident_autre") ;
	choix("ident_numero") ;
	choix("ident_date") ;
	choix("ident_lieu") ;
echo "</table>\n" ;

titre_selec("Autres informations relatives à l'inscription", 17, 19) ;
echo "<table class='formulaire hover' width='100%'>\n" ;
	choix("id_dossier") ;
	choix("date_inscrip") ;
	choix("date_maj") ;
echo "</table>\n" ;

titre_selec("Imputation", 20, 26) ;
echo "<table class='formulaire hover' width='100%'>\n" ;
	choix("date_imput") ;
	choix("date_maj_imput") ;
	choix("lieu_paiement") ;
	choix("montant") ;
	choix("monnaie") ;
	choix("imputation") ;
	choix("lieu_examen") ;
echo "</table>\n" ;

titre_selec("Résultat", 27, 28) ;
echo "<table class='formulaire hover' width='100%'>\n" ;
	choix("etat_dossier") ;
	choix("date_maj_etat") ;
echo "</table>\n" ;

titre_selec("Instituion, formation, inscription", 29, 36) ;
echo "<table class='formulaire hover' width='100%'>\n" ;
//	choix("institution") ;
	choix("ref_institution") ;
	choix("universite") ;
	choix("groupe") ;
	choix("ref_discipline") ;
	choix("intitule") ;
	choix("intit_ses") ;
	choix("id_session") ;
echo "</table>\n" ;

/*
titre_selec("Inscription", 27, 29) ;
echo "<table class='formulaire hover' width='100%'>\n" ;
	unchoix("commentaires", "<em>Évaluations</em> du (ou des) sélectionneur(s) et de l'AUF") ;
	unchoix("diplome", "Diplôme (Oui/non)") ;
echo "</table>\n" ;
*/

echo "<p class='c'><input type='submit' style='font-weight: bold'
	value='Enregistrer' /></p>\n" ;

echo "</form>\n" ;


echo $end ;


?>
