<?php
include("inc_session.php") ;
if ( intval($_SESSION["id"]) > 3 ) {
    header("Location: /bienvenue.php") ;
}

include("inc_html.php") ;
$titre = "Résultats des inscrits" ;
echo $dtd1 ;
echo "<title>$titre</title>\n" ;
echo "<style type='text/css' media='all'>
table.tabres {
	border-collapse: separate;
	border-collapse: collapse;
	border: 1px solid #ccc;
}
table.tabres caption {
	text-align: center;
	font-weight: bold;
	font-size: smaller;
	margin: 1em 0 0.5em 0;
}
table.tabres tbody {
}
table.tabres tbody tr {
}
table.tabres tbody tr td {
	margin: 0;
	padding: 1px 5px;
	border: 0px;
	text-align: center;
}
table.tabres tbody tr td.l {
	text-align: left;
}
table.tabres tbody.ab tr td {
	background: #ddd;
}
table.tabres tbody.aj tr td {
	background: #fff;
}
table.tabres tbody.re tr td {
	background: #ddd;
}
table.tabres tbody.di tr td {
	background: #fff;
}
p.p {
	margin: 0.4em 0;
}
ul.p {
	margin: 0.4em 0 0.4em 1.5em;
}
ul.p li {
	margin: 0.3em 0;
}
</style>\n" ;
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

<h1><?php echo $titre ; ?></h1>

<p class='p'>Le résultat d'un inscrit est un champ à renseigner pour tous les candidats&nbsp;:</p>
<ul class='p'>
<li>imputés 
(<span class='allocataire'>Allocataire</span> <span class="paye"><?php echo LABEL_INSCRIT ; ?></span>
ou <span class='payant'>Payant</span> <span class="paye"><?php echo LABEL_INSCRIT ; ?></span>
ou <span class='scac'>Allocataire SCAC</span> <span class="paye"><?php echo LABEL_INSCRIT ; ?></span>)&nbsp;;</li>
<li>et <span class='payantetab'>Payant établissement</span>.</li>
</ul>
<p class='p'>Ce champ résultat peut prendre 5 valeurs :</p>
<ul class='p'>
<li> &nbsp; (vide) : valeur à renseigner,</li>
<li><span class='abandon'>Abandon</span> : abandon au début, pendant, ou avant l'examen final ;</li>
<li><span class='ajourne'>Ajourné</span> : n'a pas, ou n'as pas encore, obtenu son diplôme ;</li>
<li><span class='reinscrit'>Réinscrit</span> : a validé une partie du diplôme et a été autorisé à se réinscrire pour terminer sa formation (candidat qui étale sa formation sur plusieurs années) ;</li>
<li><span class='diplome'>Diplômé</span> : a obtenu son diplôme.<br />
	Cette valeur doit toujours être associée à un autre champ contenant l'année d'obtention du diplôme.<br />
	Les deux valeurs sont affichées en même temps : <span class='diplome'>Diplômé 20XX</span>.<br />
	Quand ce diplôme a été ajouté à l'annuaire des anciens : <span class='diplome_ancien'>Diplômé 20XX</span>.	</li>
</ul>


<div style='float: right; margin-left: 1em;'>
<table class='tabres c'>
<caption>7 cas groupés par résultat initial</caption>
<tbody class='ab'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='abandon'>Abandon</span></td>
		<td></td>
		<td></td>
	</tr>
</tbody>
<tbody class='aj'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
	</tr>
</tbody>
<tbody class='re'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='abandon'>Abandon</span></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
	</tr>
</tbody>
<tbody class='di'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
		<td></td>
		<td></td>
	</tr>
</tbody>
</table>

<table class='tabres c'>
<caption>7 cas groupés par résultat final</caption>
<tbody class='ab'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='abandon'>Abandon</span></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='abandon'>Abandon</span></td>
	</tr>
</tbody>
<tbody class='aj'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
	</tr>
