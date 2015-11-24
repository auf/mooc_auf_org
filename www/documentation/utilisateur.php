<?php
include("inc_session.php") ;

include("inc_html.php") ;
$titre = "Documentation pour utilisateurs" ;
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

<p>Cette plateforme permet la gestion en ligne d'inscriptions...</p>


<h2>Pièces jointes</h2>

Formats autorisés : Image au formats JPEG ou PNG.

Pas de code javascript pour l'affichage des images pour les inscrits.



<h2>Du point de vue des inscrits</h2>

<p>Cette plateforme est construite autour de dossiers d'inscription
qui contiennent&nbsp;:</p>

<ol>
<li>Les réponses à un court questionnaire, paramétrable...,</li>
<li>d'éventuels fichiers joints par le candidat.</li>
</ol>

<p>Un individu qui dépose un dossier de pré-inscription reçoit automatiquement son numéro de
dossier et le mot de passe correspondant par courrier électronique.<br />
Il peut ainsi modifier son dossier d'inscription tant qu'il n'est pas effectivement
inscrit, c'est à dire jusqu'à son imputation.</p>

<p>Mais un même individu qui dépose plusieurs dossiers d'inscription
doit à chaque fois répondre intégralement au questionnaire.</p>

<p>Ce questionnaire inclut notamment l'adresse électronique, et, optionnellement, un identifiant
d'enregistrement au MOOC.
Ces deux champs sont particuliers dans la mesure où&nbsp;:</p>
<ul>
<li>Le système assimile la seconde inscription à la même promotion
	avec une même adresse électronique ou un même numéro d'enregistrement au MOOC
	à un doublon, et il empêche le dépôt de cette seconde inscription.</li>
<li>Lorsque le système affiche un dossier d'inscription, il affiche aussi la liste
	des autres inscriptions pour la même adresse électronique.</li> 
</ul>




<h2>Les utilisateurs de la plateforme (autres que les inscrits)</h2>

<ul>
<li>Plusieurs niveaux d'accès définis dans le code&nbsp;:
	<ul>
	<li>pour l'AUF&nbsp;:
		<ul style="margin-top: 0.5em;">
		<li>Un accès (identifiant, mot de passe) <strong>administrateur</strong>,
			qui permet d'accéder à l'ensemble des fonctionnalités de la plateforme.
			</li>
		<li>Gestionnaires AUF</li>
		<li>CNF</li>
		</ul>
	</li>
	<li>Visiteur</li>
	</ul>
</li>
<li>Des accès enregistrés dans la base de données&nbsp;:<br />
	L'administrateur peut créer pour chaque formation, un ou plusieurs
	accès (identifiant, mot de passe) pour un ou plusieurs
	<strong>responsables</strong>.
	Un responsable à la possibilité de commenter et de modifier l'état
	des candidatures aux formations dont il est responsable.
</li>
</ul>

<!--

<h2>Devenir d'une candidature</h2>

<p>Le devenir des candidatures est caractérisé par trois possibilités
d'interventions sur elles&nbsp;:</p>

<ul>
<li>Tant que les candidatures sont possibles, des candidats peuvent
	soumettre un nouveau dossier de candidature ou mettre à jour celui
	qu'ils ont soumis.</li>
<li>Tant que les évaluations sont possibles, les responsables peuvent
	commenter et modifier l'état des candidatures de leurs promotions.</li>
<li>Tant que les imputations sont possibles, ...</li>
</ul>

<p>Quand aucun de ces types d'intervention n'est possible, les candidatures
à une promotion </p>



<h2>Droits d'accès aux fonctionnalités</h2>

-->

<table class='tableau'>

<tr>
<td class="invisiible"></td>
<th colspan="4">Utilisateurs définis dans le code</th>
<th>Utilisateurs enregistrés dans la base de données</th>
</tr>
<tr>
<td></td>
<th>Administrateur</th>
<th>Gestionnaires AUF</th>
<th>CNF</th>
<th>Visiteur</th>
<th>Responsables</th>
</tr>

<tr>
<th>Consulter les inscriptions (listes, statistiques, exports, recherche)</th>
<td colspan="4">Toutes</td>
<td>Inscriptions à ses formations</td>
</tr>

<!--
<tr>
<th>Commenter les candidature</th>
<td colspan="3">Champ commentaire commun pour toutes les candidatures</td>
<td class='Non'>Non</td>
<td>Champ commentaire individuel pour chaque candidature à ses formations</td>
</tr>
-->

<tr>
<th>Modifier le résultat des inscrits</th>
<td>Toutes</td>
<td>Toutes</td>
<td class='Non'>Non</td>
<td class='Non'>Non</td>
<td>Inscrits à ses formations</td>
</tr>

<tr>
<th>Utiliser la messagerie</th>
<td>Oui</td>
<td>Oui</td>
<td class='Non'>Non</td>
<td class='Non'>Non</td>
<td>Pour les inscrits aux formations dont il responsable</td>
</tr>

<tr>
<th>Créer une nouvelle formation, une nouvelle promotion/inscription de cette formation,<br />
	un accès responsable pour cette formation.</th>
<td>Oui</td>
<td colspan="3" class='Non'>Non</td>
<td class='Non'>Non</td>
</tr>

<tr>
<th></th>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

</table>






<!--
<h2>La navigation</h2>

<dl>
<dt><strong>Formations</strong></dt>
	<dd>Les formations sont classées par <em>groupe</em>
	puis par <em>niveau</em> (3.DU, 3.L3, 4M1, 5.DU, 5.M2 ou 8.D).
	</dd>
<dt><strong>Promotions</strong>
	<dd>Pour chaque formation, on peut créer une ou plusieurs promotions.
	C'est ici que l'on ouvre et que l'on met fin, pour chaque promotion,
	aux candidatures (possibilité pour les candidats de déposer une
	candidature , aux évaluations et aux imputations.
	 </dd>
<dt><strong>Questions</strong></dt>
	<dd></dd>
<dt><strong>Responsables</strong></dt>
	<dd></dd>
<dt><strong>Recherche</strong></dt>
	<dd></dd>
<dt><strong>Candidature en ligne</strong></dt>
	<dd></dd>
<dt><strong>Statistiques</strong></dt>
	<dd></dd>
<dt><strong>Gestion des candidatures</strong></dt>
	<dd></dd>
<dt><strong>Exports (tableur)</strong></dt>
	<dd></dd>
<dt><strong>Messagerie</strong></dt>
	<dd></dd>
<dt><strong>Imputations</strong></dt>
	<dd></dd>
<dt><strong>Examens</strong></dt>
	<dd></dd>
</dl>

-->

<?php
echo $end ;
?>


