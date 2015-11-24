<?php

$implantationBureau = array(
	""					=> "",
	"Abidjan"			=> "BAO",
	"Alep"				=> "BMO",
	"Alexandrie"		=> "BMO",
	"Alger"				=> "BM",
	"Antananarivo"		=> "BOI",
	"Bamako"			=> "BAO",
	"Bangui"			=> "BACGL",
	"Beyrouth"			=> "BMO",
	"Bobo Dioulasso"	=> "BAO",
	"Brazzaville"		=> "BACGL",
	"Bucarest"			=> "BECO",
	"Bujumbura"			=> "BACGL",
	"Chisinau"			=> "BECO",
	"Conakry"			=> "BAO",
	"Constantine"		=> "BM",
	"Cotonou"			=> "BAO",
	"Dakar"				=> "BAO",
	"Damas"				=> "BMO",
	"Danang"			=> "BAP",
	"Erévan"			=> "BECO",
	"Hanoi"				=> "BAP",
	"Ho Chi Minh"		=> "BAP",
	"Kinshasa"			=> "BACGL",
	"Libreville"		=> "BACGL",
	"Lomé"				=> "BAO",
	"Lubumbashi"		=> "BACGL",
	"Montréal"			=> "BA",
	"Moroni"			=> "BOI",
	"Ndjaména"			=> "BACGL",
	"Ngaoundéré"		=> "BACGL",
	"Niamey"			=> "BAO",
	"Nouakchott"		=> "BAO",
	"Oran"				=> "BM",
	"Ouagadougou"		=> "BAO",
	"Paris"				=> "BEO",
	"Phnom Penh"		=> "BAP",
	"Port-au-Prince"	=> "BC",
	"Port-Vila"			=> "BAP",
	"Rabat"				=> "BM",
	"Réduit"			=> "BOI",
	"Saint-Louis"		=> "BAO",
	"Sofia"				=> "BECO",
	"Tbilissi"			=> "BECO",
	"Tirana"			=> "BECO",
	"Tripoli"			=> "BMO",
	"Tunis"				=> "BM",
	"Vientiane"			=> "BAP",
	"Yaoundé"			=> "BACGL",
	"Autre"				=> "Autre"
) ;


$CNF = array(
	"Abidjan",
	"Alep",
	"Alexandrie",
	"Alger",
	"Antananarivo",
	"Bamako",
	"Bangui",
	"Beyrouth",
	"Bobo Dioulasso",
	"Brazzaville",
	"Bucarest",
	"Bujumbura",
	"Chisinau",
	"Conakry",
	"Constantine",
	"Cotonou",
	"Dakar",
	"Damas",
	"Danang",
	"Erévan",
	"Hanoi",
	"Ho Chi Minh",
	"Kinshasa",
	"Libreville",
	"Lomé",
	"Lubumbashi",
	"Montréal",
	"Moroni",
	"Ndjaména",
	"Ngaoundéré",
	"Niamey",
	"Nouakchott",
	"Oran",
	"Ouagadougou",
	"Paris",
	"Phnom Penh",
	"Port-au-Prince",
	"Port-Vila",
	"Rabat",
	"Réduit",
	"Saint-Louis",
	"Sofia",
	"Tbilissi",
	"Tirana",
	"Tripoli",
	"Tunis",
	"Vientiane",
	"Yaoundé",
	"Autre"
) ;

function listeCnf($name, $selected, $vide=FALSE)
{
	global $CNF ;
	$form = "<select name='$name'>\n" ;
	if ( $vide ) {
		$form .= "<option value=\"\"></option>\n" ;
	}
	foreach($CNF as $cnf) {
		$form .= "<option value=\"$cnf\"" ;
		if ( $selected == $cnf ) {
			$form .= " selected='selected'" ;
		}
		$form .= ">$cnf</option>\n" ;
	}
	$form .= "</select>" ;
	return $form;
}



?>
