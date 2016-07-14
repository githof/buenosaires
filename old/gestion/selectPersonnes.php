<?php
/*
 * crée un tableau $personnes pour y recopier les id des personnes sélectionnées, 
 * et afficher la liste des id, pour vérifier qu'on récupère bien ce qu'on veut.
 */
	if(!isset($_POST)){
			echo "ERREUR. AUCUN FORMULAIRE SOUMIT";
	}else{
		  if(empty($_POST)){
				echo "AUCUNE PERSONNE SELECTIONNER";
		  }else{
				foreach($_POST as $id => $nom){
					echo $nom.' --------> '.$id.' <br />';
				}
				
				//OPTION DE VIEWGROUPE (sans s)
				if(isset($_POST['choix'])){
					switch ($_POST['choix']) {
							case 'creer':
								echo "i égal 0";
								break;
							case 'afficher':
								echo "i égal 1";
								break;
							case 'supprimer':
								echo "i égal 2";
								break;
							case 'exporter':
								echo "i égal 2";
								break;
							default:
							   echo "DESOLER se choix n est pas pris en compte .";
						}	
					
				}//fin option
		}
	}
?>
