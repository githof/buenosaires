<?php

	// retourne les infos d'une personne sous forme de tableau
	function personne($id){
		return mysql_fetch_assoc(mysql_query("SELECT * FROM personnes WHERE id='$id'"));
	}
		


?>