<section>
    <h4>Choisir deux personnes à fusionner</h4>
    <div>
        <select multiple="multiple" id="fusion_personne_list"></select>
    </div>
</section>
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
