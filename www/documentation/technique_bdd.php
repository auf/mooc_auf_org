<?php
include("inc_session.php") ;

include("inc_html.php") ;
$titre = "Documentation technique" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='index.php'>Documentation</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre;
echo $fin_chemin ;
?>



<h2>Base de données</h2>

<div style='float: right; margin-left: 1em;'>
<img src='mooc.png' width='642' height='847' alt='mooc' title='mooc' />
</div>

<p>La nomenclature a été un peu améliorée pour les clés étrangères
(<code>ref_session</code>, <code>ref_atelier</code>), mais pas pour
le nom des tables au singulier.</p>

<p>L'agenda des CNF (tables <code>indispos</code> et <code>indispos_cnf</code>)
est dans la base <code>foad</code>.</p>

<p>Le champ <code>dossier.etat_dossier</code> n'a pas changé de nom,
mais il ne contient pas l'état du dossier, mais le résultat.</p>

<p>Le filtre &laquo;&nbsp;État&nbsp;&raquo; ne correspond à aucun champ, mais
au fait qu'il existe, ou non, une imputation correspondant à un dossier.</p>


<?php
echo $end ;
?>


