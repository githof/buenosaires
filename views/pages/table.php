<?php


    function add_link($table_name, $column_name, $value){
        $link = NULL;
        switch($table_name){
            case "acte":
                switch($column_name){
                    case "id":
                        $link = "./acte/$value";
                        break;
                    case "epoux":
                    case "epouse":
                        $link = "./personne/$value";
                        break;
                }
                break;
            case "acte_contenu":
                switch($column_name){
                    case "acte_id":
                        $link = "./acte/$value";
                        break;
                }
                break;
            case "personne":
                switch($column_name){
                    case "id":
                        $link = "./personne/$value";
                        break;
                }
                break;
            case "relation":
                switch($column_name){
                    case "pers_source_id":
                    case "pers_destination_id":
                        $link = "./personne/$value";
                        break;
                }
                break;
            case "condition":
                switch($column_name){
                    case "personne_id":
                        $link = "./personne/$value";
                        break;
                }
                break;
        }

        if(isset($link))
            return "<a href='$link'>$value</a>";
        return $value;
    }

    function print_table($table_name){
        global $mysqli, $alert;

        $rep = $mysqli->select($table_name, ["*"], "");
        if($rep === FALSE){
            $alert->e("Erreur lors de l'affichage de la table $table_name");
            return;
        }

        echo "<table class='table table-striped table-hover'><thead><tr>";

        $fields = $rep->fetch_fields();
        foreach ($fields as $val) {
            echo "<th>$val->name</th>";
        }
        echo "</tr></thead><tbody>";

        while($row = $rep->fetch_row()){
            echo "<tr>";
            for($i = 0; $i < count($row); $i++){
                $value = htmlspecialchars($row[$i]);
                $value = add_link($table_name, $fields[$i]->name, $value);
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }

        echo "</tbody></tables>";
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

?>

<div id="tables_buttons">
    <?php
        foreach ($tables_available as $key => $value) {
            echo button_table($key, $value);
        }
    ?>
</div>
<div id="table_container" class="table-responsive">
    <?php
        if(isset($url_parsed["table"])){
            print_table($url_parsed["table"]);
        }
    ?>
</div>
