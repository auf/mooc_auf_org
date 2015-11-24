<?php
require_once("inc_config.php") ;
include("inc_html.php") ;
echo $dtd1 ;
$titreConditions = "Conditions générales" ;
echo "<title>".$titreConditions."</title>\n" ;
echo $dtd2Public ;
echo enteteAufMooc($titreConditions) ;
?>


<p class='j'>Les frais versés par un candidat pour avoir le droit de passer un examen de certification organisé par l’Agence universitaire de la Francophonie ne sont pas remboursables.<br />
Ils sont définitivement acquis, même si le candidat ne peut se présenter à l'examen pour quelque raison que ce soit.</p>


<?php
echo $endPublic ;
?>


