<?php 
include("../includes/header.php"); 
include("../includes/restriction.php");
include_once("../includes/fonctions_all.php"); 
restriction($compte, 2);
function existe_acte($acte){
  foreach($acte->attributes() as $key => $val){
    if ($key == "num"){
      $sql = mysql_query("SELECT id_acte FROM actes WHERE id_acte='$val'");
      if (mysql_num_rows($sql) > 0) return true;	
    }
  }
  return false;
}

?>

<h1>AJOUTER UN OU DES ACTES</h1>

Uploader un fichier xml contenant des actes &agrave; ajouter : 
<form name="upload_acte" method="post" action="" enctype="multipart/form-data">
<input name="fichier_upload" value="" type="file" />
<br>
<label for="only_new_by_file">Ignorer les actes déjà balisés</label>
<input type="checkbox" id="only_new_by_file" checked="checked" name="only_new">
<input type="hidden" name="MAX_FILE_SIZE" value="20000000">
<input type="hidden" name="hide" value="fichier" />
<input type="submit" value="Valider" />
</form>

Copier les actes &agrave; ajouter : (&lt;acte&gt; ... &lt;/acte&gt;)
<form name="add_acte" method="post" action="">
<textarea rows="15" cols="80" name="source"></textarea>
<input type="hidden" name="hide" value="text_acte" />
<br>
<label for="only_new_by_text">Ignorer les actes déjà balisés</label>
<input type="checkbox" id="only_new_by_text" checked="checked" name="only_new">
<input type="submit" value="Valider" />

</form>

<?php	

	if (isset($_POST['hide'])){
	
		try{
			include("../info/parametre.php"); // connexion à la base de donnée mysql
			include_once("../includes/lireXML.class.php"); // on importe l'objet pour lire le XML
			include_once("../includes/periode.class.php"); // on importe l'objet pour lire les dates
			include_once("../includes/fonctions_compilation.php"); // des fonctions sur mesure
			
			$msg = 'Op&eacute;ration r&eacute;ussie.';
			$flag = true;
			
			if ($_POST['hide'] == "fichier"){			
				$name_fichier = basename($_FILES['fichier_upload']['name']);
				$tab_ext = preg_split("/\./", $name_fichier);
				$ext = $tab_ext[count($tab_ext)-1];
				if ($ext == "xml") {
					move_uploaded_file($_FILES['fichier_upload']['tmp_name'], "../data/temp.xml");
				}
				$lecteur = new LireXML("../data/temp.xml");
				try {
					$actes = $lecteur->tabActes();
				} catch (Exception $e0) {
					$flag = false;
					$msg = $e0->getMessage();	
				}
			}
			else if ($_POST['hide'] == "text_acte"){
				$sources = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<document>\n<ACTES>\n".stripslashes($_POST['source'])."\n</ACTES>\n</document>\n";
				$file_tmp = fopen("../data/temp.xml", "w");
				fwrite($file_tmp, $sources);	
				$lecteur = new LireXML("../data/temp.xml");
				try {
					$actes = $lecteur->tabActes();
				} catch (Exception $e0) {
					$flag = false;
					$msg = $e0->getMessage();	
				}
			}			
			
			$only_new = $_POST['only_new'];
			// on remplit la table
			if ($flag){
				foreach($actes as $acte){
				  if (ok_pour_prendre_acte($acte))
				  {
				    echo '<ul>';
				    add_acte($acte, $only_new);
				    echo '</ul>';
				  }
				}
			}
			
			echo '<p>'.$msg.'</p>';
			
		}
		catch(Exception $erreur){
		  echo "<p>Erreur : " . $erreur->getMessage() . "</p>";
		}
	
	}
	
?>
<?php 
fclose($fichier_log_sql);
include("../includes/footer.php"); 
?>