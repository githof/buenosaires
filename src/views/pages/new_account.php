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

    /*
      Il faut récupérer les variables "true" de check_post_values()
      pour mettre la classe "has-error" à l'input
    */
    $error = "$name".'_error';
    $class_error = (${$error}) ?
      ' has-error' : '';

    if($name == 'password')
    {
      $info = '<p>Doit contenir au moins 6 caractères</p>';
      $value = '';
      /*
          La valeur de 'password' n'est pas conservée si le form contient une/des erreurs.
          Pour les autres champs, afficher de nouveau la valeur saisie
      */
    }
    else
    {
      $info = '';
      $value = 'value="' . get_post_var($name). '"';
    }

    $html = '<div class="form-group' . $class_error. '">
            <label class="col-sm-3 control-label"'
              . ' for="' $name. '">' . $label . '</label>
            <div class="col-sm-9">
                <input class="form-control"'
                  .  ' type="' . $type . '" name="' . $name .'"'
                  .  ' id="' .$name . '" '.$value.'/>'
                  . $info
                  . '
            </div>
        </div>';

    return $html;
}

function html_form_new_account($contents) {

    return '<form class="form-horizontal" name="new_account"'
      . ' action="./new-account" method="post">
      '
      . html_input_if('Email', 'email', 'email')
      . html_input_if('Password', 'password', 'password')
      . html_input_if('Prenom', 'text', 'prenom')
      . html_input_if('Nom', 'text', 'nom')
      . html_form_group(html_submit('col-sm-offset-5 col-sm-2 ', 'Envoyer'))
      . '
    </form>';
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
        ?>
    <!-- </form> -->
</div>

<?php } ?>
