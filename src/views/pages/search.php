<?php


    function all_acte_id(){
        global $mysqli;
        $str = "<option value='0'>Aucun</option>";

        $result = $mysqli->select("acte", ["id"]);
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc())
                $str .= "<option value='{$row["id"]}'>{$row["id"]}</option>";
        }
        return $str;
    }

    function all_noms(){
        global $mysqli;
        $str = "";

        $result = $mysqli->query("
            SELECT id, no_accent
            FROM nom
            ORDER BY no_accent
        ");
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $str .= "<option value='{$row["id"]}'>{$row["no_accent"]}</option>";
            }
        }
        return $str;
    }

    function all_prenoms(){
        global $mysqli;
        $str = "";

        $result = $mysqli->query("
            SELECT id, no_accent
            FROM prenom
            ORDER BY no_accent
        ");
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $str .= "<option value='{$row["id"]}'>{$row["no_accent"]}</option>";
            }
        }
        return $str;
    }

?>

<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active">
        <a href="recherche#actes" aria-controls="actes" role="tab" data-toggle="tab">Actes</a>
    </li>
    <li role="presentation">
        <a href="recherche#personnes" aria-controls="personnes" role="tab" data-toggle="tab">Personnes</a>
    </li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="actes">
        <section>
            <div>
                <form method="get" action="resultat">
                    <div class="form-group">
                        <label for="acte_date_start">A partir de</label>
                        <input type="date" name="acte_date_start" id="acte_date_start">
                        <p class="help-block">Format: AAAA-MM-JJ</p>
                    </div>
                    <div class="form-group">
                        <label for="acte_date_end">Avant</label>
                        <input type="date" name="acte_date_end" id="acte_date_end">
                        <p class="help-block">Format: AAAA-MM-JJ</p>
                    </div>
                    <div class="form-group">
                        <label for="acte_noms">Contenant les personnes avec pour nom de famille</label>
                        <select multiple="multiple" name="acte_noms[]" id="acte_noms">
                            <?php echo all_noms(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Rechercher">
                    </div>
                    <input type="hidden" name="type" value="acte">
                </form>
            </div>
        </section>
    </div>
    <div role="tabpanel" class="tab-pane" id="personnes">
        <section>
            <div>
                <form method="get" action="resultat">
                    <!-- <div class="form-group">
                        <label for="personne_date_start">A partir de</label>
                        <input type="date" name="personne_date_start" id="personne_date_start">
                    </div>
                    <div class="form-group">
                        <label for="personne_date_end">Avant</label>
                        <input type="date" name="personne_date_end" id="personne_date_end">
                    </div> -->
                    <div class="form-group">
                        <label for="personne_noms">Avec pour nom(s) de famille</label>
                        <select multiple="multiple" name="personne_noms[]" id="personne_noms">
                            <?php echo all_noms(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="personne_prenoms">Avec pour prenom(s)</label>
                        <select multiple="multiple" name="personne_prenoms[]" id="personne_prenoms">
                            <?php echo all_prenoms(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Rechercher">
                    </div>
                    <input type="hidden" name="type" value="personne">
                </form>
            </div>
        </section>
    </div>
</div>
