<?php


    function all_family_name(){
        global $mysqli;
        $str = "";

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

<section>
    <h4>Recherche d'actes</h4>
    <form method="post" action="resultat">
        <div class="form-group">
            <label for="acte_id">Par ID</label>
            <input type="number" name="acte_id" id="acte_id">
        </div>
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
            <select name="acte_family_name" id="acte_family_name">
                <?php echo all_family_name(); ?>
            </select>
        </div>
    </form>
</section>
