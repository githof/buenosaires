<?php

function restriction($compte, $rang){
	if ($compte == NULL or $compte->rang < $rang){
		echo "<br /><br /><br /><h1>Zone restreinte : Vous n'avez pas les droits suffisants pour acc&eacute;der &agrave; cette page.</h1>";	
		include("../includes/footer.php");
		die();
	}
}

?>