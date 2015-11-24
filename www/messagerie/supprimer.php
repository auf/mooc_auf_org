<?php
include("inc_session.php") ;

if ( empty($_GET["id"]) ) {
	header("Location: /messagerie/courriel.php") ;
}
else {
	include("inc_mysqli.php") ;
	$cnx = connecter() ;
	$req = "SELECT ref_session, nom FROM attachements
		WHERE ref_courriel=0 AND id_attachement=".$_GET["id"] ;
	$res = mysqli_query($cnx, $req) ;
	$enr = mysqli_fetch_assoc($res) ;
	if ( strval($enr["ref_session"]) == $_SESSION["messagerie"]["promotion"] )
	{
		$nom = $enr["nom"] ;
		$req = "DELETE FROM attachements WHERE id_attachement=".$_GET["id"] ;
		mysqli_query($cnx, $req) ;
		unlink($_SERVER["DOCUMENT_ROOT"] . "attachements/"
            . $_SESSION["messagerie"]["promotion"] . "/" . $nom) ;

		if ( $_SESSION["messagerie"]["nbAttachements"] > 1 ) {
			$_SESSION["messagerie"]["nbAttachements"] 
				= $_SESSION["messagerie"]["nbAttachements"] - 1 ;
			foreach($_SESSION["messagerie"]["attachements"] as $attach) {
				if ( intval($attach) != intval($_GET["id"]) ) {
					$nouveau[] = $attach ;
				}
			}
			$_SESSION["messagerie"]["attachements"] = $nouveau ;
		}
		else {
			unset($_SESSION["messagerie"]["nbAttachements"]) ;
			unset($_SESSION["messagerie"]["attachements"]) ;
		}
	}
	deconnecter($cnx) ;
	header("Location: /messagerie/courriel.php#bas") ;
}
?>
