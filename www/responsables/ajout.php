<?php
include("inc_session.php") ;
include("inc_html.php") ;
$titre = "Nouveau responsable" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $htmlJquery . $htmlMakeSublist ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
//echo "<a href='/responsables/index.php'>Responsables</a>" ;
echo "<a href='/responsables/index.php'>Sélectionneurs</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

include_once("inc_mysqli.php") ;
$cnx = connecter() ;

?>

<form action="insert.php" method="post">
<table class='formulaire'>
<tr>
	<th>Institution : </th>
	<td><?php
		require_once("inc_institutions.php") ;
		$tabSelectInstitutions = selectChaineInstitutions($cnx, "ref_institution", 0) ;
		echo $tabSelectInstitutions["form"] ;
		echo $tabSelectInstitutions["script"] ;
		//liste_institutions($cnx, "ref_institution", ( isset($ref_institution) ? $ref_institution : 0 )) ;
		?></td>
</tr><tr>
	<th>Nom : </th>
	<td><input name="zn" type="text" size="40" maxlength="100" value="<?php
		echo ( isset($zn) ? $zn : "" ) ;
		?>" /></td>
</tr><tr>
	<th>Prénom : </th>
	<td><input name="zp" type="text" size="40" maxlength="100" value="<?php
		echo ( isset($zp) ? $zp : "" ) ;
		?>" /></td>
</tr><tr>
	<th>Email : </th>
	<td><input name="zemail" type="text" size="70" maxlength="70" value="<?php
		echo ( isset($zemail) ? $zemail : "" ) ;
		?>" /></td>
</tr><tr>
	<th>Identifiant : </th>
	<td><input name="zuser" type="text" size="20" maxlength="56" value="<?php
		echo ( isset($zuser) ? $zuser : "" ) ;
		?>" /></td>
</tr><tr>
	<th>Mot de passe  : </th>
	<td><input name="zpwd" type="text" size="20" maxlength="20" value="<?php
		echo ( isset($zpwd) ? $zpwd : "" ) ;
		?>" /></td>
</tr><tr>
	<th>Commentaire : </th>
	<td><textarea rows='2' cols='70' name="commentaire"><?php
		echo ( isset($commentaire) ? $commentaire : "" ) ;
		?></textarea></td>
</tr>

<?
$req = "SELECT *  FROM atelier
    ORDER BY groupe, niveau, intitule" ;
$res = mysqli_query($cnx, $req);

echo("<tr><th>Formations : </th><td>") ;

$groupe = "" ;
while ( $val = mysqli_fetch_array($res) )
{
	if ( $val["groupe"] != $groupe ) {
		$groupe = $val["groupe"] ;
		echo "<b>$groupe</b><br />" ;
	}
	echo "<label><input type='checkbox' name=dip[] " ;
	echo "value='".$val["id_atelier"]."' /> " ;
	echo $val["intitule"]."</label><br />" ;
}
echo("</td></tr>");

?>
</table>
<p class='c'><input type="submit" value="Valider" /></p>
</form>

<?php
deconnecter($cnx) ;
echo $end ;
?>
