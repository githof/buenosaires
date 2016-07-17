<?php

    if(isset($_POST['connect_email']) && isset($_POST['connect_pass'])){
        $account->set_email(safe($_POST['connect_email']));
        $account->set_password(safe(md5($_POST['connect_pass'])));

        if($account->connect())
            $alert->add_success("Connexion réussie");
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
    <a href="accueil/administration.php"><button class="connexion_btn btn btn-default btn-sm m-t-3">Administration</button></a>
    <?php } ?>
    <a href="?p=disconnect"><button class="connexion_btn btn btn-default btn-sm m-t-3">Deconnexion</button></a>
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
        <input class="connexion_btn btn btn-primary btn-sm" type="submit" value="Connexion" />
    </form>
    <a href="./?p=new_account"><div class="connexion_btn btn btn-default btn-sm m-t-3">Créer un compte</div></a>
</div>
<?php } ?>
<ul class="nav nav-sidebar">
    <li>
        <a href="./">
            <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
            <span class="nav_item">Acceuil</span>
        </a>
    </li>
	<li>
        <a href="accueil/console.php">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="nav_item">Console</span>
        </a>
    </li>
	<li>
        <a href="./?p=import">
            <span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span>
            <span class="nav_item">Import</span>
        </a>
    </li>
    <li>
        <a href="./?p=export">
            <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span>
            <span class="nav_item">Export</span>
        </a>
    </li>
    <li>
        <a href="gestion/viewActes.php">
            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
            <span class="nav_item">Actes</span>
        </a>
    </li>
    <li>
        <a href="gestion/viewPersonnes.php">
            <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
            <span class="nav_item">Personnes</span>
        </a>
    </li>
    <li>
        <a href="gestion/fusion.php">
            <span class="glyphicon glyphicon-resize-small" aria-hidden="true"></span>
            <span class="nav_item">Fusionner</span>
        </a>
    </li>
    <li>
        <a href="gestion/dissocier.php">
            <span class="glyphicon glyphicon-resize-full" aria-hidden="true"></span>
            <span class="nav_item">Dissocier</span>
        </a>
    </li>
    <li>
        <a href="tables/viewTables.php">
            <span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span>
            <span class="nav_item">Tables</span>
        </a>
    </li>
    <li>
        <a href="gestion/viewGroupes.php">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            <span class="nav_item">Groupe</span>
        </a>
    </li>
</ul>
