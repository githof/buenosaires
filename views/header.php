<?php

    if(isset($_POST['connect_email']) && isset($_POST['connect_pass'])){
        $account->set_email(safe($_POST['connect_email']));
        $account->set_password(safe(md5($_POST['connect_pass'])));

        if($account->connect())
            $alert->add_success("Connecté avec succès");
        else
            $alert->add_warning("Echec de la connexion");
    }

?>

<?php if ($account->is_connected){ ?>
<div class="connected">
    <div>
        <h3><?php echo $account->get_full_name(); ?></h3>
    </div>
    <?php if ($account->get_rang() > 1){ ?>
    <button class="btn btn-default btn-sm m-t-3"><a href="accueil/administration.php">Administration</a></button>
    <?php } ?>
    <button class="btn btn-default btn-sm m-t-3"><a href="?p=disconnect">Deconnexion</a></button>
</div>
<?php } else {?>
<div class="connexion">
    <form name="identification" action="" method="post">
        <div class="form-group">
            <input class="form-control" type="email" name="connect_email" placeholder="Email" />
        </div>
        <div class="form-group">
            <input class="form-control" type="password" name="connect_pass" placeholder="Password" />
        </div>
        <input class="btn btn-primary btn-sm" type="submit" value="Connexion" />
    </form>
    <div class="btn btn-default btn-sm m-t-3"><a href="./?p=new_account">Créer un compte</a></div>
</div>
<?php } ?>
<ul class="nav nav-sidebar">
    <li><a href="./">Acceuil</a></li>
	<li><a href="accueil/console.php">Console</a></li>
	<li><a href="./?p=import">Import</a></li>
    <li><a href="./?p=export">Export</a></li>
    <li><a href="gestion/viewActes.php">Rechercher un acte</a></li>
    <li><a href="gestion/viewPersonnes.php">Rechercher une personne</a></li>
    <li><a href="gestion/fusion.php">Fusionner deux personnes</a></li>
    <li><a href="gestion/dissocier.php">Dissocier deux personnes</a></li>
    <li><a href="tables/viewTables.php">Voir les tables</a></li>
    <li><a href="gestion/viewGroupes.php">Groupe</a></li>
</ul>
