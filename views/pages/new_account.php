<?php


    function get_post_var($value){
        if(isset($_POST[$value]))
            return $_POST[$value];
        return "";
    }
?>



<h1>
    Création d'un compte
</h1>

<div>
    <form name="new_account" action="?p=new_account" method="post">
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo get_post_var('email'); ?>" />
        </div>
        <div>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" />
        </div>
        <div>
            <label for="prenom">Prenom</label>
            <input type="text" name="prenom" id="prenom" value="<?php echo get_post_var('prenom'); ?>" />
        </div>
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="<?php echo get_post_var('nom'); ?>" />
        </div>
        <div>
            <input type="submit" value="Envoyer" />
        </div>
    </form>
</div>
