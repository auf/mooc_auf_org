<?php
include("inc_mysqli.php") ;
$cnx = connecter() ;


$req = "DELETE FROM dossier WHERE id_candidat=0" ;



$req = "SELECT id_candidat FROM dossier ORDER BY id_candidat" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	$candidats_dossier[] = $enr["id_candidat"] ;
}
echo "<div>".count($candidats_dossier)." candidats dans la table <code>dossier</code></div>\n" ;

$req = "SELECT id_candidat FROM candidat ORDER BY id_candidat" ;
$res = mysqli_query($cnx, $req) ;
while ( $enr = mysqli_fetch_assoc($res) ) {
	$candidats_candidat[] = $enr["id_candidat"] ;
}
echo "<div>".count($candidats_candidat)." candidats dans la table <code>candidat</code></div>\n" ;


$pas_dans_candidat = array_diff($candidats_dossier, $candidats_candidat) ;
echo count($pas_dans_candidat) . "\n" ;
$pas_dans_dossier = array_diff($candidats_candidat, $candidats_dossier) ;
echo count($pas_dans_dossier) . "\n" ;
//print_r($pas_dans_candidat) ;

foreach($pas_dans_candidat as $id_dossier) {
	$liste_pas_dans_candidat .= $id_dossier . ", " ;
}
echo "<br />" ;
echo $liste_pas_dans_candidat ;
$liste_pas_dans_candidat = substr($liste_pas_dans_candidat, 0, -2) ;
echo "<br />" ;
echo $liste_pas_dans_candidat ;
echo "<br />" ;



deconnecter($cnx) ;
?>
