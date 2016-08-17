<?php

    function html_item($title, $icon, $url){
        global $url_parsed;

        $selected = "";
        if($url == $url_parsed["page"] || $url_parsed["page"] == "/" && $url == "")
            $selected = "selected-page";
        return "
            <li data-toggle='tooltip' data-placement='bottom' title='$title' class='$selected'>
                <a href='./$url'>
                    <span class='glyphicon glyphicon-$icon' aria-hidden='true'></span>
                </a>
            </li>
        ";
    }

    if(isset($_POST['connect_email']) && isset($_POST['connect_pass'])){
        $account->set_email(safe($_POST['connect_email']));
        $account->set_password(safe(md5($_POST['connect_pass'])));

        if($account->connect())
            $alert->add_success("Connexion réussie");
        else
            $alert->add_warning("Echec de la connexion");
    }

    $menu_items = [
        ["Acceuil", "home", ""],
        ["Import", "cloud-upload", "import"],
        ["Export", "cloud-download", "export"],
        ["Recherche", "search", "recherche"],
        ["Fusion", "resize-small", "fusion"],
        //["Dissocier", "resize-full", ""],
        ["Tables", "align-justify", "table"],
        ["Logs", "wrench", "logs"],
        //["Groupe", "user", ""]
        //["Console", "console", ""]
    ];

    $html_menu_items = "";
    foreach($menu_items as $item)
        $html_menu_items .= html_item($item[0], $item[1], $item[2]);

?>

<p>
    BUENOS AIRES
</p>
<ul>
    <?php echo $html_menu_items; ?>
</ul>

<?php if ($account->is_connected){ ?>
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
    <a type="button" class="connexion_btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-connexion">Connexion</a>
    <div class="modal fade" id="modal-connexion" tabindex="-1" role="dialog" aria-labellebdy="modal-connexion-label">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="modal-connexion-label">Connexion</h4>
                </div>
                <div class="modal-body">
                    <form name="identification" action="" method="post">
                        <div class="form-group">
                            <input class="form-control" type="email" name="connect_email" placeholder="Email" />
                        </div>
                        <div class="form-group">
                            <input class="form-control" type="password" name="connect_pass" placeholder="Password" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary">Se connecter</button>
                </div>
            </div>
        </div>
    </div>
    <a href="./new-account" class="connexion_btn btn btn-default btn-sm">Créer un compte</a>
</div>
<?php } ?>
