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

/*  Les inputs ont une classe "has-error" si un champ n'est pas rempli correctement.
    Or pour que la class "has-error" soit prise en compte par Bootstrap elle doit
    être placée sur form-group, pas sur l'input    */
function html_input_if($label, $type, $name) {
    global $email_error, $password_error, $prenom_error, $nom_error;

    /*
      Il faut récupérer les variables "true" de check_post_values()
      pour mettre la classe "has-error" à form-group
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
              . ' for="' .$name. '">' . $label . '</label>
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

    return '<div id="form_new_account">
        <form class="form-horizontal" name="new_account"'
        . ' action="./new-account" method="post">
          '
          . html_input_if('Email', 'email', 'email')
          . html_input_if('Password', 'password', 'password')
          . html_input_if('Prenom', 'text', 'prenom')
          . html_input_if('Nom', 'text', 'nom')
          . html_form_group(html_submit('col-sm-offset-5 col-sm-2 ',
                                        'Envoyer'))
          . '
        </form>
      </div>';
}

function html_div_message($message)
{
  return "<div>$message</div>\n";
}

function create_account()
{
  global $account;

  $account->set_email(safe($_POST['email']));
  $account->set_password(safe(md5($_POST['password'])));
  $account->set_prenom(safe($_POST['prenom']));
  $account->set_nom(safe($_POST['nom']));

  return $account->add_into_db();
}

if($account->is_connected)
  echo html_div_message('Vous êtes déjà connecté(e) avec un compte');
else if(isset($_POST['email']) && check_post_values()){
    if(create_account())
      echo html_div_message('Compte crée avec succès !');
    else
      echo html_div_message('Erreur lors de la création du compte');
}
else
  echo html_form_new_account($contents);
?>
