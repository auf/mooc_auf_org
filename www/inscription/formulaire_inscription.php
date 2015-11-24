<?php
if ( $tabInscription["idmooc"] == "1" )
{
	section_candidature("1") ;
	table_candidature("1") ;
	affiche_si_non_vide( isset($erreur_saisie1) ? $erreur_saisie1 : "" ) ;
	// Le champ pwd_mooc est un pot de miel pour les robots
	if ( trim($tabInscription["consignes_idmooc"]) != "" )
	{
		echo "<tr><td colspan='2'>" ;
		echo nl2br($tabInscription["consignes_idmooc"]) ;
		echo "</td></tr>\n" ;
	}
	?>
	<tr id="idMooc">
		<th style='width: 12em;'><?php libelle("id_mooc") ; ?></th>
		<td><?php inputtxt("id_mooc", ( isset($T["id_mooc"]) ? $T["id_mooc"] : "" ), 40, 100); ?></td>
	</tr>
	</table>
	<?php
}


section_candidature("2") ;
table_candidature("2") ;
affiche_si_non_vide( isset($erreur_saisie2) ? $erreur_saisie2 : "" ) ;
// Pot de miel
?>
<tr id="pwdMooc">
	<th><?php libelle("pwd_mooc") ; ?></th>
	<td><?php
	//inputtxt("pwd_mooc", ( isset($T["pwd_mooc"]) ? $T["pwd_mooc"] : "" ), 40, 100, "password");
	inputtxt("pwd_mooc", ( isset($T["pwd_mooc"]) ? $T["pwd_mooc"] : "" ), 40, 100);
	?></td>
</tr>
<tr>

	<th style='width: 12em;'><?php libelle("email") ; ?></th>
	<td>
	<div>Veuillez utiliser la même adresse que celle utilisée pour votre inscription au MOOC.<br />
