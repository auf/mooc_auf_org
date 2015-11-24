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
<img src='foad.png' width='777' height='706' alt='foad' title='foad' />
</div>

<ul>
<li>La structure et le nommage ne sont pas particulièrement cohérents
	pour des raisons historiques...</li>
<li>A partir des candidatures 2007, <code>id_dossier</code> est égal à
	<code>id_candidat</code>.<br />
	(Les défauts d'intégrité ont été corrigés au prix de la suppression
	de quelques données antérieures à 2006.)</li>
</ul>


<h2 style='clear: both;'>Configuration</h2>

<?php
/*
<h3>Configuration de PHP</h3>

<ul>
<li><code>register_globals</code> <code>Off</code></li>
<li><code>magic_quotes_gpc</code> <code>On</code></li>
<li><code>magic_quotes_runtime</code> <code>Off</code></li>
</ul>

<h3>Configuration d'Apache</h3>

<pre>
&lt;VirtualHost foad.refer.org:443&gt;
    ServerName foad.refer.org
    NameVirtualHost 81.80.123.40:443
    ServerAdmin cedric.musso@labor-liber.org
    <strong>DocumentRoot</strong> /var/www/foad.refer.org/ssl/<strong>www/</strong>
    <strong>php_admin_value include_path</strong> "/var/www/foad.refer.org/ssl/<strong>include/</strong>"
    <strong>php_admin_value register_globals Off
    php_value magic_quotes_gpc 1
    AddDefaultCharset iso-8859-1</strong>
    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/foad.refer.org/foad.refer.org.cert
    SSLCertificateKeyFile /etc/apache2/ssl/foad.refer.org/foad.refer.org.key
    ErrorLog /var/log/apache2/foad.refer.org-ssl-error.log
    LogLevel warn
&lt;/VirtualHost&gt;
&lt;Directory /var/www/foad.refer.org/ssl/www/&gt;
    Options None
&lt;/Directory&gt;
</pre>

*/
?>

<h2>Variables de session</h2>

<p>La soumission des candidatures n'utilise aucune variable de session.
Toutes les variables nécessaires sont transmises de page en page.
(On évite ainsi un risque de <i>timeout</i> à cause de problèmes de connexion.)

<h3>Initialisées au moment de l'identification</h3>

<h4>Concernant l'utilisateur identifié</h4>

<table class='simple'>
<tr>
	<td><code>$_SESSION["authentification"]</code></td>
	<td>doit être <code>oui</code>.</td>
</tr><tr>
	<td><code>$_SESSION["temps"]</code></td>
	<td>permet de diminuer la durée d'une session par rapport à la configuration de PHP.</td>
</tr><tr>
	<td><code>$_SESSION["id"]</code></td>
	<td>
	<code>00</code> pour l'administrateur<br />
	<code>01</code> pour les services des bourses<br />
	<code>02</code> pour les CNF<br />
	<code>03</code> pour le compte SCAC<br />
	valeur entière <code>&gt; 3</code> pour les responsables
	</td>
</tr><tr>
	<td><code>$_SESSION["utilisateur"]</code></td>
	<td>Nom de l'utilisateur identifié, affiché en haut à gauche, avant le lien permettant de se déconnecter.</td>
</tr><tr>
	<td><code>$_SESSION["courriel"]</code></td>
	<td>Courriel de l'utilisateur identifié (éventuellement expéditeur pour la messagerie)</td>
</tr>
</table>


<h4>Concernant l'ensemble du système</h4>

<ul>
<li><code>$_SESSION["transferable"]</code> : Tableau contenant les identifiants des promotions pour lesquelles les candidats sont transférables.</li>
<li><code>$_SESSION["derniere_annee"]</code> : la dernière année pour laquelle il existe des promotions.</li>
<li><code>$_SESSION["derniere_annee_imputations"]</code> : la dernière année pour laquelle il existe des imputations.</li>
</ul>

</ul>

<h4>Concernant les responsables uniquement</h4>

<ul>
<li><code>$_SESSION["transfert"]</code> : possibilité de transfert d'une promotion à une autre (<code>Oui</code> ou <code>Non</code>)</li>


<li><code>$_SESSION["tableau_promotions"]</code> : promotions du responsables pour lesquelles <code>evaluations='Oui'</code></li>
<li><code>$_SESSION["liste_promotions"]</code> : </li>
<li><code>$_SESSION["tableau_toutes_promotions"]</code> : toutes les promotions du responsable</li>
<li><code>$_SESSION["liste_toutes_promotions"]</code> : </li>


