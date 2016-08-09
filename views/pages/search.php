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

    function all_family_name($with_aucun = FALSE){
        global $mysqli;
        $str = "";

        if($with_aucun)
            $str .= "<option value='0'>Aucun</option>";

        $result = $mysqli->query("
            SELECT nom.id AS id, no_accent, value
            FROM nom LEFT JOIN attribut
            ON nom.attribut_id = attribut.id
            ORDER BY nom.no_accent
        ");
        if($result != FALSE && $result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $attribut_str = "";
                if(isset($row["value"]) && $row["value"] != "NULL")
                    $attribut_str = "{$row["value"]} ";
                $str .= "<option value='{$row["id"]}'>$attribut_str{$row["no_accent"]}</option>";
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
                <form method="post" action="resultat">
                    <div class="form-group">
                        <label for="acte_date_start">A partir de</label>
                        <input type="date" name="acte_date_start" id="acte_date_start">
                    </div>
                    <div class="form-group">
                        <label for="acte_date_end">Avant</label>
                        <input type="date" name="acte_date_end" id="acte_date_end">
                    </div>
                    <div class="form-group">
                        <label for="acte_family_name">Concernant la famille</label>
                        <select multiple="multiple" name="acte_family_name" id="acte_family_name">
                            <?php echo all_family_name(); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Rechercher">
                    </div>
                </form>
            </div>
        </section>
    </div>
    <div role="tabpanel" class="tab-pane" id="personnes">
        <section>
            <div>
                <form method="post" action="resultat">
                    <div class="form-group">
                        <label for="personne_date_start">A partir de</label>
                        <input type="date" name="personne_date_start" id="personne_date_start">
                    </div>
                    <div class="form-group">
                        <label for="personne_date_end">Avant</label>
                        <input type="date" name="personne_date_end" id="personne_date_end">
                    </div>
                    <div class="form-group">
                        <label for="personne_family_name">Concernant la famille</label>
                        <select name="personne_family_name" id="personne_family_name">
                            <?php echo all_family_name(TRUE); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Rechercher">
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
