<?php
include("inc_session.php") ;

if ( empty($_GET["id_courriel"]) ) {
	header("Location: index.php") ;
	exit ;
}

include("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "SELECT * from courriels WHERE id_courriel=".$_GET["id_courriel"] ;
$res = mysqli_query($cnx, $req) ;
$enr = mysqli_fetch_assoc($res) ;


include_once("inc_date.php") ;
include_once("inc_promotions.php") ;
include_once("inc_etat_dossier.php") ;
include_once("inc_etat_inscrit.php") ;
$promo = idpromotion2nom($enr["ref_session"], $cnx) ;
$titreP = $promo["intitule"]." (".$promo["intit_ses"].")" ;

include("inc_html.php") ;
echo $dtd1 ;
echo "<title>$titreP</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie/index.php'>Messagerie <span>(inscrits)</span></a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie/promotion.php?promotion=".$enr["ref_session"]."'>" ;
echo $titreP."</a>" ; ;
echo " <span class='arr'>&rarr;</span> " ;
echo $enr["subject"] ;
echo $fin_chemin ;

echo "<table class='formulaire'>" ;
echo "<tr>" ;
echo "<th>Date :</th>" ;
echo "<td>".mysql2datealpha($enr["date"])."</td>" ;
echo "</tr>" ;
echo "<tr>" ;
echo "<th>Expéditeur&nbsp;:</th>" ;
echo "<td>".$enr["expediteur"]."</td>" ;
echo "</tr>" ;
echo "<tr>" ;
echo "<th>Copie à&nbsp;:</th>" ;
echo "<td>".$enr["cc"]."</td>" ;
echo "</tr>" ;
echo "<tr>" ;
echo "<th>Commentaire&nbsp;:</th>" ;
echo "<td>".$enr["commentaire"]."</td>" ;
echo "</tr>" ;
echo "<tr>" ;
echo "<th>Sujet&nbsp;:</th>" ;
echo "<td>".$enr["subject"]."</td>" ;
echo "</tr>" ;
echo "<tr>" ;
echo "<th>Message&nbsp;:</th>" ;
echo "<td>".nl2br($enr["body"])."</td>" ;
echo "</tr>" ;

$chemin = "/attachements/".$enr["ref_session"]."/" ;

$req = "SELECT nom, taille FROM attachements
	WHERE ref_courriel=".$_GET["id_courriel"] ;
$res = mysqli_query($cnx, $req) ;

echo "<tr>" ;
echo "<th>Fichiers joints&nbsp;:</th>" ;
echo "<td>" ;
while ( $enr2 = mysqli_fetch_assoc($res) ) {
	echo "<strong>" ;
	echo "<a href='".$chemin.$enr2["nom"]."'>".$enr2["nom"]."</a>" ;
	echo "</strong>" ;
	echo " (".intval($enr2["taille"]/1024.0)."ko) " ;
	echo "<br />\n" ;
}
echo "</td>" ;
echo "</tr>" ;
echo "</table>" ;



$req = "SELECT genre, nom, prenom FROM destinataires, dossier
	WHERE ref_courriel=".$_GET["id_courriel"]."
	AND dossier.id_dossier=destinataires.ref_dossier
	ORDER BY dossier.nom" ;
$res = mysqli_query($cnx, $req) ;
$N = mysqli_num_rows($res) ;


echo "<p class='c'><strong>Destinataires ($N) :</strong></p>" ;
echo "<table class='formulaire'>" ;
if ( $enr["etat"] != "" )
{
	echo "<tr>" ;
	echo "<th>&Eacute;tat&nbsp;:</th>" ;
	echo "<td>".$ETAT_INSCRIT[$enr["etat"]]."</td>" ;
	echo "</tr>" ;
}
if ( $enr["lieu_examen"] != "" )
{
	echo "<tr>" ;
	echo "<th>Lieu d'examen&nbsp;:</th>" ;
	echo "<td>".$enr["lieu_examen"]."</td>" ;
	echo "</tr>" ;
}
if ( $enr["etat_dossier"] != "" )
{
	echo "<tr>" ;
	echo "<th>Résultat&nbsp;:</th>" ;
	echo "<td class='".$ETAT_DOSSIER_IMG_CLASS[$enr["etat_dossier"]]."'>"
		.$ETAT_DOSSIER[$enr["etat_dossier"]]."</td>" ;
	echo "</tr>" ;
}
echo "<tr>" ;
echo "<th>Destinataires&nbsp;:</th>" ;
echo "<td>" ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	echo $enr["civilite"] ;
	echo " <em>". strtoupper($enr["nom"])."</em> " ;
	echo ucwords(strtolower($enr["prenom"])) ;
	echo "<br />" ;
}
echo "</td>" ;
echo "</tr>" ;
echo "</table>" ;


deconnecter($cnx) ;
echo $end ;
?>
