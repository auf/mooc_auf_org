<?php
define("LIEN_REINITIALISER", "<a class='reinitialiser' href='reinitialiser.php'>".LABEL_REINITIALISER."</a>") ;
define("BOUTON_ACTUALISER", "<input class='b' type='submit' value='".LABEL_ACTUALISER."' />") ;
define("FILTRE_BOUTON_LIEN", "<div class='c'>" . LIEN_REINITIALISER . BOUTON_ACTUALISER . "</div>") ;

$dtd1 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
' ;

$htmlJquery = '<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
' ;
$htmlJquery172 = '<script type="text/javascript" src="/js/jquery-1.7.2.min.js"></script>
' ;
$htmlMakeSublist = '<script type="text/javascript" src="/js/makeSublist.js"></script>
' ;
$htmlDatePick = '<script type="text/javascript" src="/js/jquery.datepick.js"></script>
<script type="text/javascript" src="/js/jquery.datepick-fr.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jquery.datepick.css" />
' ;
$htmlPhotobox = $htmlJquery172
. '<script type="text/javascript" src="/js/photobox-1.7.1/photobox/photobox.min.js"></script>
<link rel="stylesheet" type="text/css" href="/js/photobox-1.7.1/photobox/photobox.css" />
<link rel="stylesheet" type="text/css" href="/js/photobox-1.7.1/photobox/photobox.ie.css" />
' ;
/*
$htmlMagnificPopup = '
<script type="text/javascript" src="/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="/js/Magnific-Popup-0.9.9/dist/jquery.magnific-popup.min.js"></script>
<link rel="stylesheet" type="text/css" href="/js/Magnific-Popup-0.9.9/dist/magnific-popup.css" />
' ;
$htmlCombo = '
<script type="text/javascript" src="~/js/jquery.simpleCombo.js"></script>
';
$htmlChecked = '
<script type="text/javascript" src="~/js/checked.js"></script>
';
*/

$dtd2sansBody= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/css/mooc.css" rel="stylesheet" type="text/css" media="screen, print" />
<link href="/css/screen.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/css/impression.css" rel="stylesheet" type="text/css" media="print" />
<link rel="icon" href="/favicon.ico" type="image/x-icon" />
</head>
' ;

$dtd2 = $dtd2sansBody . '
<body>
' ;

$dtd2Public = $dtd2sansBody . '
<body class="public">
<div id="conteneur">
' ;

$debut_chemin = "<div id='chemin'>\n" ;
$fin_chemin = "</div>\n
<div id='page'>" ;

$end = "<a id='bas' accesskey='B'></a></div>
</body>\n</html>" ;
$endPublic = "<a id='bas' accesskey='B'></a></div></div>
<div id='pied'>&nbsp;</div>
</body>\n</html>" ;

$logoAUF = "<p class='c' style='margin-top: 2em;'>
	<img src='/img/AUF.png' border='0' width='130' height='93'
	alt='Agence universitaire de la Francophonie' /></p>\n" ;
$logoAUF = "<p class='c' style='margin-top: 2em;'>
	<img src='/img/AUF.png' border='0' width='100' height='69'
	alt='Agence universitaire de la Francophonie' /></p>\n" ;

$titreFOAD = "<h1 title='Cours en ligne ouvert et massif'><a style='font-weight: normal;' href='http://foad-mooc.auf.org/'>MOOC</a></h1>\n" ;

$logoAufFoad = "<div class='c accueil'>
	<img src='/img/AUF.png' border='0' width='130' height='93'
	alt='Agence universitaire de la Francophonie'
	title='Agence universitaire de la Francophonie' />
<h1 class='foad'><a href='http://www.foad-mooc.auf.org/MOOC'>Cours en ligne ouvert et massif (MOOC)</a></h1>
</div>\n" ;

function enteteAufMooc($titre)
{
	return "<div id='entete'>
<div id='logo'><img src='/img/AUF.png' border='0' width='130' height='93'
	alt='Agence universitaire de la Francophonie'
	title='Agence universitaire de la Francophonie' /></div>
<h1 class='mooc'><a href='http://www.foad-mooc.auf.org/MOOC'
	>Cours en ligne ouvert et massif (<acronym title='Massive open online course'>MOOC</acronym>)</a></h1>
<h1>$titre</h1>
</div>
<div id='page'>\n" ;
/*
	return "<div id='entete'>
<div id='logo'><img src='/img/LogoAUF.png' border='0' width='100' height='69'
	alt='Agence universitaire de la Francophonie'
	title='Agence universitaire de la Francophonie' /></div>
<h1 class='mooc'><a href='http://foad-mooc.auf.org/'
	>Formations en ligne ouvertes à tous (<acronym title='Massive open online course'>MOOC</acronym>)</a></h1>
<h1>$titre</h1>
</div>
<div id='page'>\n" ;
*/
}

?>
