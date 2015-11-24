<?php
$date_examen = $_POST["annee_examen"] . "-"
	. $_POST["mois_examen"] . "-"
	. $_POST["jour_examen"] ;
$req = "UPDATE examens SET
	ref_session=".$_POST["promotion_examen"].",
	date_examen='$date_examen',
	commentaire='".mysqli_real_escape_string($cnx, $_POST["commentaire"])."'
	WHERE id_examen=".$_POST["id_examen"] ;

mysqli_query($cnx, $req) ;


