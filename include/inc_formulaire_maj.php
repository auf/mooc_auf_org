<form action="maj.php" method="post">
<table class='formulaire'>
<tr>
	<th>Numéro de dossier :</th>
	<td><input type='text' name='id_dossier' value='<?php echo $_POST["id_dossier"]; ?>'/></td>
</tr><tr>
	<th>Mot de passe :</th>
	<td><input type='password' name='pass' /></td>
</tr>
</table>
<p class='c'><input type="submit" value="Suivant"></p>
</form>
