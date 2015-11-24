<?php
include("inc_session.php") ;

function historique($date1="0", $date2="0" )
{
	$req = "SELECT * FROM accessel
		WHERE (date_acces<='$date2' AND date_acces>='$date1' )
		ORDER BY date_acces, heure_acces" ;
	$res = mysqli_query($cnx, $req);
	if ( mysqli_num_rows($res) > 0 ) {
		echo "<table class='tableau'>" ;
		while ( $enr = mysqli_fetch_assoc($res) )
		{
			echo "<tr>" ;
			echo "<td>" . $enr["selecteur"] . "</td>\n" ;
			echo "<td>" . $enr["heure_acces"] . "</td>\n" ;
			echo "<td>" . $enr["date_acces"] . "</td>\n" ;
			echo "</tr>" ;
		}
		echo "</table>";
	}
	else {
		echo "Aucun accès pour cette date" ;
	}
}


include("../include/config.inc.php");
include("../include/shareinc99.php");
include("inc_date.php") ;


include("inc_html.php") ;
$titre = "Journal d'accès des responsables" ;
echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/responsables/index.php'>Responsables</a>" ;
echo " <span class='arr'>&rarr;</span> " ;


if ( !$_POST["operation"] )
{
	echo $titre ;
	echo $fin_chemin ;
	?>
<form action='stat_acces.php?operation=disp' method='post'>
<table class='formulaire'>
<tr>
	<th>Du :</th>
	<td><?php liste_der($jour, "j_deb", 00) ; ?>
<select name="m_deb">
<option value="01">Janvier</option>
<option value="O2">Février</option>
<option value="03">Mars</option>
<option value="04">Avril</option>
<option value="05">Mai</option>
<option value="06">Juin</option>
<option value="07">Juillet</option>
<option value="08">Août</option>
<option value="09">Septembre</option>
<option value="10">Octobre</option>
<option value="11">Novembre</option>
<option value="12">Décembre</option>
</select>
<?php
liste_der($annee,"a_deb",2006) ;
?></td>
</tr>
<tr>
	<th>au :</th>
	<td><?php liste_der($jour,"j_fin",00) ; ?>
<select name="m_fin">
<option value="01">Janvier</option>
<option value="O2">Février</option>
<option value="03">Mars</option>
<option value="04">Avril</option>
<option value="05">Mai</option>
<option value="06">Juin</option>
<option value="07">Juillet</option>
<option value="08">Août</option>
<option value="09">Septembre</option>
<option value="10">Octobre</option>
<option value="11">Novembre</option>
<option value="12">Décembre</option>
</select>
<?
liste_der($annee,"a_fin",2006); 
?></td>
</tr>
</table>

<p class='c'><input type="submit" value="Valider" /></p>
<input type="hidden" name="operation" value="disp" />

</form>
	<?
}
if ( $_POST["operation"] == "disp" )
{
	$chaine_d = $_POST["a_deb"]."-".$_POST["m_deb"]."-".$_POST["j_deb"] ;
	$chaine_f = $_POST["a_fin"]."-".$_POST["m_fin"]."-".$_POST["j_fin"] ;

	echo "<a href=''>" ;
	echo $titre ;
	echo "</a>" ;
	echo " <span class='arr'>&rarr;</span> " ;
	echo "Du ". mysql2datealpha($chaine_d) ;
	echo " au ". mysql2datealpha($chaine_f) ;
	echo $fin_chemin ;

	include("inc_mysqli.php") ;
	con_db() ;


	historique($chaine_d, $chaine_f) ;
}

echo $end ;
?>
