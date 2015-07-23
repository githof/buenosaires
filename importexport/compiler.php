<?php 
include("../includes/header.php"); 
include("../includes/restriction.php");
restriction($compte, 2);
include("../includes/fonctions_all.php");

// on fait la sauvegarde du fichier bsql dans un deuxieme fichier
include_once("../includes/fonctions_copy_fichier.php");
copy_fichier_bsql();

?>

<h1>COMPILER LES INFORMATIONS</h1>

<?php

	
		try{
			include("../info/parametre.php"); // connexion à la base de donnée mysql
			include("../includes/creer_tables.php"); // on créer les tables dans la base de données
					
			include("../includes/lireXML.class.php"); // on importe l'objet pour lire le XML
			include("../includes/periode.class.php"); // on importe l'objet pour lire les dates
			include("../includes/fonctions_compilation.php"); // des fonctions sur mesures 			
			  
			// on créé l'objet
			$lecteur = new LireXML("../data/matrimonios.base.xml");
			//$lecteur = new LireXML("./matrimoniostest.xml");
			// on récupère les actes sous forme d'un tableau
			try {
			  $actes = $lecteur->tabActes();
							
			  // on remplit la table
			  $msg = "Op&eacute;ration r&eacute;ussie.";
			  foreach($actes as $acte){
			    if(ok_pour_prendre_acte($acte))
			      {
				try {
				  add_acte($acte);
				} catch(Exception $e1) {
				  $msg = $e1->getMessage();	
				}
			      }
			  }			
			} catch (Exception $e0) {
				$msg = $e0->getMessage();	
			}

			mysql_close(); // fin de la connexion à la base de donnée mysql
			echo '<p>'.$msg.'</p>';

			
		}
		catch(Exception $erreur){
			echo $erreur;
		}
		
		
?>
		
<?php 
fclose($fichier_log_sql);
include("../includes/footer.php"); 
?>