</tbody>
<tbody class='re'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
	</tr>
</tbody>
</table>
</div>



<!--
<table class='tabres c'>
<tbody class='ab'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='abandon'>Abandon</span></td>
		<td></td>
		<td></td>
		<td>(pas de changement)</td>
		<td class='l'></td>
	</tr>
</tbody>
<tbody class='aj'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
		<td></td>
		<td></td>
		<td>(pas de changement)</td>
		<td rowspan='2' class='l'></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
		<td></td>
	</tr>
</tbody>
<tbody class='re'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='abandon'>Abandon</span></td>
		<td></td>
		<td rowspan='3' class='l'></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='ajourne'>Ajourné</span></td>
		<td></td>
	</tr>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='reinscrit'>Réinscrit</span></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
		<td></td>
	</tr>
</tbody>
<tbody class='di'>
	<tr>
		<td><code>&nbsp;</code></td>
		<td>&rarr;</td>
		<td><span class='diplome'>Diplômé</span></td>
		<td></td>
		<td></td>
		<td>(pas&nbsp;de&nbsp;changement)</td>
		<td class='l'></td>
	</tr>
</tbody>
</table>
-->

<p>Indépendamment des erreurs de saisie,
il existe 7 cas d'évolution du résultat d'un candidat inscrit&nbsp;:</p>

<p>Par conséquent, à terme, il ne doit rester que 3 valeurs de résultat :
<span class='abandon'>Abandon</span>,
<span class='ajourne'>Ajourné</span> ou
<span class='diplome'>Diplômé</span>.</p>

<ol>

<li>
<p>En conservant l'historique des changements du résultat d'un candidat, on pourra différencier ces 7 cas.<br />
Mais on ne pourra faire facilement des statistiques que sur :<br />
- les 3 valeurs de résultat final,<br />
&nbsp; ou les 4 (ou 5, valeur vide comprise) valeurs possibles de résultat à un instant donné,<br />
- et pour les diplômés, sur l'année d'obtention du diplôme (N+1 ou N+2, etc).</p>

<p>Il ne sera, par exemple, pas facile du tout de différencier dans les statistiques&nbsp;:<br />
- les <span class='ajourne'>Ajourné</span> &rarr; <span class='diplome'>Diplômé</span>
  des <span class='reinscrit'>Réinscrit</span> &rarr; <span class='diplome'>Diplômé</span>,<br />
- ni les <span class='ajourne'>Ajourné</span> des <span class='reinscrit'>Réinscrit</span> &rarr; <span class='ajourne'>Ajourné</span>.</p>

<p>On ne pourrait donc différencier les <span class='ajourne'>Ajourné</span> des <span class='reinscrit'>Réinscrit</span>&nbsp;:<br />
- qu'individuellement (en consultant leur historique dans leur dossier),<br />
- ou dans les statistiques non définitives, mais pas dans les statistiques des résultats finaux.</p>

<p>Par conséquent, le seul intérêt que je vois à cet état <span class='reinscrit'>Réinscrit</span>,
c'est qu'il permettrait, dans le tableau des «&nbsp;Examens&nbsp;»,
d'ajouter le nombre des réinscrits des années antérieures au nombre d'imputés et au nombre de payant établissement.</p>

<p>Je pense que conserver l'historique des changements du résultat d'un candidat est de toute manière utile pour repérer d'éventuelles erreurs.</p>

<p>Ces limites de la valeur <span class='reinscrit'>Réinscrit</span> et des statistiques conviennent-elles ?</p>
</li>

<li>Par ailleurs : ne faudrait-il pas ajouter, pour les <span class='diplome'>Diplômé</span>, un champ mention au champ année ?<br />
(passable, assez-bien, bien, très bien, excellent)
</li>

</ol>

<!--
<br />
<br />
<br />

<p><code>dossier.resultat</code></p>
-->

<?php
echo $end ;
?>


