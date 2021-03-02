<?php

include_once(ROOT."src/html_entities.php");

$email_error = false;
$password_error = false;
$prenom_error = false;
$nom_error = false;

function get_post_var($value){
    if(isset($_POST[$value]))
        return $_POST[$value];
    return "";
}

function check_post_values(){
    global $email_error, $password_error, $prenom_error, $nom_error;

    $email_error = !isset($_POST['email']) ||
        !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    $password_error = !isset($_POST['password']) ||
        strlen($_POST['password']) < 6;

    $prenom_error = !isset($_POST['prenom']) ||
        strlen($_POST['prenom']) < 1;

    $nom_error = !isset($_POST['nom']) ||
        strlen($_POST['nom']) < 1;

    return !$email_error && !$password_error && !$prenom_error && !$nom_error;
}

//  inputs ont une classe "has-error" si un champ n'est pas rempli correctement
/*  Pour que la class "has-error" soit prise en charge par Bootstrap elle doit 
    être placée sur form-group, pas sur l'input    */
function html_input_if($label, $type, $name) {
    global $email_error, $password_error, $prenom_error, $nom_error;

    //  pour inputs
    $type;
    $name;
    $class;
    $value;

    /*  
        La valeur de 'password' n'est pas conservée si le form contient une/des erreurs
        Pour les autres champs, afficher de nouveau la valeur saisie
    */
    $value = $name === 'password' ? '' : 'value="'.get_post_var($name).'"';

    //  Il faut récupérer les variables "true" de check_post_values() pour mettre la class "has-error" à l'input

    //  $form_error renvoie false, pas des noms de variables, on ne peut pas l'utiliser dans une condition 
    // ($form_error == $name.'_error') ? $class='form-control has-error' : $class='form-control';
    if($name === 'email') { 
        if($email_error===true) 
            $class = ' has-error';
        else 
            $class = '';
    } 
    if($name === 'password') { 
        if($password_error===true) 
            $class = ' has-error';
        else 
            $class = '';
    } 
    if($name === 'prenom') { 
        if($prenom_error===true) 
            $class = ' has-error';
        else 
            $class = '';
    } 
    if($name === 'nom') { 
        if($nom_error===true) 
            $class = ' has-error';
        else 
            $class = '';
    } 
    $html = '<label class="col-sm-3 control-label" for="'.$name.'">'.$label.'</label>
        <div class="col-sm-9">
            <input class="form-control '.$class.'" type="'.$type.'" name="'.$name.'" id="'.$name.'" '.$value.'/>
        </div>';

    return $html;
}

function html_form_group_input($label, $type, $name) {
    return '<div class="form-group">'.
        html_input_if($label, $type, $name).'
    </div>';
}

function html_form_new_account($contents) {

    $contents = html_form_group_input('Email', 'email', 'email').
    html_form_group_input('Password', 'password', 'password').
    html_form_group_input('Prenom', 'text', 'prenom').
    html_form_group_input('Nom', 'text', 'nom').
    html_form_group(html_submit('col-sm-offset-5 col-sm-2 ', 'Envoyer'));

    return '<form class="form-horizontal" name="new_account" action="./new-account" method="post">'. 
        $contents.
    '</form>';
}


if($account->is_connected){
?>

<div>
    Vous êtes déjà connecté avec un compte
</div>
<?php
}else if(isset($_POST['email']) && check_post_values()){
    $account->set_email(safe($_POST['email']));
    $account->set_password(safe(md5($_POST['password'])));
    $account->set_prenom(safe($_POST['prenom']));
    $account->set_nom(safe($_POST['nom']));

    $res = $account->add_into_db();

    if($res){
?>

<div>
    Compte crée avec succès !
</div>
<?php
    }else{
?>

<div>
    Erreur lors de la création du compte
</div>
<?php
    }
}else{
?>

<div id="form_new_account">
    <!-- <form class="form-horizontal" name="new_account" action="./new-account" method="post"> -->
        <?php 
            echo html_form_new_account($contents);
            // echo html_form_group_input('Email', 'email', 'email').
            // html_form_group_input('Password', 'password', 'password').
            // html_form_group_input('Prenom', 'text', 'prenom').
            // html_form_group_input('Nom', 'text', 'nom').
            // html_form_group(html_submit('col-sm-offset-5 col-sm-2 ', 'Envoyer'))
        ?>
    <!-- </form> -->
</div>

<?php } ?>
