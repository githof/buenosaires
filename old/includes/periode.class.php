<?php

class LirePeriode{
	
	// Variables d'instances
	var $minDebut;
	var $maxDebut; 
	var $minFin;
	var $maxFin; 
	
	// Constructeur
	function LirePeriode($acte){
		// format date sql : annee-mois-jour
		if (isset($acte->date)){
			$tabDate = preg_split("/-/", trim($acte->date)); // array [0] -> jour, [1] -> mois, [2] -> annee
			if (!is_string($tabDate) && count($tabDate) == 3){
				$this->minDebut = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
				$this->maxDebut = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
				$this->minFin = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
				$this->maxFin = $tabDate[2]."-".$tabDate[1]."-".$tabDate[0];
			}
			else if (!is_string($tabDate) && count($tabDate) == 1) {
				$this->minDebut = $tabDate[0]."-01-01";
				$this->maxDebut = $tabDate[0]."-12-31";
				$this->minFin = $tabDate[0]."-01-01";
				$this->maxFin = $tabDate[0]."-12-31";
			}
		}
		else {
			$this->minDebut = "0000-00-00"; // veut dire que la période n'est pas définie
			$this->maxDebut = "0000-00-00";
			$this->minFin = "0000-00-00";
			$this->maxFin = "0000-00-00";
		}
	}
	
}
?>