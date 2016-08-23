<?php

    include_once(ROOT."src/html_entities.php");

    function search_actes(){
        global $mysqli, $ARGS;

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
            $noms = array_to_string_with_separator($noms_id, ", ");
            $select_relation_with_noms = "
                SELECT acte_has_relation.acte_id
                FROM relation INNER JOIN nom_personne AS nom_personne1
                ON relation.pers_source_id = nom_personne1.personne_id
                INNER JOIN nom_personne
                ON relation.pers_destination_id = nom_personne.personne_id
                INNER JOIN acte_has_relation
                ON relation.id = acte_has_relation.relation_id
                WHERE nom_personne1.nom_id IN ($noms)
                OR nom_personne.nom_id IN ($noms)
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

    function search_personnes(){
        global $mysqli, $ARGS;

        $date_start = (isset($ARGS["personne_date_start"]))? $ARGS["personne_date_start"] : NULL;
        $date_end = (isset($ARGS["personne_date_end"]))? $ARGS["personne_date_end"] : NULL;
        $noms_id = (isset($ARGS["personne_noms"]))? $ARGS["personne_noms"] : NULL;
        $prenoms_id = (isset($ARGS["personne_prenoms"]))? $ARGS["personne_prenoms"] : NULL;

        $where_noms = NULL;
        $where_prenoms = NULL;

        if(isset($noms_id) && count($noms_id) > 0){
            $noms = array_to_string_with_separator($noms_id, ", ");
            $where_noms = "
                SELECT personne_id
                FROM nom_personne
                WHERE nom_id IN ($noms)
            ";
        }

        if(isset($prenoms_id) && count($prenoms_id) > 0){
            $prenoms = array_to_string_with_separator($prenoms_id, ", ");
            $where_prenoms = "
                SELECT personne_id
                FROM prenom_personne
                WHERE prenom_id IN ($prenoms)
            ";
        }

        $where = "";
        if(isset($where_noms, $where_prenoms)){
            $where = "WHERE id IN ($where_noms) OR id IN ($where_prenoms)";
        }else if(isset($where_noms)){
            $where = "WHERE id IN ($where_noms)";
        }else if(isset($where_prenoms)){
            $where = "WHERE id IN ($where_prenoms)";
        }

        return $mysqli->query("
            SELECT id
            FROM personne
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
                $epoux_str = html_personne(personne_memory($row["epoux"]));

            $epouse_str = "";
            if(isset($row["epouse"]))
                $epouse_str = html_personne(personne_memory($row["epouse"]));

            $str .= "
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
                </thead>
                <tbody>
                    $str
                </tbody>
            </table>
        ";
    }

    function print_result_personnes($results){
        $str = "";
        while($row = $results->fetch_assoc()){
            $html_personne = html_personne(personne_memory($row["id"]));
            $str .= "<tr><td>$html_personne</td></tr>";
        }

        return "
            <table class='table table-striped table-hover'>
                <thead>
                    <tr><th>Personne</th></tr>
                </thead>
                <tbody>
                    $str
                </tbody>
            </table>
        ";
    }

    $html = "";

    if(isset($ARGS["type"]) && $ARGS["type"] == "acte"){
        $results = search_actes();
        if($results != FALSE)
            $html = print_result_actes($results);
    }else if(isset($ARGS["type"]) && $ARGS["type"] == "personne"){
        $results = search_personnes();
        if($results != FALSE)
            $html = print_result_personnes($results);
    }else{
        $html = "Formulaire de recherche incomplet";
    }

    echo $html;
?>