</ul>

<h3>Initialisées après l'identification</h3>

<p>Ces variables permettent de conserver les derniers choix faits d'une
page à l'autre au cours d'une session.</p>

<table class='tableau'>
<caption>Mémorisation des filtres appliqués dans chaque page, <code>$_SESSION["filtres"]["</code>PAGE<code>"]["</code>VARIABLE<code>"]</code></caption>
<thead>
<tr>
	<th>Page</th>
	<th>Tableau associatif</th>
	<th>Variables (clés des variables)</th>
</tr>
</thead>
<tbody>
<tr>
	<th colspan='3'>Pages d'administration</th>
</tr>
<tr>
	<td>Institutions</th>
	<td><code>$_SESSION["filtres"]["institutions"]</code></th>
	<td><code>"pays", "tri"</code></th>
</tr>
<tr>
	<td>Formations</th>
	<td><code>$_SESSION["filtres"]["formations"]</code></th>
	<td><code>"annee", "groupe", "niveau", "ref_institution", "intitule"</code></th>
</tr>
<tr>
	<td>Promotions</th>
	<td><code>$_SESSION["filtres"]["promotions"]</code></th>
	<td><code>"annee", "groupe", "exam"</code></th>
</tr>
<tr>
	<td>Responsables</th>
	<td><code>$_SESSION["filtres"]["responsables"]</code></th>
	<td><code>"nombre", "ref_institution", "nom", "login", "tri"</code></th>
</tr>
<tr>
	<td>Individus (CNF)</th>
	<td><code>$_SESSION["filtres"]["individus"]</code></th>
	<td><code>"cnf", "pays", "actif", "tri"</code></th>
</tr>
<tr>
	<th colspan='3'>Autres pages</th>
</tr>
<tr>
	<td></th>
	<td><code>$_SESSION["filtres"][""]</code></th>
	<td><code></code></th>
</tr>
<tr>
	<td></th>
	<td><code>$_SESSION["filtres"][""]</code></th>
	<td><code></code></th>
</tr>
<tr>
	<td></th>
	<td><code>$_SESSION["filtres"][""]</code></th>
	<td><code></code></th>
</tr>
<!--
<tr>
	<td></th>
	<td><code>$_SESSION["filtres"][""]</code></th>
	<td><code></code></th>
</tr>
-->
</tbody>
</table>


<ul>
<li>Recherche :
	<ul>
	<li><code>$_SESSION["rechercher"]</code> (<code>Array</code>)</li>
	<li><code>$_SESSION["recherche_precedente"]</code> (<code>Array</code>)</li>
	</ul>
</li>
<li>Candidatures :
	<code>$_SESSION["c_annee"]</code>,
	<code>$_SESSION["c_groupe"]</code><br />
	<code>$_SESSION["c_tri"]</code>,
	<code>$_SESSION["c_etat"]</code>,
	<code>$_SESSION["c_pays"]</code>,
	<code>$_SESSION["c_nom"]</code>,
	<code>$_SESSION["c_max"]</code>
</li>
<li>Sélections multiples :
	<code>$_SESSION["selections_multiples"]["annee"]</code>,
	<code>$_SESSION["selections_multiples"]["etat"]</code>
</li>
<li>Statistiques :
	<code>$_SESSION["stats"]["annee"]</code>,
	<code>$_SESSION["stats"]["pays"]</code>,
	<code>$_SESSION["stats"]["etat"]</code>
</li>
<li>Exports :
	<code>$_SESSION["exporter"]</code> (<code>Array</code>) : les champs à exporter.
</li>
<li>Messagerie :
	<code>$_SESSION["m_annee"]</code>,
	<code>$_SESSION["m_groupe"]</code><br />
	<code>$_SESSION["messagerie"]</code>
	(<code>Array</code>, message en cours de rédaction)
</li>
<li>Imputations :
	<code>$_SESSION["i_promotion"]</code>, <code>$_SESSION["i_lieu"]</code>,
	<code>$_SESSION["i_annee"]</code>, <code>$_SESSION["i_etat"]</code>,
	<code>$_SESSION["i_tri"]</code>, <code>$_SESSION["i_utf8"]</code>
</li>
<li>Recherche d'anciens:
	<ul>
	<li><code>$_SESSION["anciens"]</code> (<code>Array</code>)</li>
	<li><code>$_SESSION["anciens_precedente"]</code> (<code>Array</code>)</li>
	</ul>
</li>


</ul>

<?php
echo $end ;
?>


