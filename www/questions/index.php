<?php
include("inc_session.php");

function modif_dossier($id_dossier, $tab_question, $op_modif, $index_quest, $cnx)
{
	if ( $op_modif == "addquestion" ) {
		foreach($tab_question as $question) {
			$query = "INSERT INTO question(id_question, texte_quest, id_modos)
				VALUES ('','".mysqli_real_escape_string($cnx, $question)."','$id_dossier')" ;
			mysqli_query($cnx, $query) OR mysqli_error($cnx) ;
		}
	}   
	elseif ( $op_modif == "modifquestion" ) {
		$i=0 ;
		foreach($tab_question as $question) {
			$id_question = $index_quest[$i] ;
			$req = "UPDATE question SET texte_quest='".mysqli_real_escape_string($cnx, $question)."'
				WHERE id_question=$id_question" ;
			mysqli_query($cnx, $req) ;
			$i++ ;
		}
		echo "<p class='msgok'><b>Les questions ont été mises à jour :</b></p>";
		echo "<p><strong>". affiche_nom_formation($id_dossier, $cnx) . "</strong>\n&nbsp; " ;
		echo "<a href='/questions/index.php?dossier=$id_dossier&amp;operation=modif'>Modifier</a></p>\n" ;
		affiche_questions($id_dossier, $cnx) ;
	}
}
// Ajouter nouveau modéle de dossier dans la base
function addBase_dossier($index_atelier, $tab_question, $cnx)
{
	// Verifier la non existance d'un dossier pour cet atelier
	$req = "SELECT COUNT(*) FROM mod_dossier WHERE id_atelier='$index_atelier'" ;
	$res = mysqli_query($cnx, $req) ;
	$nbre = mysqli_fetch_row($res) ;
	if ( $nbre[0] == 0 )
	{
		$query = "INSERT INTO mod_dossier (id_modos, id_atelier, date_dos)
			VALUES ('', '$index_atelier', CURRENT_DATE)" ;
		$res = mysqli_query($cnx, $query) ;
		if ($res) {
			$id_dossier = mysqli_insert_id($cnx) ;
			echo "<p><strong>". affiche_nom_formation($id_dossier, $cnx) . "</strong></p>\n" ;
			echo "<p class='msgok'>Les questions ont été créées.</p>\n" ;
			affiche_questions($id_dossier, $cnx) ;
		}
		foreach($tab_question as $question) {
			$query = "INSERT INTO question(id_question, texte_quest,id_modos)
				VALUES ('','".mysqli_real_escape_string($cnx, $question)."','$id_dossier')" ;
			mysqli_query($cnx, $query) ;
		}
	}
	else {
		echo "<p class='erreur'>Il existe déjà un modèle de dossier de candidature pour cette formation !</p>";
	}
		
}
// Ajouter nouveau moéles questions dans la table
function addBase_question($id_dossier, $tab_question, $cnx)
{
	foreach($tab_question as $question)
	{
		$query = "INSERT INTO question(id_question, texte_quest,id_modos)
			VALUES ('', '".mysqli_real_escape_string($cnx, $question)."', '$id_dossier')" ;
		mysqli_query($cnx, $query) ;
	}
	echo "<p class='msgok'>Les questions ont été ajoutées :</p>" ;
	echo "<p><strong>". affiche_nom_formation($id_dossier, $cnx) . "</strong>\n&nbsp; " ;
	echo "<a href='/questions/index.php?dossier=$id_dossier&amp;operation=modif'>Modifier</a></p>\n" ;
	affiche_questions($id_dossier, $cnx) ;
}
function affiche_nom_formation($id_modos, $cnx)
{
	$req= "SELECT intitule FROM mod_dossier, atelier
		WHERE atelier.id_atelier=mod_dossier.id_atelier
		AND id_modos=$id_modos" ;
	$res = mysqli_query($cnx, $req) ;
	$ligne = mysqli_fetch_assoc($res) ;
	return $ligne["intitule"] ;
}
function affiche_questions($id_modos, $cnx)
{
	$req = "SELECT question.id_question, texte_quest, mod_dossier.id_modos, COUNT(id_reponse) AS N
		FROM mod_dossier, question LEFT JOIN reponse ON question.id_question=reponse.id_question
		WHERE mod_dossier.id_modos=$id_modos
		AND mod_dossier.id_modos=question.id_modos
		GROUP BY question.id_question
		ORDER BY question.id_question" ;
	$res = mysqli_query($cnx, $req) ;

	$n = 0 ;
	echo "<ol>\n" ;
	while ( $ligne = mysqli_fetch_assoc($res) )
	{
		echo "<li><p>".$ligne["texte_quest"]." <span style='color: #777; cursor: help;' title='Nombre de réponses'>(".$ligne["N"].")</span></p></li>\n" ;
		if ( intval($ligne["N"]) != 0  ) {
			$n = intval($ligne["N"]) ;
		}
	}
	echo "</ol>\n" ;
	return $n ;
}



