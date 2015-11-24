<?php
include("inc_session.php") ;
include("inc_mysqli.php") ;
$cnx = connecter() ;

$req = "SELECT * from selecteurs where codesel=".$_GET["select"] ;
$res = mysqli_query($cnx, $req);
$enr = mysqli_fetch_array($res) ;

$titre = $enr["nomsel"] . " " . $enr["prenomsel"] ;
include("inc_html.php") ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $htmlJquery . $htmlMakeSublist ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
//echo "<a href='/responsables/index.php'>Responsables</a>" ;
echo "<a href='/responsables/index.php'>Responsables</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo " <span class='majuscules'>" .$enr["nomsel"]."</span> " . $enr["prenomsel"] ;
echo $fin_chemin ;

?>

<form action="update.php" method="POST">
<input type="hidden" name="select" value="<? echo $_GET["select"] ;?>" />
<table class='formulaire'>
<tr>
    <th>Institution : </th>
    <td><?php
        require_once("inc_institutions.php") ;
		$tabSelectInstitutions = selectChaineInstitutions($cnx, "ref_institution", $enr["ref_institution"]) ;
        echo $tabSelectInstitutions["form"] ;
        echo $tabSelectInstitutions["script"] ;
        ?></td>
</tr><tr>
	<th>Nom : </th>
	<td><input name="zn" type="text" size="40" maxlength="100" value="<?php
		echo ( isset($enr["nomsel"]) ? $enr["nomsel"] : "" );
		?>" /></td>
</tr>
<tr>
	<th>Prénom : </th>
	<td><input name="zp" type="text" size="40" maxlength="100" value="<?php
		echo ( isset($enr["prenomsel"]) ? $enr["prenomsel"] : "" );
		?>" /></td>
</tr>
<tr>
	<th>Email : </th>
	<td><input name="zemail" type="text" size="70" maxlength="70" value="<?php
		echo ( isset($enr["email"]) ? $enr["email"] : "" ) ;
		?>" /></td>
</tr>
<tr>
	<th>Identifiant : </th>
	<td><input name="zuser" type="text" size="20" maxlength="56" value="<?php
		echo ( isset($enr["usersel"]) ? $enr["usersel"] : "" );
		?>" /></td>
</tr>
<tr>
	<th>Mot de passe  : </th>
	<td><input name="zpwd" type="text" size="20" maxlength="20" value="<?php
		echo ( isset($enr["pwdsel"]) ? $enr["pwdsel"] : "" );
		?>" /></td>
</tr>
<tr>
	<th>Transferts possibles &nbsp;:</th>
	<td>
	<label><input type='radio' name='transfert' value='Oui' <?php
	if ( $enr["transfert"] == "Oui" ) {
		echo "checked='checked'" ;
	}
	?>/> Oui</label><br />
	<label><input type='radio' name='transfert' value='Non' <?php
	if ( $enr["transfert"] == "Non" ) {
		echo "checked='checked'" ;
	}
	?>/> Non</label>
	</td>
</tr>
<tr>
	<th>Commentaire : </th>
	<td><textarea rows='2' cols='70' name="commentaire"><?php
		echo ( isset($enr["commentaire"]) ? $enr["commentaire"] : "" ) ;
		?></textarea></td>
</tr>
<tr><td colspan='2' style='background: #fff;'>
	<p class='c'><input type="submit" class='b' value="Modifier" /></p>
	</td>
</tr>
<?
$req = "SELECT *  FROM atelier
	ORDER BY groupe, niveau, intitule" ;
$res1 = mysqli_query($cnx, $req);

//
$req = "SELECT atelier.id_atelier, atelier.intitule
	FROM  atelier, atxsel
	WHERE atelier.id_atelier = atxsel.id_atelier
	AND atxsel.id_sel=".$_GET["select"] ;
$res2 = mysqli_query($cnx, $req) ;

echo "<tr>\n<th>Formations : </th><td>" ;
$groupe = "" ;
$i = 1 ;
while ( $val1 = mysqli_fetch_array($res1) )
{
	if ( $val1["groupe"] != $groupe ) {
		$groupe = $val1["groupe"] ;
		echo "<b>$groupe</b><br />" ;
	}
	$ok = 0 ;
	while ( $val2 = mysqli_fetch_array($res2) ) {
		if ( $val1["id_atelier"] == $val2["id_atelier"] ) {
			$ok = 1;
			break ;
		} 
	}
	$res2 = mysqli_query($cnx, $req) ;
	echo "<input type='checkbox' name =dip[] value='".$val1["id_atelier"]."' " ;
	if ( $ok == 0 )
		echo "id='$i' /> " ;
	else
		echo "id='$i' checked='checked' /> " ;
	echo "<label for='$i'>".$val1["intitule"]."</label><br />" ;
	$i++;
}
     
echo("</td></tr>");

?>
</table>
<p class='c'><input type="submit" class='b' value="Modifier" /></p>
</form>

<?php
deconnecter($cnx) ;
echo $end ;
?>
