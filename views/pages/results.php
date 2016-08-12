<?php

    include_once(ROOT."src/html_entities.php");

    function search_actes(){
        global $mysqli, $ARGS;
        var_dump($ARGS);

        $date_start = (isset($ARGS["acte_date_start"]))? $ARGS["acte_date_start"] : NULL;
        $date_end = (isset($ARGS["acte_date_end"]))? $ARGS["acte_date_end"] : NULL;
        $noms_id = (isset($ARGS["acte_noms"]))? $ARGS["acte_noms"] : NULL;

        $where = "";
        if(isset($date_start) && strlen($date_start) > 0)
            $where .= " date_start >= '$date_start'";
        if(isset($date_end) && strlen($date_end) > 0){
            if(strlen($where) > 0)
                $where .= " AND ";
            $where .= " date_end <= '$date_end'";
        }

        $select_relation_with_noms = "";
        if(isset($noms_id) && count($noms_id) > 0){
            $names = "";
            $i = 0;
            foreach($noms_id as $nom_id){
                $names .= "'".$mysqli->real_escape_string($nom_id)."'";
                if($i < count($noms_id)-1)
                    $names .= ", ";
                $i++;
            }

            $select_relation_with_noms = "
                SELECT acte_has_relation.acte_id
                FROM relation INNER JOIN nom_personne AS nom_personne1
                ON relation.pers_source_id = nom_personne1.personne_id
                INNER JOIN nom_personne
                ON relation.pers_destination_id = nom_personne.personne_id
                INNER JOIN acte_has_relation
                ON relation.id = acte_has_relation.relation_id
                WHERE nom_personne1.nom_id IN ($names) OR nom_personne.nom_id IN ($names)
            ";
        }

        if(strlen($select_relation_with_noms) > 0){
            if(strlen($where) > 0)
                $where .= " AND ";
            $where .= " id IN ($select_relation_with_noms)";
        }

        if(strlen($where) > 0)
            $where  = "WHERE $where ";

        return $mysqli->query("
            SELECT *
            FROM acte
            $where
        ");
    }

    function print_result_actes($results){
        $str = "";
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

            $str .= "
                <tr>
                    <td><a href='acte/{$row["id"]}'>{$row["id"]}</a></td>
                    <td>$epoux_str</td>
                    <td>$epouse_str</td>
                    <td>$date_str</td>
                </tr>";
        }

        return "
            <table class='table table-striped table-hover'
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Epoux</th>
                        <th>Epouse</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    $str
                </tbody>
            </table>
        ";
    }


    $ARGS = [];
    $args = explode("&", $url_parsed["args"]);
    foreach($args as $arg){
        $split = explode("=", $arg);
        if(endsWith($split[0], "[]")){
            $key = substr($split[0],0, strlen($split[0]) -2);
            if(!isset($ARGS[$key]))
                $ARGS[$key] = [];
            if(strlen($split[1]) > 0)
                $ARGS[$key][] = $mysqli->real_escape_string($split[1]);
        }else if(strlen($split[1]) > 0)
            $ARGS[$split[0]] = $mysqli->real_escape_string($split[1]);
    }

    $html = "";

    if(isset($ARGS["type"]) && $ARGS["type"] == "acte"){
        $results = search_actes();
        if($results != FALSE)
            $html = print_result_actes($results);
    }else if(isset($ARGS["type"]) && $ARGS["type"] == "personne"){

    }else{
        $html = "Formulaire de recherche incomplet";
    }

    echo $html;
?>