include("inc_formations.php");
include("inc_html.php");

$titre = "Questions" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $htmlJquery ;
echo $htmlMakeSublist ;
echo $dtd2 ;
include("inc_menu.php");
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/questions/index.php'>".$titre."</a>" ;
echo $fin_chemin ;
?>


<?php
include("inc_mysqli.php");
$cnx = connecter();


if ( !isset($_GET["operation"]) OR ( isset($_GET["operation"]) AND ($_GET["operation"] == "dispAll") ) )
{
	$requete = "SELECT id_modos, atelier.id_atelier, groupe, intitule
		FROM mod_dossier, atelier
		WHERE mod_dossier.id_atelier=atelier.id_atelier
		ORDER BY groupe, niveau, intitule" ;
	$result = mysqli_query($cnx, $requete) ;

	echo "<p class='c'><strong><a " ;
	echo "href='index.php?operation=add&etape=1'>" ;
	echo "Ajouter des questions à une formation" ;
	echo "</a></strong></p>\n" ;
	echo "<br />" ;

	echo "<table class='tableau'>\n" ;
	echo "<thead>\n" ;
	echo "<tr>\n" ;
	echo "<th class='invisible'></th>\n" ;
	echo "<th>Formation</th>\n" ;
	echo "<th class='invisible' colspan='2'></th>\n" ;
	echo "</tr>\n" ;
	echo "</thead>\n" ;
	echo "<tbody>\n" ;

	$groupe_precedent = "" ;
	while( $ligne = mysqli_fetch_assoc($result) )
	{
		if ( $ligne["groupe"] != $groupe_precedent ) {
			echo "<tr class='groupe'>\n<td class='invisible'></td>\n" ;
			echo "<td style='background: #333; color: #fff;'>".$ligne["groupe"]."</td>\n" ;
			echo "<td class='invisible' colspan='2'></tr>\n" ;
		}
		$groupe_precedent = $ligne["groupe"] ;

		$lien = "index.php?dossier=".$ligne["id_modos"] ;

		echo "<tr>\n" ;
		echo "<td><a href='".$lien."&operation=del'>Supprimer</a></td>\n";
		echo "<td><b>".$ligne["intitule"]."</b></td>\n" ;
		echo "<td><a href='".$lien."&operation=modif'>Modifier</a></td>\n";
		echo "<td><a href='".$lien."&operation=disp'>Afficher</a></td>\n";
		echo "</tr>\n" ;
	}
	echo "</tbody>\n" ;
	echo "</table>\n" ;
}