Cette adresse doit être durablement fiable.
	<span style='font-size: 0.85em;'>(C'est notamment à cette adresse que votre certification sera envoyée.)</span></div>
	<?php inputtxt("email", ( isset($T["email"]) ? $T["email"] : "" ), 50, 100); ?>
	<div>Confirmez (répétez) cette adresse&nbsp;:</div>
	<?php inputtxt("verif_email", ( isset($T["verif_email"]) ? $T["verif_email"] : "" ), 50, 100); ?>
</td>
</tr><tr>
	<th><?php libelle("genre") ; ?></th>
	<td><?php  liste_der1($GENRE, "genre", ( isset($T["genre"]) ? $T["genre"] : "" )); ?></td>
</tr><tr>
	<th><?php libelle("nom") ; ?></th>
	<td><?php inputtxt("nom", ( isset($T["nom"]) ? $T["nom"] : "" ), 40, 100) ; ?></td>
</tr><tr>
	<th><?php libelle("prenom") ; ?></th>
	<td><?php inputtxt("prenom", ( isset($T["prenom"]) ? $T["prenom"] : "" ), 40, 100) ; ?></td>
</tr><tr>
	<th><?php libelle("naissance") ; ?></th>
	<td><?php liste_der1($JOUR, "jour_n", ( isset($T["jour_n"]) ? $T["jour_n"] : "" )) ;
		echo " / " ;
		liste_der2($MOIS, "mois_n", ( isset($T["mois_n"]) ? $T["mois_n"] : "" )) ;
		echo " / " ;
		liste_der1($ANNEE_NAISSANCE, "annee_n", ( isset($T["annee_n"]) ? $T["annee_n"] : "" )) ;
		?></td>
</tr><tr>
	<th><?php libelle("lieu_naissance") ; ?></th>
	<td><?php echo inputtxt("lieu_naissance", ( isset($T["lieu_naissance"]) ? $T["lieu_naissance"] : "" ), 40, 100) ; ?></td>
</tr><tr>
	<th><?php libelle("pays_naissance") ; ?></th>
	<td><?php echo selectPays($cnx, "pays_naissance", ( isset($T["pays_naissance"]) ? $T["pays_naissance"] : "" )) ; ?></td>
</tr><tr>
	<th><?php libelle("pays_nationalite") ; ?></th>
	<td><?php echo selectPays($cnx, "pays_nationalite", ( isset($T["pays_nationalite"]) ? $T["pays_nationalite"] : "" )) ; ?></td>
</tr><tr>
	<th><?php libelle("pays_residence") ; ?></th>
	<td><?php echo selectPays($cnx, "pays_residence", ( isset($T["pays_residence"]) ? $T["pays_residence"] : "" )); ?></td>
</tr><tr>
	<th><?php libelle("situation_actu") ; ?></th>
	<td><?php
		liste_der2($SITUATION, "situation_actu", ( isset($T["situation_actu"]) ? $T["situation_actu"] : "" ));
		?><div><?php libelle("sit_autre") ; ?><br /><?php
		inputtxt("sit_autre", ( isset($T["sit_autre"]) ? $T["sit_autre"] : "" ), 69, 255); ?></div></td>
</tr>
</table>



<?php 
if ( $tabInscription["identite"] == "1" )
{
	section_candidature("3") ;
	table_candidature("3") ;
	affiche_si_non_vide( isset($erreur_saisie3) ? $erreur_saisie3 : "" ) ;
	if ( trim($tabInscription["consignes_identite"]) != "" )
	{
		echo "<tr><td colspan='2'>" ;
		echo nl2br($tabInscription["consignes_identite"]) ;
		echo "</td></tr>\n" ;
	}
	?>
	<tr>
		<th style='width: 12em;'><?php libelle("ident_nature") ; ?></th>
		<td><?php
		liste_der2($IDENTITE, "ident_nature", ( isset($T["ident_nature"]) ? $T["ident_nature"] : "" ));
		?><div><?php libelle("ident_autre") ; ?><br /><?php
		inputtxt("ident_autre",
			( isset($T["ident_autre"]) ? $T["ident_autre"] : "" ), 69, 255) ;
			?></div></td>
	</tr><tr>
		<th><?php libelle("ident_numero") ; ?></th>
		<td><?php inputtxt("ident_numero",
			( isset($T["ident_numero"]) ? $T["ident_numero"] : "" ), 40, 100) ;
			?></td>
	</tr><tr>
		<th><?php libelle("ident_date") ; ?></th>
		<td><?php liste_der1($JOUR, "jour_ident",
				( isset($T["jour_ident"]) ? $T["jour_ident"] : "" )) ;
			echo " / " ;
			liste_der2($MOIS, "mois_ident",
				( isset($T["mois_ident"]) ? $T["mois_ident"] : "" )) ;
			echo " / " ;
			liste_der1($ANNEE_DELIVRANCE, "annee_ident",
				( isset($T["annee_ident"]) ? $T["annee_ident"] : "" )) ;
		?></td>
	</tr><tr>
		<th><?php libelle("ident_lieu") ; ?></th>
		<td><?php inputtxt("ident_lieu",
			( isset($T["ident_lieu"]) ? $T["ident_lieu"] : "" ), 40, 100) ;
		?></td>
	</tr>
	</table><?php
}

section_candidature("4") ;
table_candidature("4") ;
affiche_si_non_vide( isset($erreur_saisie4) ? $erreur_saisie4 : "" ) ;
?>
<tr><td colspan='2'>
<p>Je soussigné(e)
<?php inputtxt("signature", ( isset($T["signature"]) ? $T["signature"] : "" ), 50, 200) ; ?>
(nom de famille et prénom(s))&nbsp;:</p>
<ul>
<li><label class='case'><input type='checkbox' name='certifie' value='1' <?php
if ( isset($T["certifie"]) AND (intval($T["certifie"]) == 1) ) {
	echo " checked='checked'" ;
}
?>/>
certifie sur l'honneur l'exactitude des informations ci-dessus</label>,</li>
<li>
<div style='font-size: 0.85em;'>Les frais versés par un candidat pour avoir le droit de passer un examen de certification organisé par l’Agence universitaire de la Francophonie ne sont pas remboursables.
Ils sont définitivement acquis, même si le candidat ne peut se présenter à l'examen pour quelque raison que ce soit.</div>
<label class='case'><input type='checkbox' name='accepte' value='1' <?php
if ( isset($T["accepte"]) AND (intval($T["accepte"]) == 1) ) {
	echo " checked='checked'" ;
}
?>/>
accepte ces conditions.</label></li>
</ul>
</td>
</tr>
</table>
<?php
/*
accepte les <a class='extern' target='_blank' href='/conditions.php'>conditions générales</a></label>.</li>
*/
?>
