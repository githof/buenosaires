<?php
    include("includes/error_handler.php");
	include_once("includes/account_log.php");

    define("EPOUX",1);
	define("EPOUSE",2);
	define("PERE",3);
	define("MERE",4);
	define("TEMOIN",5);

    function affiche_barre_connexion($compte){
    	echo 'Bonjour '.$compte->pseudo.' | <a href="accueil/deconnexion.php">D&eacute;connexion</a>';
    	if ($compte->rang > 1) echo ' | <a href="accueil/administration.php">Administration</a>';
    }

    function affiche_form_connect(){
    	echo '

    	';
    }

    $compte = identification_cookie();

?>

<div class="identifiant">
	<?php if ($compte != NULL){ ?>
        Bonjour <?php $compte->pseudo ?> | <a href="accueil/deconnexion.php">D&eacute;connexion</a>
        <?php if ($compte->rang > 1) ?>
            | <a href="accueil/administration.php">Administration</a>
	<?php } else ?>
        <form name="identification" action="accueil/connexion.php" method="post">
            <table border="0" cellpadding="0" cellspacing="0" class="none">
                <tr>
                    <td class="none">Pseudo</td>
                    <td class="none">Mot de passe</td>
                    <td class="none"></td>
                </tr>
                <tr>
                    <td class="none"><input type="text" name="pseudo" value="" /></td>
                    <td class="none"><input type="password" name="pass" value="" />
                        <input type="hidden" name="url_courant" value="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'" /></td>
                    <td class="none"><input type="submit" value="Connexion" /></td>
                </tr>
             </table>
        </form>
</div>

<div class="menu">
	<p><a href="accueil/index.php">MENU</a></p>
	<ul>
    	<li><a href="accueil/console.php">Console</a></li>
    	<li><a href="importexport/">Import / Export</a></li>
        <li><a href="gestion/viewActes.php">Rechercher<br />un acte</a></li>
        <li><a href="gestion/viewPersonnes.php">Rechercher<br />une personne</a></li>
        <li><a href="gestion/fusion.php">Fusionner<br />deux personnes</a></li>
        <li><a href="gestion/dissocier.php">Dissocier<br />deux personnes</a></li>
        <li><a href="tables/viewTables.php">Voir les tables</a></li>
        <li><a href="gestion/viewGroupes.php">Groupe</a></li>
    </ul>
</div>

<div class="page">
</div>
