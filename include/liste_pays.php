<?php
function selectPays($cnx, $name, $value="", $req="SELECT code, nom FROM ref_pays WHERE actif=1 ORDER BY nom")
{
	$str = "" ;
	$res = mysqli_query($cnx, $req) ;
	$str .= "<select name='$name'>\n" ;
	$str .= "<option value=''></option>" ;
	while ( $enr = mysqli_fetch_assoc($res) )
	{
	    $str .= "<option value='".$enr["code"]."'" ;
	    if ( $enr["code"] == $value ) {
	        $str .= " selected='selected'" ;
	    }
		$str .= ">".$enr["nom"]."</option>\n" ;
	}
	$str .= "</select>\n" ;
	return $str ;
}

$PAYS_MIGRATION = array(
	"Afghanistan", "Afrique du Sud", "Albanie", "Algérie", "Allemagne", "Andorre", "Angola",
//	"Anguilla",
	"Antigua-et-Barbuda", // "Antigua-Et-Barbuda"
//	"Antilles néerlandaises",
	"Arabie saoudite", // "Arabie Saoudite"
	"Argentine", "Arménie", "Australie", "Autriche", "Azerbaïdjan", "Bahamas", "Bahreïn", "Bangladesh", "Barbade",
	"Biélorussie", // "Bélarus"
	"Belgique", "Belize", "Bénin",
//	"Bermudes",
	"Bhoutan", "Bolivie", "Bosnie-Herzégovine", "Botswana", "Brésil",
	"Brunei", // "Brunei Darussalam"
	"Bulgarie", "Burkina Faso", "Burundi", "Cambodge", "Cameroun", "Canada", "Cap-Vert", "Chili", "Chine", "Chypre", "Colombie", "Comores", "Congo",
	"Costa Rica", "Côte d'Ivoire", "Croatie", "Cuba",
	"Danemark", "Djibouti",
	"Dominique", // FIXME NOUVEAU
	"Egypte",
	"Salvador",
	"Emirats arabes unis", "Equateur",
	"Erythrée", "Espagne", "Estonie", "Etats Unis d'Amérique", "Ethiopie",
	"Russie",
	"Fidji", "Finlande", "France", "Gabon", "Gambie",
	"Géorgie",
	"Ghana",
//	"Groënland",
	"Grèce",
	"Grenade",
//	"Guam",
	"Guatemala", "Guinée",
	"Guinée-Bissao",
	"Guinée équatoriale",
	"Guyana",
	"Haïti", "Honduras", "Hongrie",
	"Îles Marshall", // FIXME NOUVEAU
//	"Îles Cook",
	"Îles Salomon", "Inde", "Indonésie",
	"Iran",
	"Iraq", "Irlande", "Islande", "Israël", "Italie",
	"Jamaïque", // FIXME NOUVEAU
	"Japon", "Jordanie", "Kazakhstan", "Kenya", "Kirghistan",
	"Kiribati", // FIXME NOUVEAU
	"Koweït",
	"Lesotho", "Lettonie", "Liban", "Libéria", "Libye", "Liechtenstein",
	"Lituanie", "Luxembourg",
	"Macédoine", "Madagascar", "Malaisie", "Malawi", "Maldives", "Mali",
	"Malte", // FIXME NOUVEAU
	"Maroc",
	"Maurice", "Mauritanie",
	"Mexique", // FIXME NOUVEAU
//	"Mayotte",
	"Micronésie (états fédérés de)", // FIXME NOUVEAU
	"Monaco", "Mongolie",
	"Monténégro", // FIXME NOUVEAU
	"Mozambique",
	"Myanmar", // FIXME NOUVEAU
	"Namibie",
	"Nauru", // FIXME NOUVEAU
	"Népal",
	"Nicaragua", "Niger", "Nigeria", "Norvège", "Nouvelle-Zélande",
	"Oman", "Ouganda", "Ousbékistan",
	"Pakistan",
	"Palaos", // FIXME NOUVEAU
	"Territoires Palestiniens",
	"Panama", "Papouasie-Nouvelle-Guinée", "Paraguay", "Pays-Bas", "Pérou",
	"Philippines", // FIXME NOUVEAU (est dans les bureaux...)
	"Pologne", "Portugal", "Qatar",
	"Syrie",
	"République Centrafricaine",
	"Corée du Sud",
	"Moldavie",
	"Congo (R.D)",
	"Laos",
	"République Dominicaine",
	"Corée du Nord",
	"République Tchèque",
	"Tanzanie",
	"Roumanie", "Royaume-Uni", "Rwanda",
	"Saint-Kitts-et-Nevis", // FIXME NOUVEAU
	"Saint-Marin",
	"Saint-Siège (État de la Cité du Vatican)", // FIXME NOUVEAU
	"Saint-Vincent-et-les Grenadines", "Sainte-Lucie",
	"Samoa occidentales", "Sao Tomé-et-Principe", "Sénégal",
	"Serbie", // FIXME NOUVEAU
	"Seychelles",
	"Sierra Leone", "Singapour", "Slovaquie", "Slovénie", "Somalie", "Soudan",
	"Soudan du Sud", // FIXME NOUVEAU
	"Sri Lanka", "Suède", "Suisse", "Suriname", "Swaziland",
	"Tadjikistan", // FIXME NOUVEAU
//	"Taiwan",
	"Tchad",
//	"Tchétchénie",
	"Thaïlande",
	"Timor-Leste", // FIXME NOUVEAU
	"Togo", "Tonga", "Trinité-et-Tobago", "Tunisie", "Turkménistan", "Turquie",
	"Tuvalu", // FIXME NOUVEAU
	"Ukraine", "Uruguay", "Vanuatu",
	"Vénézuela",
	"Viêt-Nam",
//	"Yougoslavie",
	"Yémen", "Zambie", "Zimbabwe"
) ;

?>
