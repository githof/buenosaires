<section>
    <h4>Choisir une personne à dissocier</h4>
    <div>
        <select multiple="multiple" id="dissocier_personne_list"></select>
    </div>
</section>
<div id="dissocier-form">
    <button class="btn btn-primary" id="dissocier-submit" disabled>Dissocier</button>
    <input type="hidden" name="s" value="dissocier_exec">
    <section>
        <h4>ID</h4>
        <div class="dissocier-ids flex-vertical">
        </div>
    </section>
    <section>
        <h4>Noms</h4>
        <div class="dissocier-noms flex-horizontal">
        </div>
        <div>
            <div class="help-block">Les noms séparés par une virgule et leurs attributs en parenthèse si besoin</div>
            <div>
                <label for="dissocier-noms-A">Noms de la personne d'origine</label>
                <input type="text" id="dissocier-noms-A" name="noms-A" placeholder="Ex: PERI, (de) BELGRANO">
            </div>
            <div>
                <label for="dissocier-noms-B">Noms de la nouvelle personne</label>
                <input type="text" id="dissocier-noms-B" name="noms-B" placeholder="Ex: PERI, (de) BELGRANO">
            </div>
        </div>
    </section>
    <section>
        <h4>Prenoms</h4>
        <div class="dissocier-prenoms flex-horizontal">
        </div>
        <div>
            <div class="help-block">Les prénoms séparés par une virgule</div>
            <div>
                <label for="dissocier-prenoms-A">Prenoms de la personne d'origine</label>
                <input type="text" id="dissocier-prenoms-A" name="prenoms-A" placeholder="Ex: Maria, Josefa">
            </div>
            <div>
                <label for="dissocier-prenoms-B">Prenoms de la nouvelle personne</label>
                <input type="text" id="dissocier-prenoms-B" name="prenoms-B" placeholder="Ex: Maria, Josefa">
            </div>
        </div>
    </section>
    <section>
        <h4>Condition</h4>
        <div class="dissocier-conditions flex-vertical">
        </div>
    </section>
    <section>
        <h4>Relations</h4>
        <div class="dissocier-relations flex-vertical">
        </div>
    </section>
</div>
