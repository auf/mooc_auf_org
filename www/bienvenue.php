<?php
include("inc_session.php") ;

include("inc_html.php") ;
$titre = "Accueil" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo $titre ;
echo $fin_chemin ;

/*
echo "<pre>" ;
print_r($_SESSION) ;
echo "</pre>" ;
*/


?>
<div style='float: right; width: 39%; font-size: 90%;'>
<div style='padding-left: 1em'>

<div style='padding: 0 1em; margin-left: 1em; border: 1px solid #ccc;'>
<p>Toutes <strong>ces pages sont imprimables</strong>
(généralement depuis le menu «&nbsp;Fichier&nbsp;» de votre navigateur).</p>
<p>La <strong>navigation permanente</strong>, encadrée en noir et sur fond gris,
ci-dessus, ne sera pas imprimée.</p>
</div>

<div class='encadre'>
<p>Sauf dans la navigation permanente, tous les liens hypertextes sont soit un
<strong><a href="" style='color: #600'>lien visité (rouge)</a></strong>, soit un
<strong><a href="" style='color: #009'>lien non visité (bleu)</a></strong>.</p>

<p><a target='_blank' href='/documentation/liens.php'>Comment forcer Firefox à considérer tous les liens comme visités ou non visités&nbsp;?</a></p>
</div>

<!--
<div class='encadre' style='margin-bottom: 2em'>
<p><strong>Raccourcis clavier</strong></p>
<p>Très utiles pour passer rapidement du haut au bas de page, notamment dans la gestion des candidatures.</p>
<p><strong>Haut de page :</strong></p>
<table class='tableau'>
<tr>
	<td class='c'>
		<img class='help' src='/img/os/windows.png' width='16' height='16' alt='Windows' title='Windows' />
	</td>
	<td class='c'>
		<img class='help' src='/img/os/ie.png' width='16' height='16' alt='Internet Explorer' title='Internet Explorer' />
	</td>
	<td class='c'><kbd>Alt</kbd> + <kbd>H</kbd>, puis <kbd>Entrer</kbd></td>
</tr>
<tr>
	<td class='c'>
		<img class='help' src='/img/os/windows.png' width='16' height='16' alt='Windows' title='Windows' />
		<img class='help' src='/img/os/linux.png' width='16' height='16' alt='GNU/Linux' title='GNU/Linux' />
	</td>
	<td class='c'>
		<img class='help' src='/img/os/firefox.png' width='16' height='16' alt='Firefox' title='Firefox' />
		<span style='position: relative; top: -3px; line-height: 1em;'>version 1</span>
	</td>
	<td class='c'><kbd>Alt</kbd> + <kbd>H</kbd></td>
</tr>
<tr>
	<td class='c'>
		<img class='help' src='/img/os/windows.png' width='16' height='16' alt='Windows' title='Windows' />
		<img class='help' src='/img/os/linux.png' width='16' height='16' alt='GNU/Linux' title='GNU/Linux' />
	</td>
	<td class='c'>
		<img class='help' src='/img/os/firefox.png' width='16' height='16' alt='Firefox' title='Firefox' />
		<span style='position: relative; top: -3px; line-height: 1em;'>version 2</span>
	</td>
	<td class='c'><kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>H</kbd>
</td>
</tr>
<tr>
	<td class='c'>
		<img class='help' src='/img/os/macosx.png' width='16' height='16' alt='MacOS X' title='MacOS X' />
	</td>
	<td class='c'>
		<img class='help' src='/img/os/firefox.png' width='16' height='16' alt='Firefox' title='Firefox' />
	</td>
	<td class='c'><kbd>Ctrl</kbd> + <kbd>H</kbd></td>
</tr>
<tr>
	<td class='c'>
		<img class='help' src='/img/os/windows.png' width='16' height='16' alt='Windows' title='Windows' />
		<img class='help' src='/img/os/linux.png' width='16' height='16' alt='GNU/Linux' title='GNU/Linux' />
		<img class='help' src='/img/os/macosx.png' width='16' height='16' alt='MacOS X' title='MacOS X' />
	</td>
	<td class='c'>
		<img class='help' src='/img/os/opera.png' width='16' height='16' alt='Opera' title='Opera' />
	</td>
	<td class='c'><kbd>Esc</kbd> + <kbd>Shift</kbd> + <kbd>H</kbd></td>
</tr>
</table>
<p><a href='/documentation/firefox_accesskey.php'>Comment revenir aux raccourcis de Firefox&nbsp;1 dans Firefox&nbsp;2&nbsp;?</a></p>
<p><strong>Bas de page :</strong> idem en remplaçant <kbd>H</kbd> par <kbd>B</kbd></p>
</div>
-->

</div>
</div>

<?php

include_once("inc_mysqli.php") ;
$cnx = connecter() ;

if ( SITE_EN_LECTURE_SEULE ) {
	echo "<h2><p class='erreur'>".EN_MAINTENANCE."</p></h2>\n" ;
	echo "<p class='erreur'>".EN_MAINTENANCE_INFO."</p>\n" ;
}

