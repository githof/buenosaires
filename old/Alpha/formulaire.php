<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Formulaire de teste</title>
    </head>
    <body>
   <form method="post" action="traitement.php"  enctype="multipart/form-data">
   <p>Formulaire vous permettant de charger et de comparer deux fichier XML et 
       d affichier leurs diferences en fonction de leur id (identifiant) <br />
       * 0 si les listes sont identiques (mêmes valeurs, dans le même ordre)<br />
       * 1 si la premier  est plus longue <br />
       * 2 si la Deuxieme est plus longue <br />
       * 3 si les listes ont la même longueur mais qu elles ne sont pas identiques<br />
   </p>
        <fieldset>
              <legend>Vos fichier</legend> 
              <label for="fichier1">Charger le premier fichier :</label><br />
              <input type="file" name="fichier1" id="f1" required /><br />
              <label for="fichier2">Charger le deuxieme fichier :</label><br />
              <input type="file" name="fichier2" id="f2" required/><br />
	      <br />
              <input type="submit" value="Envoyer" />
       </fieldset>
   </form>
    </body>
</html>
