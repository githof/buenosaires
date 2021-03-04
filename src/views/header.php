<?php

include_once(ROOT."src/html_entities.php");

function html_item($title, $icon, $url){
    global $url_parsed;

    $selected = "";
    $page = (isset($url_parsed['page']))? $url_parsed['page'] : "";

    if($url == "" && $page == "/" || $url == $page)
        $selected = "selected-page";
    return "
        <li data-toggle='tooltip' data-placement='bottom' title='$title' class='$selected'>
            <a href='./$url'>
                <span class='glyphicon glyphicon-$icon' aria-hidden='true'></span>
            </a>
        </li>
    ";
}

$menu_items = [
    ["Accueil", "home", ""],
    ["Import", "cloud-upload", "import"],
    ["Export", "cloud-download", "export"],
    ["Recherche", "search", "recherche"],
    ["Fusion", "resize-small", "fusion"],
    ["Dissocier", "resize-full", "dissocier"],
    ["Tables", "align-justify", "table"],
    ["Logs", "wrench", "logs"],
    //["Groupe", "user", ""]
    //["Console", "console", ""]
];

$html_menu_items = "";
foreach($menu_items as $item){
    if(can_access($access_pages[$item[2]]))
        $html_menu_items .= html_item($item[0], $item[1], $item[2]);
}

function html_input($type, $name, $placeholder) {
    return '<input class="form-control" type="'.$type.'" name="'.$name.'" placeholder="'.$placeholder.'">';
}

function html_form_connexion($contents) {
    $fermer = '<button type="button"'
      . ' class="btn btn-default" data-dismiss="modal">'
      . 'Fermer</button>';
    $se_connecter = '<button type="submit"'
    . ' class=" btn btn-primary">Se connecter</button>';
    $buttons = html_form_group($fermer .' '. $se_connecter);

    return '<div class="modal-body">
        <form name="identification" action="" method="post">'
        . html_hidden_type('action', 'connexion')
        . html_form_group( 
            html_input('email', 'connect_email', 'Email')
        )
        . html_form_group( 
            html_input('password', 'connect_pass', 'Password')
        ) 
        . $buttons
        . '</form>
    </div>';
}

?>

<p>
    BUENOS AIRES
</p>
<ul>
    <?php echo $html_menu_items; ?>
</ul>

<?php if ($account->is_connected){ ?>
<div class="connected">
    <span><?php echo $account->get_full_name(); ?></span>
    <?php if (can_access($access_pages["administration"])){ ?>
        <a href="administration"><button class="connexion_btn btn btn-default btn-sm m-t-3">Administration</button></a>
    <?php } ?>
    <form action="" method="post">
        <?php
            echo html_hidden_type('action', 'deconnexion');
        ?>
        <button type="submit" data-toggle='tooltip' data-placement='bottom' title='Deconnexion' class="connexion_btn btn btn-default btn-sm">
            <span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>
        </button>
    </form>
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
                <?php
                    echo html_form_connexion($contents);
                ?>
                </div>
            </div>
        </div>
    </div>
    <a href="./new-account" class="connexion_btn btn btn-default btn-sm">Créer un compte</a>
</div>
<?php } ?>
