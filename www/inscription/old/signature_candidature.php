<?php
section_candidature("signature") ;
affiche_si_non_vide(isset($erreur_signature) ? $erreur_signature : "") ;
?>
<tr><td colspan='2'>
<p><strong>Je soussigné(e)
<?php inputtxt("signature", ( isset($T["signature"]) ? $T["signature"] : "" ), 30, 200) ; ?>
(nom et prénom du candidat) certifie sur l'honneur l'exactitude des renseignements ci-dessus.</strong></p>

<p><strong>Fait à <?php inputtxt("ville_res", ( isset($T["ville_res"]) ? $T["ville_res"] : "" ), 15, 200) ; ?> (ville de résidence),
le <?php inputtxt("date_sign", ( isset($T["date_sign"]) ? $T["date_sign"] : "" ), 15, 200); ?> (date de candidature).</strong></p>
</td>
</tr>
</table>

