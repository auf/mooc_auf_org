<?php
include("inc_session.php") ;

include("inc_html.php") ;
$titre = "Documentation technique : Généralités, configuration" ;
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

echo "<h2>$titre</h2>\n" ;

?>

<h3>Arborescence</h3>

<ul>
<li><code>include/</code> : librairies</li>
<li><code>www/</code> : racine du site
	<ul>
	<li><code>...</code></li>
	</ul>
</li>
<li><code>pj/</code> : pièces jointes aux candidatures</li>
</ul>

<?php
echo $end ;
?>


