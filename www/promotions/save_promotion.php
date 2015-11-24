<?
require_once("inc_date.php") ;
/*
echo "<pre>" ;
print_r($_POST) ;
echo "</pre>" ;
*/

$ref_atelier = $_POST["ref_atelier"] ;
$intit_ses   = mysqli_real_escape_string($cnx, $_POST["intit_ses"]) ;
$annee       = $_POST["annee"] ;
$ects        = mysqli_real_escape_string($cnx, $_POST["ects"]) ;
$chapeau     = mysqli_real_escape_string($cnx, $_POST["chapeau"]) ;
$inscriptions_deb = date2mysql($_POST["inscriptions_deb"]) ;
$inscriptions_fin = date2mysql($_POST["inscriptions_fin"]) ;
$imputations_deb  = date2mysql($_POST["imputations_deb"]) ;
$imputations_fin  = date2mysql($_POST["imputations_fin"]) ;
$date_deb         = date2mysql($_POST["date_deb"]) ;
$date_fin         = date2mysql($_POST["date_fin"]) ;
$date_examen      = date2mysql($_POST["date_examen"]) ;

$idmooc = $_POST["idmooc"] ;
$consignes_idmooc = mysqli_real_escape_string($cnx, $_POST["consignes_idmooc"]) ;
$identite = $_POST["identite"] ;
$consignes_identite = mysqli_real_escape_string($cnx, $_POST["consignes_identite"]) ;
$pj = $_POST["pj"] ;
$consignes_pj = mysqli_real_escape_string($cnx, $_POST["consignes_pj"]) ;

$code_imputation = mysqli_real_escape_string($cnx, $_POST["code_imputation"]) ;
$tarif = floatval(str_replace(",", ".", $_POST["tarif"])) ;
$tarif1 = floatval(str_replace(",", ".", $_POST["tarif1"])) ;
$tarif2 = floatval(str_replace(",", ".", $_POST["tarif2"])) ;
$tarif3 = floatval(str_replace(",", ".", $_POST["tarif3"])) ;

$session = $_POST["session"] ;

if ( $_POST["operation"] == "addBase" )
{
	$req = "INSERT INTO session
		(ref_atelier, intit_ses, annee, ects, chapeau,
		inscriptions_deb, inscriptions_fin, imputations_deb, imputations_fin,
		date_deb, date_fin, date_examen,
		idmooc, consignes_idmooc,
		identite, consignes_identite,
		pj, consignes_pj,
		code_imputation, tarif, tarif1, tarif2, tarif3)
		VALUES(
		'$ref_atelier',
		'$intit_ses',
		'$annee',
		'$ects',
		'$chapeau',
		'$inscriptions_deb',
		'$inscriptions_fin',
		'$imputations_deb',
		'$imputations_fin',
		'$date_debut',
		'$date_fin',
		'$date_examen',
		'$idmooc',
		'$consignes_idmooc',
		'$identite',
		'$consignes_identite',
		'$pj',
		'$consignes_pj',
		'$code_imputation',
		'$tarif',
		'$tarif1',
		'$tarif2',
		'$tarif3')";
//	echo $req ;
	$res = mysqli_query($cnx, $req) ;
	if ( !($res) ) {
		echo "<p class='erreur c'>La promotion n'a pas été créée&nbsp;:<br />"
		. mysqli_error($cnx) . "</p>\n" ;
	}
	else {
		$_SESSION["filtres"]["promotions"]["annee"] = $annee ;
		header("Location: /promotions/index.php") ;
	}
}

if ( $_POST["operation"] == "modifBase" )
{
	$req = "UPDATE session SET
		ref_atelier='$ref_atelier',
		intit_ses ='$intit_ses',
		annee='$annee',
		ects='$ects',
		chapeau='$chapeau',
		inscriptions_deb='$inscriptions_deb',
		inscriptions_fin='$inscriptions_fin',
		imputations_deb='$imputations_deb',
		imputations_fin='$imputations_fin',
		date_deb='$date_deb',
		date_fin='$date_fin',
		date_examen='$date_examen',
		idmooc='$idmooc',
		consignes_idmooc='$consignes_idmooc',
		identite='$identite',
		consignes_identite='$consignes_identite',
		pj='$pj',
		consignes_pj='$consignes_pj',
		code_imputation='$code_imputation',
		tarif='$tarif',
		tarif1='$tarif1',
		tarif2='$tarif2',
		tarif3='$tarif3'
		WHERE id_session=$session" ;
//	echo $req ;
	$res = mysqli_query($cnx, $req) ;
	if ( !($res) ) {
		echo "<p class='erreur c'>La promotion n'a pas été modifiée&nbsp;:<br />"
		. mysqli_error($cnx) . "</p>\n" ;
	}
	else {
		header("Location: /promotions/index.php#p".$session) ;
	}
}
?>
