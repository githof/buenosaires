<?php
class LireXML{
	
	// Variables d'instances
	var $fichier;
	var $sources; // texte brut du xml
	var $tab_sources; // xml parsé en tableau
	
	// Constructeur
	function LireXML($fichier){
		$this->fichier = $fichier;
		$this->sources = file_get_contents($fichier);
	}
	
	/*
		Fonctions
	*/
	// On met les actes sous forme de tableau
	function tabActes(){
		$this->tab_sources = simplexml_load_string($this->sources);
		if ($this->tab_sources == false) throw new Exception("Echec lors de la lecture de simple XML.");
		return $this->tab_sources->ACTES->ACTE;	
	}
	
	function getSource(){
		return $this->sources;
	}
	

}

?>