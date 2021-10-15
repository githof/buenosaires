<?php

include_once(ROOT."src/html_entities.php");

//  *** Affichages des contenus des tables 
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
    global $mysqli;

    $rows = "";
    while($row = $results->fetch_assoc()){
        $relation = new Relation();
        $relation->result_from_db($row);
        $mysqli->from_db_relation_list_actes($relation);

        $html_statut = html_relation_statut($relation->get_statut_name());
        $html_source = html_personne(personne_memory($relation->personne_source->id));
        $html_destination = html_personne(personne_memory($relation->personne_destination->id));
        $html_actes = html_list_actes($relation->actes);

        $rows .= "
            <tr>
                <th>$relation->id</th>
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
                    <th>ID</th>
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
    global $mysqli;

    $rows = "";
    while($row = $results->fetch_assoc()){
        $condition = new Condition();
        $condition->result_from_db($row);
        $mysqli->from_db_condition_list_actes($condition);

        $html_text = html_condition_text($condition->text);
        $html_personne = html_personne(personne_memory($condition->personne->id));
        $html_source = html_condition_source($condition->get_source_name());
        $html_actes = html_list_actes($condition->actes);

        $rows .= "
            <tr>
                <th>$condition->id</th>
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
                    <th>ID</th>
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
        $rows .= "
            <tr>
                <td>{$row["id"]}</td>
                <td>{$row["nom"]}</td>
                <td>{$row["no_accent"]}</td>
            </tr>";
    }
    return  "
        <table class='table table-striped table-hover'>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Sans accent</th>
                </tr>
            </thead>
            <tbody>
                $rows
            </tbody>
        </table>";
}

//  *** Affichage contenu de chaque table 
function print_table($table_name, $page, $nb){
    global $mysqli, $alert, $page_title;

    $str = "";

    $results = $mysqli->query("SELECT COUNT(*) as c FROM `$table_name`");
    if($results === FALSE){
        $alert->error("Erreur d'accès à la table $table_name");
        return;
    }

    $nb_rows = $results->fetch_assoc()["c"];
    $page_max = (int) ceil($nb_rows / $nb);

    $str = button_pages($table_name, $page, $page_max);

    $page--;
    if($page > $page_max)
        $page = $page_max;

    $row_offset = $page * $nb;
    $results = $mysqli->select($table_name, ["*"], "", "LIMIT $row_offset, $nb");

    if($results === FALSE){
        $alert->error("Erreur d'accès à la table $table_name");
        return;
    }

    //  *** rewrite-index 
    $page_title = "Table : ".ucfirst($table_name);
    $print_table_name = 'print_table_'.$table_name;
    $str = $print_table_name($results) . $str;


    return $str;
}


//  *** Boutons "page suviante / précédente 
function button_pages($table, $current_page, $max_page){
    $button_start = "
        <a class='btn btn-default table-nav-btn' href='$table'>
            <span class='glyphicon glyphicon-fast-backward' aria-hidden='true'></span>
        </a>"; 

    $button_end = "
        <a class='btn btn-default table-nav-btn' href='$table?page=$max_page'>
            <span class='glyphicon glyphicon-fast-forward' aria-hidden='true'></span>
        </a>"; 

    if($current_page < $max_page)
        $visible = "visible";
    else
        $visible = "hidden";
    $p = $current_page +1;
    $button_inc = "
        <a class='btn btn-default table-nav-btn' href='$table?page=$p' style='visibility:$visible'>
            $p
        </a>"; 

    if($current_page > 1)
        $visible = "visible";
    else
        $visible = "hidden";
    $p = $current_page -1;
    $button_dec = "
        <a class='btn btn-default table-nav-btn' href='$table?page=$p' style='visibility:$visible'>
            $p
        </a>"; 

    return "
        <div class='table-nav'>
            $button_start
            $button_dec
            $button_inc
            $button_end
        </div>";
}

//  ***  boutons affichage table 
function button_table($text, $nom_table){
    global $url_parsed;

    $class = "btn";
    if(isset($url_parsed["table"]) && $url_parsed["table"] === $nom_table) 
        $class .= " btn-primary";
    else
        $class .= " btn-default";

    //  *** rewrite-index 
    if(isset($url_parsed) && $url_parsed["table"] != NULL)
        return "<a href='./$nom_table'><div class='$class'>$text</div></a>"; 
    else 
        return "<a href='table/$nom_table'><div class='$class'>$text</div></a>";
}

$tables_available = array(
    "Actes" => "acte",
    "Contenu des actes" => "acte_contenu",
    "Personnes" => "personne",
    "Relations" => "relation",
    "Conditions" => "condition",
    "Sources" => "source",
    "Statuts" => "statut",
    "Prenoms" => "prenom",
    "Noms" => "nom"
);

$html_table = "";
$page = 1;
$nb = 25;
if(isset($url_parsed["table"])){
    if(isset($ARGS["page"]) && $ARGS["page"] > 0)
        $page = $ARGS["page"];
    $html_table = print_table($url_parsed["table"], $page, $nb);
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
