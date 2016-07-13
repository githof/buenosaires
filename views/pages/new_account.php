<?php


    function get_post_var($value){
        if(isset($_POST[$value]))
            return $_POST[$value];
        return "";
    }
?>



<h1 class="page-header">
    Création d'un compte
</h1>

<div id="form_new_account">
    <form class="form-horizontal" name="new_account" action="?p=new_account" method="post">
        <div class="form-group">
            <label class="col-sm-3 control-label" for="email">Email</label>
            <div class="col-sm-9">
                <input class="form-control" type="email" name="email" id="email" value="<?php echo get_post_var('email'); ?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="password">Mot de passe</label>
            <div class="col-sm-9">
                <input class="form-control" type="password" name="password" id="password" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="prenom">Prenom</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="prenom" id="prenom" value="<?php echo get_post_var('prenom'); ?>" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="nom">Nom</label>
            <div class="col-sm-9">
                <input class="form-control" type="text" name="nom" id="nom" value="<?php echo get_post_var('nom'); ?>" />
            </div>
        </div>
        <div class="form-group">
            <input class="col-sm-offset-5 col-sm-2 btn btn-primary" type="submit" value="Envoyer" />
        </div>
    </form>
</div>