if ( intval($_SESSION["id"]) < 4 )
{
	// La dernière annee des promotions
	$req = "SELECT MAX(annee) FROM session" ;
	$res = mysqli_query($cnx, $req) ;
	$row = mysqli_fetch_row($res) ;
	$derniere_annee = $row[0] ;

	$req = "SELECT COUNT(dossier.id_dossier)
		FROM dossier, session
		WHERE dossier.ref_session=session.id_session
		AND session.annee=$derniere_annee" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_row($res) ;
	$Ndossiers = $enr[0] ;

	$req = "SELECT COUNT(dossier.id_dossier)
		FROM session, dossier
		LEFT JOIN imputations ON imputations.ref_dossier=dossier.id_dossier
		WHERE dossier.ref_session=session.id_session
		AND id_imputation IS NOT NULL
		AND session.annee=$derniere_annee" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_row($res) ;
	$NdossiersI = $enr[0] ;

	$req = "SELECT COUNT(id_session) FROM session
		WHERE session.annee=$derniere_annee" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_row($res) ;
	$Npromotions = $enr[0] ;

	echo "<p><strong>" ;
	echo "<span style='font-size: 120%'>$Ndossiers</span> pré-inscrits, " ;
	echo "<span style='font-size: 120%'>$NdossiersI</span> inscrits" ;
	echo " pour les " ;
	echo "<span style='font-size: 120%'>$Npromotions</span>" ;
	echo " inscriptions de ".$derniere_annee."." ;
	echo "</strong></p>" ;
}
else
{
	// La dernière annee des promotions, et le nombre de promotions
	$req = "SELECT COUNT(id_session) AS N, annee FROM session
		WHERE id_session IN (".$_SESSION["liste_toutes_promotions"].")
		GROUP BY annee ORDER BY annee DESC" ;
	$res = mysqli_query($cnx, $req) ;
	$row = mysqli_fetch_assoc($res) ;
	$derniere_annee = $row["annee"] ;
	$Npromotions = $row["N"] ;

	$req = "SELECT COUNT(dossier.id_dossier)
		FROM session, dossier
		WHERE dossier.ref_session=session.id_session
		AND session.id_session IN (".$_SESSION["liste_toutes_promotions"].")
		AND session.annee=$derniere_annee" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = @mysqli_fetch_row($res) ;
	$Ndossiers = $enr[0] ;

	$req = "SELECT COUNT(dossier.id_dossier)
		FROM session, dossier
		LEFT JOIN imputations ON imputations.ref_dossier=dossier.id_dossier
		WHERE dossier.ref_session=session.id_session
		AND id_imputation IS NOT NULL
		AND session.id_session IN (".$_SESSION["liste_toutes_promotions"].")
		AND session.annee=$derniere_annee" ;
	$res = mysqli_query($cnx, $req) ;
	$enr = @mysqli_fetch_row($res) ;
	$NdossiersI = $enr[0] ;

	echo "<p><strong>" ;
	echo "<span style='font-size: 120%'>$Ndossiers</span> pré-inscrits, " ;
	echo "<span style='font-size: 120%'>$NdossiersI</span> inscrits" ;
	if ( $Npromotions == 1 ) {
		echo " pour votre inscription de ".$derniere_annee."." ;
	}
	else {
		echo " pour vos <span style='font-size: 120%'>$Npromotions</span> inscriptions de ".$derniere_annee."." ;
	}
	echo "</strong></p>" ;
}
?>

<p><strong>
Contact : 
<a href="mailto:<?php echo EMAIL_CONTACT ; ?>"><?php echo EMAIL_CONTACT ; ?></a>
</strong></p>

<br />


<?php
if ( ($_SESSION["id"] != "00") AND ($_SESSION["id"] != "02") ) {
?>
<?php
}
?>


<?php
if ( ($_SESSION["id"]=="01") OR ($_SESSION["id"]=="02") ) {
?>
<br />
<p style='margin: 0.5em 0;'>La <strong>création d'une nouvelle attestation
de paiement et d'inscription</strong> se fait à partir de la
«&nbsp;Gestion des inscrits&nbsp;» ou de la «&nbsp;Recherche&nbsp;» en
cliquant sur un lien «&nbsp;Imputation&nbsp;».</p>
<p style='margin: 0.5em 0;'>Les liens «&nbsp;Imputé&nbsp;» permettent
de consulter, modifier ou supprimer une attestation existante.</p>
<p style='margin: 0.5em 0;'>La liste des attestations existantes est
consultable en cliquant sur le lien
«&nbsp;<strong>Imputations</strong>&nbsp;» dans la
navigation permanente. (Le code d'imputation comptable est calculé
automatiquement lors de la création/modification d'une attestation.)</p>
<?php
}
?>


<div style='clear: both; font-size: 90%; text-align: center;'>
<br />
<p>Pour tout problème technique, ou pour suggérer une amélioration,<br />
vous pouvez envoyer un courriel à <a href='mailto:cedric.musso@labor-liber.org'
>Cédric Musso</a>
en précisant «&nbsp;MOOC&nbsp;» dans le sujet, et en mettant
<?php
echo "<a href='mailto:".EMAIL_CONTACT."'>".EMAIL_CONTACT."</a>\n" ;
?>
en copie.</p>
</div>



<?php
//echo "<br /><br /><br /><br /><br /><br /><br /><br /> <br /><br /><br /><br /><br /><br /><br /><br /> <br /><br /><br /><br /><br /><br /><br /><br />" ;

echo $end ;
deconnecter($cnx) ;
?>


