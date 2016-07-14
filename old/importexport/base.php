<?php 
include("../includes/header.php"); 
?>

<h1>CREER LE FICHIER DE BASE</h1>

<p class="p_center">Cette fonction n'est pas disponible sur le serveur web.</p>

<?php

	/*	
		try{
			
			include("../includes/lireXML.class.php");
			$lecteur = new LireXML("../data/matrimonios.base.xml");
			$actes = $lecteur->tabActes();
			
			foreach($actes as $acte){
				if (count($acte->children()) > 0) ajouter_fichier_log($acte->asXML());
			}       
			
			
			echo '<p>Op&eacute;ration r&eacute;ussie.</p>';

			
		}
		catch(Exception $erreur){
			echo $erreur;
		}
	*/
		
?>
		
<?php 
fclose($fichier_log_sql);
include("../includes/footer.php"); 
?>