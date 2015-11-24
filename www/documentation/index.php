<?php
include_once("inc_session.php") ;

require_once("inc_html.php");
$titre = "Documentation" ;
echo $dtd1 ;
echo "<title>".$titre."</title>" ;
echo $dtd2 ;
require_once("inc_menu.php");
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

echo "<h2>$titre</h2>\n" ;
?>


<h3>Ébauche de documentation sur le fonctionnement de la plateforme</h3>
<ul>
<li><a href='utilisateur.php'>Documentation pour utilisateurs</a></li>
</ul>

<h3>Ébauche de documentation technique</h3>

<ul>
<li><a href='technique_general.php'>Généralités, configuration</a></li>
<li><a href='technique_bdd.php'>Base de données</a></li>
<li><a href='technique_session.php'>Sessions</a></li>
</ul>


<?php
echo $end ;
?>	
