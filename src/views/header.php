<?php

include_once(ROOT."src/html_entities.php");

//  ITEMS MENU GENERAL //
function html_item($title, $icon, $url){
    global $url_parsed;

    $selected = "";
    $page = (isset($url_parsed['page']))? $url_parsed['page'] : "";

    if($url == "" && $page == "/" || $url == $page)
        $selected = "selected-page";
    return "
        <li data-toggle='tooltip' data-placement='bottom' title='$title' class='$selected'>
            <a href='/$url'>
                <span class='glyphicon glyphicon-$icon' aria-hidden='true'></span>
            </a>
        </li>
    ";  //  <a href='./$url'>   //  *** Modifié le lien de base sur branche rewrite-index 
}

//  FORMS   //
function html_input($type, $name, $placeholder) {
    return '<input class="form-control" type="'.$type.'" name="'.$name.'" placeholder="'.$placeholder.'">';
}

function html_form_connexion() {    //  $contents  (inutile)
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

//  *** déplacé dans html_entities.php 
// function html_button($type, $class, $data, $value) {

//     return '
//         <button type="' . $type . '" 
//         class="' . $class . '" '
//         . $data . '>'
//         . $value 
//         . '</button>';
// }

function html_form_deconnexion(){

    $class = 'connexion_btn btn btn-default btn-sm';
    $data ='data-toggle="tooltip" 
            data-placement="bottom" 
            title="Deconnexion"';
    $value ='<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>';

    return '<form action="" method="post">'
                . html_hidden_type('action', 'deconnexion')
                . html_button('submit', $class, $data, $value)
                . '
            </form>';
}

function html_modal_connexion() {

    $data = 'data-dismiss="modal" aria-label="Close"';
    $value = '<span aria-hidden="true">
                &times;
            </span>';

    $modal_header = '<div class="modal-header">'
                        . html_button('button', 'close', $data, $value)
                        . '<h4 class="modal-title" 
                        id="modal-connexion-label">
                            Connexion
                        </h4>
                    </div>';
    $modal_body = '<div class="modal-body">'
                        . html_form_connexion()
                        . '
                    </div>';

    return '
        <div class="modal fade" id="modal-connexion" tabindex="-1" role="dialog" aria-labellebdy="modal-connexion-label">
            <div class="modal-dialog" role="document">
                <div class="modal-content">'
                    . $modal_header
                    . $modal_body
                . '</div>
            </div>
        </div>
        ';
}

//  DIV "connected" OU "connexion"  
//  A droite dans la navbar
function html_connected_or_not() {
    global $access_pages;
    global $account;

    //  si connecté
    if ($account->is_connected){ 
        $contents = '<div class="connected">
                    <span>' . $account->get_full_name() .'</span>&nbsp;';

        //  si accès admin 
        if(can_access($access_pages["administration"])) {   
            $contents .= '<a href="administration">'
                            . html_button('', 
                                'connexion_btn btn btn-default btn-sm m-t-3', 
                                '', 'Administration'
                            ) 
                            . 
                        '</a>';
        } 

        $contents .= html_form_deconnexion();

    //  si pas connecté 
    } else { 
        $contents = '<div class="connexion">'
                        . html_button(
                            'button', 
                            'connexion_btn btn btn-primary btn-sm', 
                            'data-toggle="modal" data-target="#modal-connexion"', 
                            'Connexion')
                        . html_modal_connexion()
                        . '<a href="./new-account" class="connexion_btn btn btn-default btn-sm">
                            Créer un compte
                        </a>';
    }

    $contents .= '</div>';
    
    return $contents;
}

//  toute la navbar
function html_navbar() {
    global $access_pages;

    $site_title = '<p>
                    BUENOS AIRES
                </p>';

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

    return $site_title
            . '<ul>'
            . $html_menu_items
            . '</ul>'
            . html_connected_or_not();

}

echo html_navbar(); 

?>
