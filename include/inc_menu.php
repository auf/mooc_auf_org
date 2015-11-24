<div id="navigation">

<div id="identification">
	<div id="logout"><a href="/logout.php">Déconnexion</a></div>
	<div id="login"><?php echo $_SESSION["utilisateur"] ; ?>
		<a id='haut' accesskey='H'></a>
	</div>
	<div id="site">MOOC</div>
</div>

<?php
// Administrateur
if ( $_SESSION["id"] == "00" )
{
	//<a href="/responsables/index.php">Sélectionneurs</a> |
	//<a href="/questions/index.php">Questions</a> |
	//| <a href="/anciens/">Anciens</a>
	//| <a href="/exports/index.php" title="Choix des champs à exporter">Exports</span></a>
	?>
	<a href="/institutions/index.php">Institutions</a> |
	<a href="/formations/index.php">Formations</a> |
	<a href="/promotions/index.php">Inscriptions</a> |
	<a href="/responsables/index.php" title="Responsables de formations">Responsables</a> |
	<a href="/individus/index.php">Individus <span>(CNF)</span></a> |
	<a href="/documentation/index.php">Documentation</a>
	<br />
	<a href="/recherche/">Recherche, export</a> |
	<a href="/en_cours.php">Inscriptions en cours</a> |
	<a href="/statistiques/">Statistiques</a> |
	<a href="/inscrits/index.php">Gestion des inscrits</a>
	| <a href="/messagerie/index.php">Messagerie <span>(inscrits)</span></a>
	| <a href="/imputations/statistiques.php">Imputations</a>
	| <a href="/examens/">Examens</a>
	| <a href="/agendaCNF/" title="Agenda des CNF">Agenda</a>
	| <a href="/messagerie_cnf/index.php">Messagerie <span>(CNF)</span></a>
	<?php
}
// Service des bourses
else if ( $_SESSION["id"] == "01" )
{
	?>
	<a href="/recherche/">Recherche, export</a> |
	<a href="/en_cours.php">Inscriptions en cours</a> |
	<a href="/statistiques/">Statistiques</a> |
	<a href="/inscrits/index.php">Gestion des inscrits</a>
	| <a href="/exports/index.php" title="Choix des champs à exporter">Exports</span></a>
	| <a href="/messagerie/index.php">Messagerie <span>(inscrits)</span></a>
	| <a href="/imputations/statistiques.php">Imputations</a>
	| <a href="/examens/">Examens</a>
	| <a href="/agendaCNF/" title="Agenda des CNF">Agenda</a>
	| <a href="/messagerie_cnf/index.php">Messagerie <span>(CNF)</span></a>
	<?php
	/*
	*/
}
// CNF
else if ( $_SESSION["id"] == "02")
{
	?>
	<a href="/recherche/">Recherche, export</a> |
	<a href="/en_cours.php">Inscriptions en cours</a> |
	<a href="/statistiques/">Statistiques</a> |
	<a href="/inscrits/index.php">Gestion des inscrits</a> 
	| <a href="/imputations/statistiques.php">Imputations</a>
	| <a href="/examens/">Examens</a>
	| <a href="/agendaCNF/" title="Agenda des CNF">Agenda</a>
	| <a href="/messagerie_cnf/index.php">Messagerie <span>(CNF)</span></a>
	<?
	/*
	*/
}		   
// SCAC
else if ( $_SESSION["id"] == "03")
{
	?>
	<a href="/recherche/">Recherche, export</a> |
	<a href="/statistiques/">Statistiques</a> |
	<a href="/inscrits/index.php">Consultation des inscrits</a> 
	<?
	/*
	*/
}
else
{ 
	?>
	<a href="/recherche/">Recherche, export</a> |
	<a href="/statistiques/">Statistiques</a>
	| <a href="/inscrits/index.php">Gestion des inscrits</a> 
	| <a href="/messagerie/index.php">Messagerie</a>
	| <a href="/imputations/statistiques.php">Imputations</a>
	| <a href="/examens/">Examens</a>
	| <a href="/agendaCNF/" title="Agenda des CNF">Agenda</a>
	<?
	/*
	*/
}
?>
</div>