// Afficher formulaire d'ajout d'un dossier
if ( isset($_GET["operation"]) AND ($_GET["operation"] == "add") )
{
	// Ajout étape 1
	if ( $_GET["etape"] == 1 )
	{
		?><form action="index.php?operation=add&etape=2" method='post'>
		<p class='c'><b>Etape 1/2</b></p><?php
		echo "<table class='formulaire'>\n" ;
		echo "<tr>\n" ;
		echo "<th>Formation : </th>\n" ;
		echo "<td>" ;
		$formForma = chaine_liste_formations("intit_at", "", "", $cnx) ;
		echo $formForma["form"] ;
		echo $formForma["script"] ;
//		liste_formations($cnx, "intit_at", "") ;
//		liste_formations($cnx, "intit_at", $id_atelier) ;
		echo "</td>\n" ;
		echo "</tr><tr>\n" ;
		echo "<th>Nombre de questions : </th>\n<td>" ;
		echo "<input type='text' name='nbr_question' size='2' maxlength='4'" ;
		echo " /></td></tr>\n" ;
		echo "</table>\n" ;
		echo "<p class='c'><input type='submit' value='Etape 2/2' /></p>\n" ;
		?></form><?php
	}
	// Afficher formulaire de saisie des différentes questions
	elseif ($_GET["etape"] == 2 )
	{
		?><p class='c'><b>Etape 2/2</b></p>
		<form action="index.php?operation=addBase" method="post"><?php
		echo "<table class='formulaire'>\n" ;
		$i = 1 ;
		while ( $i <= $_POST["nbr_question"] ) {
			echo "<tr>\n" ;
			echo "\t<th>Question : </th>\n" ;
			echo "\t<td><textarea name='question[]' cols='40' rows='4'>" ;
			echo "</textarea></td>\n" ;
			echo "</tr>\n" ;
			$i++ ;
		}
		echo "</table>\n" ;
		//echo "<input type='hidden' name='id_atelier' value='".$_POST["intit_at"]."' />\n" ;
		?>
		<input type="hidden" name="id_atelier" value="<?php echo $_POST["intit_at"]; ?>" />
		<input type="hidden" name="nbr_question" value="<?php echo $_POST["nbr_question"]; ?>" />
		<input type="hidden" name="id_dossier" value="<?php echo $_POST["id_dossier"]; ?>"/>
		<input type="hidden" name="add_dossier" value="<?php echo $_POST["add_dossier"];?>" />
		<p class='c'><input type="submit" value="Valider"></p>
		</form><?php
	}
} // Fin ajout

//Modifier des questions existantes
if ( isset($_GET["operation"]) AND ($_GET["operation"] == "modif") )
{
	$req = "SELECT question.id_question, texte_quest, mod_dossier.id_modos, COUNT(id_reponse) AS N
		FROM mod_dossier, question LEFT JOIN reponse ON question.id_question=reponse.id_question
		WHERE mod_dossier.id_modos=".$_GET["dossier"]."
		AND mod_dossier.id_modos=question.id_modos
		GROUP BY question.id_question
		ORDER BY question.id_question" ;
	$res = mysqli_query($cnx, $req) ;

	
	echo "<p class='c'><strong>". affiche_nom_formation($_GET["dossier"], $cnx) . "</strong></p>\n" ;
	?>
	<form action="index.php?operation=modifBase&op_modif=modifquestion" method="post">
	<?php
	$i=1;
	$j=0;
	$nombre_reponses = 0 ;
	echo "<table class='formulaire'>\n" ;
	while ( $ligne = mysqli_fetch_array($res) )
	{
		echo "<tr>\n" ;
		echo "\t<th>Question $i : </th>\n" ;
		echo "\t<td><textarea name='question[]' cols='40' rows='4'>" ;
		echo $ligne["texte_quest"] ;
		echo "</textarea>\n" ;
		echo "\t<input type='hidden' name='index_quest[]' value='".$ligne["id_question"]."' />\n" ;
		echo "\t<input type='hidden' name='id_dossier' value='".$ligne["id_modos"]."' />" ;
		echo "</td>\n" ;
		echo "<td style='cursor: help;' title='Nombre de réponses'>".$ligne["N"]."</td>\n" ;
		echo "</tr>\n" ;
		$i++;
		$j++;
		if ( intval($ligne["N"]) != 0 ) {
			$nombre_reponses = intval($ligne["N"]) ;
		}
	}
	?>
	</table>
	<?php
	if ( $nombre_reponses != 0 ) {
		echo "<p class='c erreur'>Il y a déjà des réponses à ces questions.</p>\n" ;
	  	echo "<p class='c erreur'>Si les candidatures à la promotion précédente sont closes et<br />" ; 
		echo "s'il n'y a pas encore de candidature pour la promotion suivante ou en cours,<br />
			il est préférable (pour la cohérence des anciens dossiers de candidature)<br />
			de supprimer ces questions et de les recréer.</p>\n" ;
	}
	?>
	<p class='c'><input type="submit" value="Modifier" /></p>
	</form>
	<hr />
	<?php
	//Ajouter des nouvelles questions
	?>
	<form action="index.php?operation=add&etape=2" method='post'>
	<table class='formulaire'><tr>
	<th>Nombre de questions à ajouter :</th>
	<td><input type="text" name="nbr_question" size='2' maxlength='4' /></td>
	</tr></table>
	<input type="hidden" name="id_dossier" value="<?php echo $_GET["dossier"] ; ?>">
	<input type="hidden" name="add_dossier" value="ok">
	<p class='c'><input type="submit" value="Ajouter"></p>
	</form>
	<?
}

