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
                $epoux_str = html_personne_link(personne_memory($row["epoux"]));

            $epouse_str = "";
            if(isset($row["epouse"]))
                $epouse_str = html_personne_link(personne_memory($row["epouse"]));

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
            $html_personne = html_personne_link(personne_memory($row["id"]));
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
            $rows .= html_relation($relation);
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
            $rows .= html_condition($condition, TRUE);
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

    function print_table($table_name){
        global $mysqli, $alert;

        $results = $mysqli->select($table_name, ["*"]);
        if($results === FALSE){
            $alert->e("Erreur lors de l'affichage de la table $table_name");
            return;
        }

        if($table_name == "acte")
            return print_table_acte($results);
        if($table_name == "personne")
            return print_table_personne($results);
        if($table_name == "acte_contenu")
            return print_table_acte_contenu($results);
        if($table_name == "relation")
            return print_table_relation($results);
        if($table_name == "condition")
            return print_table_condition($results);
        if($table_name == "source")
            return print_table_source($results);
    }

    // function add_link($table_name, $column_name, $value){
    //     $link = NULL;
    //     switch($table_name){
    //         case "acte":
    //             switch($column_name){
    //                 case "id":
    //                     $link = "./acte/$value";
    //                     break;
    //                 case "epoux":
    //                 case "epouse":
    //                     $link = "./personne/$value";
    //                     break;
    //             }
    //             break;
    //         case "acte_contenu":
    //             switch($column_name){
    //                 case "acte_id":
    //                     $link = "./acte/$value";
    //                     break;
    //             }
    //             break;
    //         case "personne":
    //             switch($column_name){
    //                 case "id":
    //                     $link = "./personne/$value";
    //                     break;
    //             }
    //             break;
    //         case "relation":
    //             switch($column_name){
    //                 case "pers_source_id":
    //                 case "pers_destination_id":
    //                     $link = "./personne/$value";
    //                     break;
    //             }
    //             break;
    //         case "condition":
    //             switch($column_name){
    //                 case "personne_id":
    //                     $link = "./personne/$value";
    //                     break;
    //             }
    //             break;
    //     }
    //
    //     if(isset($link))
    //         return "<a href='$link'>$value</a>";
    //     return $value;
    // }

    // function print_table($table_name){
    //     global $mysqli, $alert;
    //
    //     $rep = $mysqli->select($table_name, ["*"], "");
    //     if($rep === FALSE){
    //         $alert->e("Erreur lors de l'affichage de la table $table_name");
    //         return;
    //     }
    //
    //     echo "<table class='table table-striped table-hover'><thead><tr>";
    //
    //     $fields = $rep->fetch_fields();
    //     foreach ($fields as $val) {
    //         echo "<th>$val->name</th>";
    //     }
    //     echo "</tr></thead><tbody>";
    //
    //     while($row = $rep->fetch_row()){
    //         echo "<tr>";
    //         for($i = 0; $i < count($row); $i++){
    //             $value = htmlspecialchars($row[$i]);
    //             $value = add_link($table_name, $fields[$i]->name, $value);
    //             echo "<td>$value</td>";
    //         }
    //         echo "</tr>";
    //     }
    //
    //     echo "</tbody></tables>";
    // }

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

<div id="tables_buttons">
    <?php
        foreach ($tables_available as $key => $value) {
            echo button_table($key, $value);
        }
    ?>
</div>
<div id="table_container" class="table-responsive">
    <?php echo $html_table; ?>
</div>
