<?php

    include_once($ROOT."src/html_entities.php");


    function html_select_personnes(){
        return "
            <section>
                <h4>Choisir deux personnes à fusionner</h4>
                <div>
                    <input type='text' name='autocomplete' placeholder='Recherche parmis les personnes'>
                    <span class='autocomplete-search'>recherche en cours ...</span>
                </div>
                <div id='auto-complete-results'>
                </div>
            </section>
        ";
    }

    function html_fusion(){
        ?>
        <div id="fusion-form">
            <button class="btn btn-primary" id="fusion-submit" disabled>Fusionner</button>
            <section>
                <h4>ID  <i>(Choisir l'ID à conserver)</i></h4>
                <div class="fusion-ids flex-horizontal">
                </div>
            </section>
            <section>
                <h4>Noms</h4>
                <div class="fusion-noms flex-horizontal">
                </div>
                <div>
                    <div class="help-block">Les noms séparés par une virgule et leurs attributs en parenthèse si besoin</div>
                    <input type="text" name="noms" placeholder="Ex: PERI, (de) BELGRANO">
                </div>
            </section>
            <section>
                <h4>Prenoms</h4>
                <div class="fusion-prenoms flex-horizontal">
                </div>
                <div>
                    <div class="help-block">Les prénoms séparés par une virgule</div>
                    <input type="text" name="prenoms" placeholder="Ex: Maria, Josefa">
                </div>
            </section>
            <section>
                <h4>Condition</h4>
                <div class="fusion-conditions flex-vertical">
                </div>
            </section>
            <section>
                <h4>Relations</h4>
                <div class="fusion-relations flex-vertical">
                </div>
            </section>
        </div>
        <?php
    }

    if(isset($ARGS["personne-A"], $ARGS["personne-B"])){
        echo html_fusion();
    }else{
        echo html_select_personnes();
    }

?>
