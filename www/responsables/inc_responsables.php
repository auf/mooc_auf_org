<?php
function responsableForm($cnx, $R=array())
{
}

// Infos relatives au responsable, pour affichage
// Avec option pour la page de suppression
function responsableData($cnx, $responsable, $delete=FALSE)
{
	$str  = "" ;

	$req = "SELECT selecteurs.*,
		(SELECT COUNT(id_atelier) FROM atxsel WHERE id_sel=codesel) AS N,
		(SELECT COUNT(id_comment_sel) FROM comment_sel WHERE ref_selecteur=codesel) AS C,
		ref_etablissement.nom AS institution
		FROM selecteurs
		LEFT JOIN ref_etablissement ON ref_etablissement.id=ref_institution
		WHERE codesel='".$responsable."'" ;
	$res = mysqli_query($cnx, $req) ;

	if ( mysqli_num_rows($res) == 0 ) {
		return $str ;
	}

	$R = mysqli_fetch_assoc($res) ;

	$str .= "<table class='data'>\n" ;
	$str .= "<tr><th>Institution : </th><td>"
		. ( isset($R["institution"]) ? $R["institution"] : "" )
		. "</td></tr>\n" ;
	$str .= "<tr><th>Nom : </th><td>"
		. ( isset($R["nomsel"]) ? $R["nomsel"] : "" )
		. "</td></tr>\n" ;
	$str .= "<tr><th>Prénom : </th><td>"
		. ( isset($R["prenomsel"]) ? $R["prenomsel"] : "" )
		. "</td></tr>\n" ;
	$str .= "<tr><th>Email : </th><td>"
		. ( isset($R["email"]) ? $R["email"] : "" )
		. "</td></tr>\n" ;
	$str .= "<tr><th>Identifiant : </th><td class='mono'>"
		. ( isset($R["usersel"]) ? $R["usersel"] : "" )
		. "</td></tr>\n" ;
	$str .= "<tr><th>Mot de passe : </th><td class='mono'>"
		. ( isset($R["pwdsel"]) ? $R["pwdsel"] : "" )
		. "</td></tr>\n" ;
	$str .= "<tr><th>Transfert : </th><td>"
		. ( isset($R["transfert"]) ? $R["transfert"] : "" )
		. "</td></tr>\n" ;
	$str .= "<tr><th>Commentaire : </th><td>"
		. ( isset($R["commentaire"]) ? $R["commentaire"] : "" )
		. "</td></tr>\n" ;

	$req = "SELECT atelier.intitule
		FROM atelier, atxsel
		WHERE atelier.id_atelier=atxsel.id_atelier
		AND atxsel.id_sel='".$responsable."'
		ORDER BY groupe, niveau, intitule" ;
	$res = mysqli_query($cnx, $req);
	$nb = mysqli_num_rows($res) ;

	$str .= "<tr>\n<th>Formation"
		. ( $nb > 1 ? "s" : "" )
		. " (".$nb.")&nbsp;:</th>\n<td>" ;
	while ( $val = mysqli_fetch_assoc($res))
	{
	   $str .= $val["intitule"] . "<br />" ;
	}
	$str .= "</td></tr>\n" ;
	$str .= "</table>\n" ;

	// Option pour page de suppression
	if ( $delete )
	{
   		if ( (intval($R["N"]) != 0) OR (intval($R["C"]) != 0) )
		{
			$str .= "<p class='c'><strong>Ce responsable ne peut pas être supprimé :</strong></p>" ;

			$str .= "<table class='data'>\n" ;
			$str .= "<tr><th>Nombre de formations : </th><td>"
				. ( isset($R["N"]) ? $R["N"] : "" )
				. "</td></tr>\n" ;
			$str .= "<tr><th>Nombre d'avis sur les dossiers de candidature : </th><td>"
				. ( isset($R["C"]) ? $R["C"] : "" )
				. "</td></tr>\n" ;
			$str .= "</table>\n" ;
    	}
		else
		{
			$str .= "<p class='c'><strong><a href='delete.php?select=".$responsable."&confirmation=oui'>Confirmer la suppression</a></strong></p>" ;
		}
	}

	return $str ;
}

?>
