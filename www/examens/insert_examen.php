<?php
$date_examen = $_POST["annee_examen"] . "-"
        . $_POST["mois_examen"] . "-"
        . $_POST["jour_examen"] ;
$req = "INSERT INTO examens(ref_session, date_examen, commentaire)
        VALUES(".$_POST["promotion_examen"].",
        '$date_examen',
        '".$_POST["commentaire"]."')" ;
mysqli_query($cnx, $req) ;

$req = "SELECT LAST_INSERT_ID() AS N" ;
$res = mysqli_query($cnx, $req) ;
$enr = mysqli_fetch_assoc($res) ;
$id_examen = $enr["N"] ;
?>
