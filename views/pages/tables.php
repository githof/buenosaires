<?php

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
            foreach($row as $col){
                $c = safe($col);
                echo "<td>$c</td>";
            }
            echo "</tr>";
        }

        echo "</tbody></tables>";
    }

    function button_table($text, $nom_table){
        $class = "btn";
        if(isset($_GET["t"]) && $_GET["t"] === $nom_table)
            $class .= " btn-primary";
        else
            $class .= " btn-default";

        return "<a href='./?p=tables&t=$nom_table'><div class='$class'>$text</div></a>";
    }

    $tables_available = [
        "Actes" => "acte",
        "Contenu des actes" => "acte_contenu",
        "Personnes" => "personne",
        "Relations" => "relation",
        "Conditions" => "cond",
        "Sources" => "source",
        "Statuts" => "statut",
        "Prenoms" => "prenom",
        "Noms" => "nom",
        "Periodes" => "periode",
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
        if(isset($_GET["t"])){
            print_table($_GET["t"]);
        }
    ?>
</div>
