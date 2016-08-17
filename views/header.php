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

<!-- <?php if ($account->is_connected){ ?>
<div class="connected">
    <div>
        <h3><?php echo $account->get_full_name(); ?></h3>
    </div>
    <?php if ($account->get_rang() > 1){ ?>
    <a href="accueil/administration.php"><button class="connexion_btn btn btn-default btn-sm m-t-3">Administration</button></a>
    <?php } ?>
    <a href="./disconnect"><button class="connexion_btn btn btn-default btn-sm m-t-3">Deconnexion</button></a>
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
    <a href="./new-account"><div class="connexion_btn btn btn-default btn-sm m-t-3">Créer un compte</div></a>
</div>
<?php } ?> -->
<p>
    BUENOS AIRES
</p>
<ul>
    <li data-toggle="tooltip" data-placement="bottom" title="Acceuil">
        <a href="./">
            <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
        </a>
    </li>
	<!-- <li>
        <a href="./">
            <span class="glyphicon glyphicon-console" aria-hidden="true"></span>
            <span class="nav_item">Console</span>
        </a>
    </li> -->
	<li data-toggle="tooltip" data-placement="bottom" title="Import">
        <a href="./import">
            <span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span>
        </a>
    </li>
    <li data-toggle="tooltip" data-placement="bottom" title="Export">
        <a href="./export">
            <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span>
        </a>
    </li>
    <li data-toggle="tooltip" data-placement="bottom" title="Recherche">
        <a href="./recherche">
            <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
        </a>
    </li>
    <li data-toggle="tooltip" data-placement="bottom" title="Fusion">
        <a href="./fusion">
            <span class="glyphicon glyphicon-resize-small" aria-hidden="true"></span>
        </a>
    </li>
    <li data-toggle="tooltip" data-placement="bottom" title="Dissocier">
        <a href="./">
            <span class="glyphicon glyphicon-resize-full" aria-hidden="true"></span>
        </a>
    </li>
    <li data-toggle="tooltip" data-placement="bottom" title="Tables">
        <a href="./table">
            <span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span>
        </a>
    </li>
    <!-- <li>
        <a href="./">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            <span class="nav_item">Groupe</span>
        </a>
    </li> -->
    <li data-toggle="tooltip" data-placement="bottom" title="Logs">
        <a href="./logs">
            <span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>
        </a>
    </li>
</ul>
