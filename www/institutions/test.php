<?php
include("inc_session.php") ;
include("inc_html.php") ;
include("inc_mysqli.php") ;
include("inc_institutions.php");

$cnx = connecter() ;

echo $dtd1 ;
$titre = "Test institutions" ;
echo "<title>$titre</title>" ;
echo $htmlJquery ;
echo $htmlMakeSublist ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

$selectEtab = selectChaineInstitutions($cnx, "ref_institution",
	( isset($_POST["ref_institution"]) ? $_POST["ref_institution"] : "" )
	) ;
?>

<form action='test.php' method='post'>

<table class='formulaire'>
<tr>
	<th>Institution : </th>
	<td colspan='3'><?php
		echo $selectEtab["form"] ;
		echo $selectEtab["script"] ;
		?></td>
</tr>
</table>

<p class='c'>
<input type="submit" value="Enregistrer" />
</p>

</form>

<?
echo $end;
deconnecter($cnx) ;
?>
