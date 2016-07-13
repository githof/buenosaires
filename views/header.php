<?php
    include("includes/error_handler.php");

    define("EPOUX",1);
	define("EPOUSE",2);
	define("PERE",3);
	define("MERE",4);
	define("TEMOIN",5);

?>

<div class="connexion">
<?php if ($account->is_connected){ ?>
    <div>
        Bonjour <?php $compte->get_full_name(); ?>
    </div>
    <?php if ($compte->get_rang() > 1){ ?>
    <button><a href="accueil/administration.php">Administration</a></button>
    <?php } ?>
    <button><a href="accueil/deconnexion.php">Deconnexion</a></button>
<?php } else {?>
    <form name="identification" action="accueil/connexion.php" method="post">
        <div class="form-group">
            <input class="form-control" type="email" name="email" placeholder="Email" />
        </div>
        <div class="form-group">
            <input class="form-control" type="password" name="pass" placeholder="Password" />
        </div>
        <input type="hidden" name="url_courant" value="http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ?>" />
        <input class="btn btn-primary btn-sm" type="submit" value="Connexion" />
    </form>
    <button class="btn btn-default btn-sm m-t-3"><a href="?p=new_account">Créer un compte</a></button>
<?php } ?>
</div>
<ul class="nav nav-sidebar">
	<li><a href="accueil/console.php">Console</a></li>
	<li><a href="importexport/">Import / Export</a></li>
    <li><a href="gestion/viewActes.php">Rechercher un acte</a></li>
    <li><a href="gestion/viewPersonnes.php">Rechercher une personne</a></li>
    <li><a href="gestion/fusion.php">Fusionner deux personnes</a></li>
    <li><a href="gestion/dissocier.php">Dissocier deux personnes</a></li>
    <li><a href="tables/viewTables.php">Voir les tables</a></li>
    <li><a href="gestion/viewGroupes.php">Groupe</a></li>
</ul>
