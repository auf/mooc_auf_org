<?php
include("inc_html.php") ;
$titre = "Raccourcis claviers dans Firefox" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo $dtd2 ;
echo "<div id='page'>" ;

echo "<h1>$titre</h1>\n" ;
?>

<p>Les raccourcis claviers (<em>accesskeys</em> dans le jargon)
sont devenues <kbd>Alt</kbd> + <kbd>Shift</kbd> + <kbd>clef</kbd> (<kbd>clef</kbd> étant une lettre ou un chiffre)
dans Firefox version 2.</p>

<p>Dans Firefox version 1, c'était par défaut <kbd>Alt</kbd> + <kbd>clef</kbd>.
Plus simple donc (2 touches au lieu de 3).</p>

<p>Pour revenir à la configuration par défaut de Firefox 1 dans Firefox 2&nbsp;:

<ol>
<li>Allez à cette URL (recopier ceci dans la barre d'adresse)&nbsp;:
	<pre>about:config</pre></li>
<li>Choisissez comme filtre : <code>ui.key.generalAccessKey</code>
	et affectez lui la valeur 18</li>
</ol>

</div>
</body>
</html>

