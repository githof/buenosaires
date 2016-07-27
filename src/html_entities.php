<?php

    include_once(ROOT."src/database/Personne.php");
    include_once(ROOT."src/database/Periode.php");
    include_once(ROOT."src/database/Condition.php");
    include_once(ROOT."src/database/Acte.php");
    include_once(ROOT."src/database/Relation.php");

    $memory = [
        "personne" => [],
        "acte" => [],
        "relation" => [],
        "condition" => [],
        "periode" => []
    ];

    function has_memory($class, $id){
        global $memory;

        if(!isset($class))
            return;

        if(isset($memory[$class][$id]))
            return $memory[$class][$id];
        else{
            switch($class){
                case "acte":
                    $obj = new Acte($id);
                    break;
                case "personne":
                    $obj = new Personne($id);
                    break;
                case "relation":
                    $obj = new Relation($id);
                    break;
                case "condition":
                    $obj = new Condition($id);
                    break;
                case "periode":
                    $obj = new Periode($id);
                    break;
            }
            $memory[$class][$id] = $obj;
            return $obj;
        }
    }

    function acte_memory($id){
        return has_memory("acte", $id);
    }

    function personne_memory($id){
        return has_memory("personne", $id);
    }

    function relation_memory($id){
        return has_memory("Relation", $id);
    }

    function condition_memory($id){
        return has_memory("condition", $id);
    }

    function periode_memory($id){
        return has_memory("periode", $id);
    }

    function html_acte_contenu($acte_contenu){
        return $acte_contenu;
    }


    function html_acte_small($acte){
        $periode = html_periode(periode_memory($acte->values["periode_id"]));

        return "
        <div class='acte_small'>
            <div class='acte_small_id'>
                <a href='./acte/$acte->id'>$acte->id</a>
            </div>
        </div>";
    }

    function html_relation($relation){
        $statut_name = $relation->get_statut_name();
        $source = html_personne_small(personne_memory($relation->values["source"]));
        $destination = html_personne_small(personne_memory($relation->values["destination"]));
        $periode = html_periode(periode_memory($relation->values["periode_id"]));

        return "
        <tr>
            <td>$source</td>
            <td class='relation_statut'>$statut_name</td>
            <td>$destination</td>
            <td>$periode</td>
        </tr>";
    }

    function html_relations($relations){
        $rows = "";
        foreach($relations as $relation)
            $rows .= html_relation($relation);

        if(strlen($rows) == 0)
            return "";

        return  "
        <table class='table table-bordered table-striped table-hover'>
            <thead>
                <tr>
                    <th>Personne source</th>
                    <th>Statut</th>
                    <th>Personne destination</th>
                    <th>Période</th>
                </tr>
            </thead>
            <tbody>
                $rows
            </tbody>
        </table>";
    }

    function html_condition($condition){
        $personne = html_personne_small(personne_memory($condition->values["personne_id"]));
        $acte = html_acte_small(acte_memory($condition->values["acte_id"]));
        $source_name = $condition->get_source_name();
        return "
        <tr>
            <td>$personne</td>
            <td>{$condition->values["text"]}</td>
            <td>$source_name</td>
            <td>$acte</td>
        </tr>";
    }

    function html_conditions($conditions){
        $rows = "";
        foreach($conditions as $condition)
            $rows .= html_condition($condition);

        if(strlen($rows) == 0)
            return "";

        return  "
        <table class='table table-striped table-hover'>
            <thead>
                <tr>
                    <th>Personne</th>
                    <th>Condition</th>
                    <th>Source</th>
                    <th>Acte</th>
                </tr>
            </thead>
            <tbody>
                $rows
            </tbody>
        </table>";
    }

    function html_periode($periode){
        $d_m = $periode->values["debut_min"];
        $d_M = $periode->values["debut_max"];
        $f_m = $periode->values["fin_min"];
        $f_M = $periode->values["fin_max"];

        return "
        <div class='periode'>
            <div class='periode_part'>
                <div class='periode_debut'>début</div>
                <div class='periode_min_max'>
                    <div class='periode_max'>
                        <div class='periode_max_title'>
                            max
                        </div>
                        <div class='periode_value'>
                            $d_M
                        </div>
                    </div>
                    <div class='periode_min'>
                        <div class='periode_max_title'>
                            min
                        </div>
                        <div class='periode_value'>
                            $d_m
                        </div>
                    </div>
                </div>
            </div>
            <div class='periode_part'>
                <div class='periode_min_max'>
                    <div class='periode_max'>
                        <div class='periode_value'>
                            $f_M
                        </div>
                        <div class='periode_max_title'>
                            max
                        </div>
                    </div>
                    <div class='periode_min'>
                        <div class='periode_value'>
                            $f_m
                        </div>
                        <div class='periode_max_title'>
                            min
                        </div>
                    </div>
                </div>
                <div class='periode_fin'>fin</div>
            </div>
        </div>
        ";
    }

    function html_personne_small($personne){
        $prenoms = html_personne_prenoms($personne);
        $noms = html_personne_noms($personne);
        return "
        <div class='personne_small'>
            <a href='./personne/$personne->id'>
                $prenoms
                $noms
                ($personne->id)
            </a>
        </div>";
    }

    function html_personne_prenoms($personne){
        $str = "";
        $prenoms = $personne->get_prenoms();
        foreach($prenoms as $prenom){
            $str .= "<div class='personne_prenom'>$prenom</div>";
        }
        return $str;
    }

    function html_personne_noms($personne){
        $str = "";
        $noms = $personne->get_noms();
        foreach($noms as $nom){
            $str .="<div class='personne_nom'>$nom</div>";
        }
        return $str;
    }

?>
