<?php

    include_once(ROOT."src/html_entities.php");

    function print_table_acte($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $date_str = "";
            if(isset($row["date_start"], $row["date_end"]))
                $date_str = html_date($row["date_start"], $row["date_end"]);

            $epoux_str = "";
            if(isset($row["epoux"]))
                $epoux_str = html_personne(personne_memory($row["epoux"]));

            $epouse_str = "";
            if(isset($row["epouse"]))
                $epouse_str = html_personne(personne_memory($row["epouse"]));

            $rows .= "
                <tr>
                    <td><a href='acte/{$row["id"]}'>{$row["id"]}</a></td>
                    <td>$epoux_str</td>
                    <td>$epouse_str</td>
                    <td>$date_str</td>
                </tr>";
        }
        return "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Epoux</th>
                        <th>Epouse</th>
                        <th>Date</th>
                    </tr>
                <thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_acte_contenu($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $rows .= "
                <tr>
                    <td><a href='acte/{$row["acte_id"]}'>{$row["acte_id"]}</a></td>
                    <td>{$row["contenu"]}</td>
                </tr>";
        }
        return "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>ID acte</th>
                        <th>Contenu</th>
                    </tr>
                <thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_personne($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $html_personne = html_personne(personne_memory($row["id"]), FALSE, FALSE);
            $rows .= "
                <tr>
                    <td><a href='personne/{$row["id"]}'>{$row["id"]}</a></td>
                    <td>$html_personne</td>
                </tr>";
        }
        return "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                    </tr>
                <thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_relation($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $relation = new Relation();
            $relation->result_from_db($row);

            $html_statut = html_relation_statut($relation->get_statut_name());
            $html_source = html_personne(personne_memory($relation->personne_source->id));
            $html_destination = html_personne(personne_memory($relation->personne_destination->id));
            $html_actes = html_list_actes($relation->actes);

            $rows .= "
                <tr>
                    <td>$html_source</td>
                    <td>$html_statut</td>
                    <td>$html_destination</td>
                    <td>$html_actes</td>
                </tr>";
        }
        return  "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>Personne source</th>
                        <th>Statut</th>
                        <th>Personne destination</th>
                        <th>Actes</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_condition($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $condition = new Condition();
            $condition->result_from_db($row);

            $html_text = html_condition_text($condition->text);
            $html_personne = html_personne(personne_memory($condition->personne->id));
            $html_source = html_condition_source($condition->get_source_name());
            $html_actes = html_list_actes($condition->actes);

            $rows .= "
                <tr>
                    <td>$html_text</td>
                    <td>$html_personne</td>
                    <td>$html_source</td>
                    <td>$html_actes</td>
                </tr>";
        }
        return  "
            <table class='table table-striped table-hover table-condensed'>
                <thead>
                    <tr>
                        <th>Condition</th>
                        <th>Personne</th>
                        <th>Source</th>
                        <th>Actes</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_source($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $rows .= "
                <tr>
                    <td>{$row["id"]}</td>
                    <td>{$row["valeur"]}</td>
                </tr>";
        }
        return  "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_status($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $rows .= "
                <tr>
                    <td>{$row["id"]}</td>
                    <td>{$row["valeur"]}</td>
                </tr>";
        }
        return  "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_attribut($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $rows .= "
                <tr>
                    <td>{$row["id"]}</td>
                    <td>{$row["value"]}</td>
                </tr>";
        }
        return  "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Valeur</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_prenom($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $rows .= "
                <tr>
                    <td>{$row["id"]}</td>
                    <td>{$row["prenom"]}</td>
                    <td>{$row["no_accent"]}</td>
                </tr>";
        }
        return  "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Prenom</th>
                        <th>Sans accent</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table_nom($results){
        $rows = "";
        while($row = $results->fetch_assoc()){
            $attr = "";
            if(isset($row["value"]))
                $attr = $row["value"];
            $rows .= "
                <tr>
                    <td>{$row["id"]}</td>
                    <td>{$row["nom"]}</td>
                    <td>{$row["no_accent"]}</td>
                    <td>$attr</td>
                </tr>";
        }
        return  "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Sans accent</th>
                        <th>Attribut</th>
                    </tr>
                </thead>
                <tbody>
                    $rows
                </tbody>
            </table>";
    }

    function print_table($table_name){
        global $mysqli, $alert, $page_title;

        if($table_name == "nom"){
            $results = $mysqli->query("
                SELECT nom.id AS id, no_accent, nom, value
                FROM nom LEFT JOIN attribut
                ON nom.attribut_id = attribut.id
            ");
        }else{
            $results = $mysqli->select($table_name, ["*"]);
        }

        if($results === FALSE){
            $alert->e("Erreur lors de l'affichage de la table $table_name");
            return;
        }

        if($table_name == "acte"){
            $page_title = "Table: Actes";
            return print_table_acte($results);
        }
        if($table_name == "personne"){
            $page_title = "Table: Personnes";
            return print_table_personne($results);
        }
        if($table_name == "acte_contenu"){
            $page_title = "Table: Contenu des actes";
            return print_table_acte_contenu($results);
        }
        if($table_name == "relation"){
            $page_title = "Table: Relations";
            return print_table_relation($results);
        }
        if($table_name == "condition"){
            $page_title = "Table: Conditions";
            return print_table_condition($results);
        }
        if($table_name == "source"){
            $page_title = "Table: Sources";
            return print_table_source($results);
        }
        if($table_name == "statut"){
            $page_title = "Table: Statuts";
            return print_table_status($results);
        }
        if($table_name == "attribut"){
            $page_title = "Table: Attributs";
            return print_table_attribut($results);
        }
        if($table_name == "prenom"){
            $page_title = "Table: Prenoms";
            return print_table_prenom($results);
        }
        if($table_name == "nom"){
            $page_title = "Table: Noms";
            return print_table_nom($results);
        }
    }

    function button_table($text, $nom_table){
        global $url_parsed;

        $class = "btn";
        if(isset($url_parsed["table"]) && $url_parsed["table"] === $nom_table)
            $class .= " btn-primary";
        else
            $class .= " btn-default";

        return "<a href='./table/$nom_table'><div class='$class'>$text</div></a>";
    }

    $tables_available = [
        "Actes" => "acte",
        "Contenu des actes" => "acte_contenu",
        "Personnes" => "personne",
        "Relations" => "relation",
        "Conditions" => "condition",
        "Sources" => "source",
        "Statuts" => "statut",
        "Prenoms" => "prenom",
        "Noms" => "nom",
        "Attributs" => "attribut"
    ];

    $html_table = "";
    if(isset($url_parsed["table"])){
        $html_table = print_table($url_parsed["table"]);
    }

?>

<div id="tables-buttons">
    <?php
        foreach ($tables_available as $key => $value) {
            echo button_table($key, $value);
        }
    ?>
</div>
<div id="table_container" class="table-responsive">
    <?php echo $html_table; ?>
</div>