if ( isset($_GET["operation"]) AND ($_GET["operation"] == "addBase") )
{
	
	/*
	$id_atelier = $_POST["id_atelier"] ;
	$req="SELECT id_atelier FROM atelier WHERE intitule='$id_atelier'";
	$atelier=get_result($req);
	$index=$atelier[0]->id_atelier;
	*/
	// Ajout de questions alorq qu'il n'y en a pas.
	if ( $_POST["add_dossier"] != "ok" ) {
		addBase_dossier($_POST['id_atelier'], $_POST["question"], $cnx) ;
	}
	// Ajout de questions alors qu'il y en a
	else {
		addBase_question($_POST["id_dossier"], $_POST["question"], $cnx) ;
	}
}

if ( isset($_GET["operation"]) AND ($_GET["operation"] == "modifBase") )
{
	modif_dossier($_POST["id_dossier"], $_POST["question"],
		$_GET["op_modif"], $_POST["index_quest"], $cnx);
}



if ( isset($_GET["operation"]) AND ($_GET["operation"] == "disp") )
{
	echo "<p><strong>". affiche_nom_formation($_GET["dossier"], $cnx) . "</strong></p>\n" ;
	affiche_questions($_GET["dossier"], $cnx) ;
}

// Suppression (avant conformation)
if	(
		( isset($_GET["operation"]) AND ($_GET["operation"] == "del") )
		AND ( !isset($_GET["confirme"]) )
	)
{
	echo "<p><strong>". affiche_nom_formation($_GET["dossier"], $cnx) . "</strong></p>\n" ;
	$n = affiche_questions($_GET["dossier"], $cnx) ;
	if ( $n > 0 ) {
		echo "<p class='erreur'>Il y des réponses à ces questions dans les dossiers de candidature.</p>\n" ;
		echo "<p class='erreur'>Vous ne devez pas supprimer ces questions si les candidatures à la promotion en cours ne sont pas closes.</p>\n" ;
	}
	echo "<p><strong><a href='index.php?dossier=".$_GET["dossier"]."&amp;operation=del&amp;confirme=oui'>Supprimer</a></strong></p>" ;
}
// Suppression (confirmation)
if	(
		( isset($_GET["operation"]) AND ($_GET["operation"] == "del") )
		AND ( isset($_GET["confirme"]) AND ($_GET["confirme"] == "oui") )
	)
{
	echo "<p><strong>". affiche_nom_formation($_GET["dossier"], $cnx) . "</strong></p>\n" ;
	$req = "DELETE FROM mod_dossier WHERE id_modos='".$_GET["dossier"]."'" ;
	mysqli_query($cnx, $req) ;
	$req1 = "DELETE FROM question WHERE id_modos='".$_GET["dossier"]."'" ;
	mysqli_query($cnx, $req1);
	echo "<p class='msgok'>Les questions ont été supprimées.</p>" ;
}


deconnecter($cnx);
echo $end ;
?>
