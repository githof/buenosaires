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
function connect($pseudo, $pass){
	$pseudo = htmlspecialchars(mysql_real_escape_string(trim($pseudo)));
	$pass = htmlspecialchars(mysql_real_escape_string(trim($pass)));
	return connect2($pseudo, cryptage($pass));	
}
function connect2($pseudo, $pass){
	$s = mysql_query("SELECT * FROM utilisateurs WHERE pseudo='$pseudo' and pwd='$pass'");
	if (mysql_num_rows($s) == 1) {
		$sql = mysql_fetch_assoc($s);
		creer_cookie($sql['pseudo'],$sql['pwd']);
		return new Compte($sql['id'],$sql['rang'],$sql['pseudo'],$sql['nom'],$sql['prenom'],$sql['pwd'],$sql['amail'],$sql['date_inscr'],$sql['valid']);
	}
	return NULL;
}
function creer_cookie($pseudo, $pass){
	supprimer_cookie();
	setcookie('utilisateur_pseudo', $pseudo, time() + 365*24*3600, "/", null, false, true);
	setcookie('utilisateur_pass', $pass, time() + 365*24*3600, "/", null, false, true); 
	setcookie('utilisateur_log', secure_cookie($pseudo, $pass), time() + 365*24*3600, "/", null, false, true); 	
}
function identification_cookie(){
	if (isset($_COOKIE['utilisateur_log']) and isset($_COOKIE['utilisateur_pseudo']) and isset($_COOKIE['utilisateur_pass'])){
		if ($_COOKIE['utilisateur_log'] == secure_cookie($_COOKIE['utilisateur_pseudo'],$_COOKIE['utilisateur_pass'])){
			return connect2($_COOKIE['utilisateur_pseudo'],$_COOKIE['utilisateur_pass']);
		}
	}
	return NULL;
}
function supprimer_cookie(){
	setcookie('utilisateur_log', "", 0, "/", null, false, true); unset($_COOKIE['utilisateur_log']);
	setcookie('utilisateur_pseudo', "", 0, "/", null, false, true); unset($_COOKIE['utilisateur_pseudo']);
	setcookie('utilisateur_pass', "", 0, "/", null, false, true); unset($_COOKIE['utilisateur_pass']);	
}
function secure_cookie($pseudo, $pass){
	return md5($pseudo."ksdhfiuhggzrkfzkl".$pass); 		// la clé peut-être modifiée quand on veut, comme on veut.
}
/* 
	Ajoute le compte si tous les paramètres sont ok, sinon return NULL
	Le pseudo doit avoir au moins 4 caractères 
	Le mot de passe doit avoir au moins 6 caractères
*/
function ajouter_compte($rang, $pseudo, $nom, $prenom, $pass1, $pass2, $amail){
	if (($pass1 != $pass2 or trim($pseudo) == "" or trim($pseudo) == NULL or count($pseudo) < 4 or count($pass1)) or !verif_mail($amail)) return NULL;	
	$pass = cryptage($pass);
	return ajouter_compte_sql(trim($rang), trim($pseudo), trim($nom), trim($prenom), $pass, $amail);
}
function ajouter_compte_sql($rang, $pseudo, $nom, $prenom, $pass, $amail){
	$rang = htmlspecialchars(mysql_real_escape_string($rang));
	$pseudo = htmlspecialchars(mysql_real_escape_string($pseudo));
	$nom = htmlspecialchars(mysql_real_escape_string($nom));
	$prenom = htmlspecialchars(mysql_real_escape_string($prenom));
	$pass = htmlspecialchars(mysql_real_escape_string($pass));	
	if (!exist_account($pseudo, $amail)){
		$now = time();
		$secure = secure($pseudo);
		mysql_query("INSERT INTO utilisateurs (id, rang, pseudo, nom, prenom, pwd, amail, date_inscr, valid) VALUES 
		(NULL,'$rang', '$pseudo','$nom','$prenom','$pass','$amail','$now','$secure')");
		$compte = connect2($pseudo, $pass);
		envoyer_mail_validation($compte);
		return $compte;
	}
	else return NULL;
}
function exist_account($pseudo, $amail){
	if(mysql_num_rows(mysql_query("SELECT * FROM utilisateurs WHERE pseudo='$pseudo'")) > 0) return false;
	if(mysql_num_rows(mysql_query("SELECT * FROM utilisateurs WHERE amail='$amail'")) > 0) return false;
	return true;	
}
function verif_mail($amail){
	return true;
}
function cryptage($pass){
	return md5($pass);	
}
function secure($cle){
	return md5($cle.mt_rand().time());
}
/*
	Mettre à jour cette fonction pour la faire correspondre au site web.
*/
function envoyer_mail_validation($account){
	$subject = 'Projet Buenos Aires : Mail d\'activation';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: "Echo Geek"<noreply@lyart.fr>'."\r\n";
	$headers .= 'Reply-to: axelle.piot@lyart.fr'."\r\n";
	$headers .= 'Content-Transfer-Encoding: 8bit'."\r\n";
				
	$msg = '
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf_8">
			<title>Projet Buenos Aires : Mail d\'activation</title>	
		</head>
		<body>
			Bonjour,<br />
			<br />
			Merci de vous etes inscrit-e sur le site pour le projet Buenos Aires.<br />
			Pour activer votre compte, cliquez sur le lien suivant : <br />
			<br />
			<a href="http://www.lyart.fr/buenosaires/accueil/activation.php?a='.$account->valid.'">http://www.lyart.fr/buenosaires/accueil/activation.php?a='.$account->valid.'</a><br />
			<br />
			A bientot sur le site.<br />
			Cordialement.<br />
		</body>
		</html>
	';
				
	mail($account->amail, $subject, $msg, $headers);
}
?>