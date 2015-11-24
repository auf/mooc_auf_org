<?php
include("inc_session.php") ;
include("inc_html.php") ;
include("inc_mysqli.php") ;
include("inc_formations.php");

$cnx = connecter() ;

echo $dtd1 ;
echo "<title>$titre</title>" ;
echo $htmlJquery ;
echo $htmlQuickSelect ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/promotions/index.php#".$_GET["session"]."'>Promotions</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;
?>

<script type="text/javascript">
$(function() {
	$("select#id_atelier").quickselect();
});
</script>
	

<form action='promotion.php' method='post'>

<table class='formulaire'>
<?php
if ( $verif_saisie != "ok" ) {
		echo "<tr><td colspan='3' style='background: #fff;'>" ;
		echo $verif_saisie."</td></tr>\n" ;
}
?>
<tr>
	<th>Formation : </th>
	<td colspan='3'><?php liste_formations($cnx, "id_atelier", $id_atelier) ; ?></td>
</tr>
</table>
<?php echo $hidden ;?>
<input type="hidden" name="session" value=<?php echo $session ; ?> />
<input type="hidden" name="formulaire" value="OK" />

<p class='c'>
<input type="submit" value="Enregistrer" />
</p>

</form>

<?
echo $end;
deconnecter($cnx) ;
?>
