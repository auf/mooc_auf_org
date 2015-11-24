<?php
include("inc_session.php") ;

include("inc_html.php") ;
$titre = "Documentation technique : Sessions" ;
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


<p>La soumission des candidatures n'utilise aucune variable de session.
Toutes les variables nécessaires sont transmises de page en page.
(On évite ainsi un risque de <i>timeout</i> à cause de problèmes de connexion.)

<h3>Initialisées au moment de l'identification</h3>

<ul>
<li><p><strong>Concernant l'utilisateur identifié</strong></p>
	<table class='simple'><tr>
		<td><code>$_SESSION["authentification"]</code></td>
		<td>doit être <code>oui</code>.</td>
	</tr><tr>
		<td><code>$_SESSION["temps"]</code></td>
		<td>permet de diminuer la durée d'une session par rapport à la configuration de PHP.</td>
	</tr><tr>
		<td><code>$_SESSION["id"]</code></td>
		<td>
		<code>00</code> pour l'administrateur<br />
		<code>01</code> pour les gestionnaires AUF<br />
		<code>02</code> pour les CNF<br />
		<code>03</code> pour le compte Visiteur<br />
		valeur entière <code>&gt; 3</code> pour les responsables
		</td>
	</tr><tr>
		<td><code>$_SESSION["utilisateur"]</code></td>
		<td>Nom de l'utilisateur identifié, affiché en haut à gauche, avant le lien permettant de se déconnecter.</td>
	</tr><tr>
		<td><code>$_SESSION["courriel"]</code></td>
		<td>Courriel de l'utilisateur identifié (éventuellement expéditeur pour la messagerie)</td>
	</tr></table>
	</li>
<li><p><strong>Concernant l'ensemble du système</strong></p>
	<table class='simple'><tr>
		<td><code>$_SESSION["derniere_annee"]</code></td>
		<td>Dernière année pour laquelle il existe des promotions.</td>
	</tr><tr>
		<td><code>$_SESSION["derniere_annee_imputations"]</code></td>
		<td>Dernière année pour laquelle il existe des imputations.</td>
	</tr></table>
	</li>
<li><p><strong>Concernant les responsables uniquement</strong><p>
	<table class='simple'><tr>
		<td><code>$_SESSION["tableau_promotions"]</code><br />
			<code>$_SESSION["liste_promotions"]</td>
		<td>Promotions du responsables pour lesquelles <code>evaluations='Oui'</code></td>
	</tr><tr>
		<td><code>$_SESSION["tableau_toutes_promotions"]</code><br />
			<code>$_SESSION["liste_toutes_promotions"]</code></td>
		<td>Toutes les promotions du responsable</td>
	</tr></table>
	</li>
</ul>


<h3>Initialisées après l'identification</h3>

<ul>
<li><strong>Mémorisation des filtres appliqués dans chaque page</strong>
	(du type <code>$_SESSION["filtres"]["</code>PAGE<code>"]["</code>VARIABLE<code>"]</code>)
	<ul style='margin-top: 0;'>
	<li><strong>Pages d'administration</strong>
	<table class='simple'><tr>
		<td><code>$_SESSION["filtres"]["institutions"]</code></th>
		<td>Institutions</td>
		<td><code>"pays", "qualite", "statut", "tri"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["formations"]</code></th>
		<td>Formations</td>
		<td><code>"annee", "groupe", "niveau", "ref_institution", "intitule"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["promotions"]</code></th>
		<td>Promotions</td>
		<td><code>"annee", "groupe", "exam"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["responsables"]</code></th>
		<td>Responsables</td>
		<td><code>"ref_institution", "nom", "login", "email", "tri"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["individus"]</code></th>
		<td>Individus (CNF)</td>
		<td><code>"region", "pays", "cnf", "actif", "tri"</code></th>
	</tr></table>
	</li>
	<li><strong>Autres pages</strong>
	<table class='simple'><tr> 
		<td><code>$_SESSION["filtres"]["recherche"]</code><br />
			<code>$_SESSION["filtres"]["recherche_precedente"]</code></th>
		<td>Recherche</td>
		<td><code></code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["inscrits"]</code></th>
		<td>Gestion des inscrits</td>
		<td><code>"annee", "groupe" ; "etat", "pays", "nom", "max", "tri"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["statistiques"]</code></th>
		<td>Statistiques</td>
		<td><code>"annee", "region", "pays", "etat", "limiter", "details"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["messagerie"]</code></th>
		<td>Messagerie (inscrits)</td>
		<td><code>"annee", "groupe"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["imputations"]</code></th>
		<td>Imputations (statistiques, listes et exports)</td>
		<td><code>"promotion", "lieu", "annee", "etat", "tri", "latin1"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["examens"]</code></th>
		<td>Examens</td>
		<td><code>"lieu", "pays", "debut", "fin"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["agendaCNF"]</code></th>
		<td>Agenda</td>
		<td><code>"mois", "annee", "lieu"</code></th>
	</tr><tr>
		<td><code>$_SESSION["filtres"]["messagerie_cnf"]</code></th>
		<td>Messagerie (CNF)</td>
		<td><code>"annee", "groupe"</code></th>
	<!--
	</tr><tr>
		<td><code>$_SESSION["filtres"][""]</code></th>
		<td></td>
		<td><code></code></th>
	-->
	</tr></table>
	</li>
	</ul>
</li>
<li><strong>Messagerie (candidats) : message en cours de rédaction</strong>
	<table class='simple'><tr>
		<td><code>$_SESSION["messagerie"]</code></td>
		<td></td>
	</tr></table>
	</li>
</ul>


<?php
echo $end ;
?>


