<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Document sans titre</title>
</head>

<body>

<?php
class Compte{

	var $id; 			// int
	var $rang; 			// int
	var $pseudo; 		// string
	var $nom; 			// string
	var $prenom; 		// string
	var $pass; 			// string
	var $amail; 		// string
	var $date_inscr; 	// string
	var $valid; 		// string
		
	function Compte($id, $rang, $pseudo, $nom, $prenom, $pass, $amail, $date_inscr, $valid){
		$this->id = $id;
		$this->rang = $rang;
		$this->pseudo = $pseudo;
		$this->nom = $nom;
		$this->prenom = $prenom;
		$this->pass = $pass;
		$this->amail = $amail;
		$this->date_inscr = $date_inscr;
		$this->valid = $valid;
	}
	
}

$compte = new Compte(1,2,3,4,5,6,7,8,9);
echo $compte->id;

$tab = array();
$bob = $tab["bob"];
if(! isSet($bob)) echo "\n not set\n";
?>

</body>
</html>