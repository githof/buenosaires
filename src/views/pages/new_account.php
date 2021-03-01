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


function html_form_label($for, $label) {
    return '<label class="col-sm-3 control-label" for="'.$for.'">'.$label.'</label>';
}

//  inputs ont une classe "has-error" si un champ n'est pas rempli correctement
function html_input_if($email_error, $password_error, $prenom_error, $nom_error, $type, $name) {

    $email_error;
    $password_error;
    $prenom_error;
    $nom_error;

    // $form_error;

    $type;
    $name;
    
    $class;
    $value;

    /*  
        La valeur de 'password' n'est pas conservée si le form contient une/des erreurs
        Pour les autres champs, afficher de nouveau la valeur tapée
    */
    $name === 'password' ? $value = '' : $value =  'value="'.get_post_var($name).'"';

    /*
        Il faut récupérer les variables "true" de check_post_values() pour mettre la class "has-error" à l'input
        (même si la class n'active pas la règle Bootstrap associée, avant même mes changements)
    */  
    // ($form_error == $name.'_error') ? $class='form-control has-error' : $class='form-control';

    return
        '<div class="col-sm-9">
            <input class="'.$class.'" type="'.$type.'" name="'.$name.'" id="'.$name.'" '.$value.'/>
        </div>';
}

function html_form_group_if($for, $label, $email_error, $password_error, $prenom_error, $nom_error, $type, $name) {
    return '<div class="form-group">'.
        html_form_label($for, $label).
        html_input_if($email_error, $password_error, $prenom_error, $nom_error, $type, $name).'
    </div>';
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
    <form class="form-horizontal" name="new_account" action="./new-account" method="post">
    <?php 
        // echo 
        //     html_form_group_if('email', 'Email', $email_error, $password_error, $prenom_error, $nom_error, 'email', 'email').
        //     html_form_group_if('password', 'Mot de passe', $email_error, $password_error, $prenom_error, $nom_error, 'password', 'password').
        //     html_form_group_if('prenom', 'Prenom', $email_error, $password_error, $prenom_error, $nom_error, 'text', 'prenom').
        //     html_form_group_if('nom', 'Nom', $email_error, $password_error, $prenom_error, $nom_error, 'text', 'nom');

    ?>
        <!-- Les classes "has-error" n'activent pas la règle (border-color) de Bootstrap --> 
        <div class="form-group">
            <label class="col-sm-3 control-label" for="email">Email</label>
            <div class="col-sm-9">
                <input class="form-control <?php  if($email_error) echo 'has-error' ?>" type="email" name="email" id="email" value="<?php echo get_post_var('email'); ?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="password">Mot de passe</label>
            <div class="col-sm-9">
                <input class="form-control <?php  if($password_error) echo 'has-error' ?>" type="password" name="password" id="password" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="prenom">Prenom</label>
            <div class="col-sm-9">
                <input class="form-control <?php  if($prenom_error) echo 'has-error' ?>" type="text" name="prenom" id="prenom" value="<?php echo get_post_var('prenom'); ?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="nom">Nom</label>
            <div class="col-sm-9">
                <input class="form-control <?php  if($nom_error) echo 'has-error' ?>" type="text" name="nom" id="nom" value="<?php echo get_post_var('nom'); ?>" />
            </div>
        </div>
        <div class="form-group">
            <?php
                echo html_submit('col-sm-offset-5 col-sm-2 ', 'Envoyer');
            ?>
            <!-- <input class="col-sm-offset-5 col-sm-2 btn btn-primary" type="submit" value="Envoyer" /> -->
        </div>
    </form>
</div>

<?php } ?>
