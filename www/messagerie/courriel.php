<?php
include("inc_session.php") ;

if ( empty($_SESSION["messagerie"]["promotion"]) ) {
	header("Location: index.php") ;
	exit ;
}
$promotion = $_SESSION["messagerie"]["promotion"] ;

// Redirection vers l'ajout d'un attachement
if ( isset($_SESSION["messagerie"]["action"]) AND ($_SESSION["messagerie"]["action"] == "attachement") ) {
	header("Location: attachement.php") ;
	exit ;
}

include("inc_mysqli.php") ;
$cnx = connecter() ;

include("inc_promotions.php") ;
$promo = idpromotion2nom($promotion, $cnx) ;
$titrePromo = $promo["intitule"]." (".$promo["intit_ses"].")" ;
$titre = "Nouveau courriel" ;
$etat = ( isset($_POST["etat"]) ? $_POST["etat"] : "" ) ;
if ( !empty($etat) ) {
	$titre .= " ($etat)" ;
}

include("inc_html.php") ;
echo $dtd1 ;
echo "<title>$titre $titrePromo</title>\n" ;
?>
<script language="javascript">
function checkAll() {
    lg=document.forms[0].elements.length;
    for ( i=0;i<lg;i++) {
        if (document.forms[0].elements[i].type=="checkbox") {
            document.forms[0].elements[i].checked=true;
        }
    }   
}      
function unCheck() {
    lg=document.forms[0].elements.length;
    for ( i=0;i<lg;i++) {
        if (document.forms[0].elements[i].type=="checkbox") {
            document.forms[0].elements[i].checked=false; 
        }
    }
}
function copie() {
	document.forms[0].cc.value="<?php echo $_SESSION["courriel"] ; ?>" ;
}
</script>
<?php
echo $dtd2 ;
include("inc_menu.php") ;
echo $debut_chemin ;
echo "<a href='/bienvenue.php'>Accueil</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie/index.php'>Messagerie <span>(inscrits)</span></a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo "<a href='/messagerie/promotion.php?promotion=$promotion'>" ;
echo "$titrePromo</a>" ;
echo " <span class='arr'>&rarr;</span> " ;
echo $titre ;
echo $fin_chemin ;

//print_r($_SESSION["messagerie"]) ;

include("inc_messagerie.php") ;

// Formulaire soumis
if ( isset($_SESSION["messagerie"]["action"]) AND ($_SESSION["messagerie"]["action"] == "envoi") ) {
	$erreurs = verification_courriel($_SESSION["messagerie"]) ;
	// Erreurs
	if ( count($erreurs) != 0 ) {
		echo "<ul class='erreur'>\n" ;
		foreach($erreurs as $erreur) {
			echo "<li>$erreur</li>\n" ;
		}
		echo "</ul>\n" ;
	}
	formulaire_courriel($_SESSION["messagerie"], $_SESSION["courriel"], $cnx) ;
}
// Formulaire non soumis
else {
	formulaire_courriel($_SESSION["messagerie"], $_SESSION["courriel"], $cnx) ;
}

deconnecter($cnx) ;
//diagnostic() ;
echo $end ;
?>
