<?php
include("inc_session.php") ;

include("inc_html.php") ;
$titre = "Imputations" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;
?>

<p>Vous devez activer javascript dans votre navigateur pour que le lien
«&nbsp;Imprimer&nbsp;» fonctionne.</p>

<p>Vous pouvez aussi imprimer les attestations de paiement en passant par le menu
«&nbsp;Fichier&nbsp;» de votre navigateur.</p>

<?php
echo $end ;
?>
