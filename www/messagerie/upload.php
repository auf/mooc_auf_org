<?php
include("inc_session.php") ;

/*
	echo "<pre>" ;
	print_r($_FILES) ;
	print_r($_SESSION) ;
	print_r($_POST) ;
	echo "</pre>" ;

*/

//
// Taille du fichier > post_max_size (php.ini)
//
if ( !isset($_FILES["fichier"]) ) {
	$_SESSION["messagerie"]["uploadErreur"]
		= "La taille de votre fichier est supérieure à 8Mo&nbsp;!"
		. "<br />Elle est limitée à 2Mo." ;
	header("Location: /messagerie/attachement.php") ;
	exit ;
}
//
// Pas d'erreur
//
if ( $_FILES["fichier"]["error"] == 0 )
{
	// Créer le répertoire s'il n'existe pas
	$chemin = $_SERVER["DOCUMENT_ROOT"] . "attachements/"
			. $_SESSION["messagerie"]["promotion"] ;
	if ( !is_dir($chemin) ) {
		mkdir($chemin) ;
	}

	// Nom du fichier
	include_once("inc_traitements_caracteres.php") ;
	$nom = $_FILES["fichier"]["name"] ;
	$nom = traitementNomFichier($nom) ;

	while ( is_file($chemin."/".$nom) ) {
		$nom = "_" . $nom ;
	}

	// Copie du fichier
	error_reporting(E_ALL) ;
	if ( move_uploaded_file($_FILES["fichier"]["tmp_name"], $chemin."/".$nom) )
	{
		include("inc_mysqli.php") ;
		$cnx = connecter() ;
		$req = "INSERT INTO attachements(ref_session, ref_courriel, nom, taille)
				VALUES(".$_SESSION["messagerie"]["promotion"].", 0, '$nom', "
				.$_FILES["fichier"]["size"].")" ;
		$res = mysqli_query($cnx, $req) ;
		$req = "SELECT LAST_INSERT_ID() AS N" ;
		$res = mysqli_query($cnx, $req) ;
		$enr = mysqli_fetch_assoc($res) ;
		$id = $enr["N"] ;
		deconnecter($cnx) ;
		unset($_FILES["fichier"]) ;
		$_SESSION["messagerie"]["action"] = "" ;

		if ( !isset($_SESSION["messagerie"]["nbAttachements"]) 
			OR ( $_SESSION["messagerie"]["nbAttachements"] == 0 ) )
		{
			$_SESSION["messagerie"]["nbAttachements"] = 1 ;
			$_SESSION["messagerie"]["attachements"] = array($id) ;
		}
		else {
			$_SESSION["messagerie"]["nbAttachements"] += 1 ;
			$attachements = $_SESSION["messagerie"]["attachements"] ;
			$attachements[] = $id ;
			$_SESSION["messagerie"]["attachements"] = $attachements ;
		}
	}
	else {
		echo "Votre fichier n'a pas pu être déplacé." ;
	}

	header("Location: /messagerie/courriel.php#bas") ;
}
//
//Erreur
//
else
{
	if ( $_FILES["fichier"]["error"] <= 2 ) {
		$_SESSION["messagerie"]["uploadErreur"]
			= "Fichier trop volumineux (limite : 2Mo)" ;
	}
	else if ( $_FILES["fichier"]["error"] == 4 ) {
		$_SESSION["messagerie"]["uploadErreur"]
			= "Vous devez joindre un fichier" ;
	}
	else {
		$_SESSION["messagerie"]["uploadErreur"]
			= "Erreur ".$_FILES["fichier"]["error"]." dans le chargement du fichier" ;
	}
	header("Location: /messagerie/attachement.php") ;
}
?>
