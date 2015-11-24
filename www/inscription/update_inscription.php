<?php

while ( list($key, $val) = each($T) ) {
	$Ts[$key] = mysqli_real_escape_string($cnx, remets_guillemets($val)) ;
}
reset($T) ;

$date_n     = $Ts["annee_n"] ."-". $Ts["mois_n"] ."-".  $Ts["jour_n"] ;
$date_ident = $Ts["annee_ident"] ."-". $Ts["mois_ident"] ."-".  $Ts["jour_ident"] ;

$req = "UPDATE dossier SET
	id_mooc='".$Ts["id_mooc"]."',
	email='".$Ts["email"]."',
	genre='".$Ts["genre"]."',
	nom='".$Ts["nom"]."',
	prenom='".$Ts["prenom"]."',
	naissance='".$date_n."',
	lieu_naissance='".$Ts["lieu_naissance"]."',
	pays_naissance='".$Ts["pays_naissance"]."',
	pays_nationalite='".$Ts["pays_nationalite"]."',
	pays_residence='".$Ts["pays_residence"]."',
	situation_actu='".$Ts["situation_actu"]."',
	sit_autre='".$Ts["sit_autre"]."',
	ident_nature='".$Ts["ident_nature"]."',
	ident_autre='".$Ts["ident_autre"]."',
	ident_numero='".$Ts["ident_numero"]."',
	ident_date='".$date_ident."',
	ident_lieu='".$Ts["ident_lieu"]."',
	signature='".$Ts["signature"]."',
	certifie='".$Ts["certifie"]."',
	accepte='".$Ts["accepte"]."',
	date_maj=CURRENT_DATE

	WHERE id_dossier=".$Ts["id_dossier"] ;
$res = mysqli_query($cnx, $req) ;

if ( $res) {
	echo "<div class='msgok'><p>Votre dossier d'inscription a été modifié.</p></div>\n" ;
}
else {
	echo "<div class='erreur'><p>Échec de la mise à jour de votre dossier d'inscription.</p></div>\n" ;
}
?>
