<?php
include "diff-actes.php";
//IMPORTATION DES FONCTIONS
if (! isset($_POST) ){
    echo "Error le formulaire n a pas ete excuter";
}else{
      if ($_FILES["fichier1"]["error"] > 0 ) {
                echo "Error: " . $_FILES["fichier1"]["error"] . "<br>";
         } else{
                if ($_FILES["fichier2"]["error"] > 0 ) {
		        echo "Error: " . $_FILES["fichier2"]["error"] . "<br>";
		}
		else {
		      echo "FICHIERS Uploads: " . $_FILES["fichier1"]["name"] . " et " . $_FILES["fichier2"]["name"] . "<br>";
		      $extensions_valides = array( 'xml' , 'php' );
		      //1. strrchr renvoie l'extension avec le point (« . »).
		      //2. substr(chaine,1) ignore le premier caractère de chaine.
		      //3. strtolower met l'extension en minuscules.
		      $extension_upload = strtolower(  substr(  strrchr($_FILES["fichier1"]["name"], '.')  ,1)  );
		      if ( in_array($extension_upload,$extensions_valides) ) echo "Extension correcte pour le fichier 1";
		      else {
			echo "Extension INVALIDE pour le fichier 1"; 
			continue;
		      }
		      echo "<br >";
		      $extension_upload = strtolower(  substr(  strrchr($_FILES["fichier2"]["name"], '.')  ,1)  );
		      if ( in_array($extension_upload,$extensions_valides) ) echo "Extension correcte pour le fichier 2";
		      else {
			echo "Extension INVALIDE pour le fichier 2"; 
			continue;
		      }
		      echo "<br >";
		      
		      $src_file_TmpLoc1 = $_FILES["fichier1"]["tmp_name"];
			  $src_file_TmpLoc2 = $_FILES["fichier2"]["tmp_name"];

		      $dest_tmp_file1 = tempnam("../data/Temp/","traitement.php");
		      $dest_tmp_file2 = tempnam("../data/Temp/","traitement.php");
				
			  move_uploaded_file($src_file_TmpLoc1,$dest_tmp_file1);
		      $moveResult = move_uploaded_file($src_file_TmpLoc2,$dest_tmp_file2);
		      if ($moveResult == true) {
				$tableau = compare_fichier($dest_tmp_file1,$dest_tmp_file2) ;
				affiche_diff_ids($tableau);
				unlink($dest_tmp_file1);
				unlink($dest_tmp_file2);
				afficheTableau($tableau);
		      } else {
				unlink($dest_tmp_file1);
				unlink($dest_tmp_file2);
				echo "ERROR: Fichier non deplacer";
		      }
		      
		}
      }
      }
?> 
