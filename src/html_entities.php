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
        global $memory, $mysqli;

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
            $mysqli->from_db($obj, TRUE);
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
        $source = html_personne_link(personne_memory($relation->personne_source->id));
        $destination = html_personne_link(personne_memory($relation->personne_destination->id));
        $actes_html = "";
        foreach($relation->get_actes() as $acte)
            $actes_html .= " <a href='acte/$acte->id'>[$acte->id]</a>";
        return "
        <tr>
            <td>$source</td>
            <td class='relation_statut'>$statut_name de</td>
            <td>$destination</td>
            <td>$actes_html</td>
        </tr>";
    }

    function html_relations($relations){
        $mariage = [];
        $famille = [];
        $temoins = [];
        $parrains = [];
        $all = [];

        foreach($relations as $relation){
            switch($relation->statut_id){
                case STATUT_EPOUX:
                case STATUT_EPOUSE:
                    $mariage[] = $relation;
                    break;
                case STATUT_PERE:
                case STATUT_MERE:
                    $famille[] = $relation;
                    break;
                case STATUT_TEMOIN:
                    $temoins[] = $relation;
                    break;
                case STATUT_PARRAIN:
                    $parrains[] = $relation;
                break;
            }
        }
        $all = array_merge($all, $mariage, $famille, $temoins, $parrains);
        $rows = "";
        foreach($all as $relation)
            $rows .= html_relation($relation);

        if(strlen($rows) == 0)
            return "";

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

    function html_personne_relation($personne, $statut_name, $actes){
        $personne = html_personne_link(personne_memory($personne->id));
        $actes_html = "";
        foreach($actes as $acte)
            $actes_html .= " <a href='acte/$acte->id'>[$acte->id]</a>";
        return "
        <tr>
            <td><span class='personne_relation_statut'>$statut_name</span></td>
            <td><span class='personne_relation_personne'>$personne</span></td>
            <td>$actes_html</td>
        </tr>";
    }

    function html_personne_relations($personne){
        $parents = [];
        $enfants = [];
        $mariage = [];
        $a_temoins = [];
        $est_temoins = [];
        $a_parrains = [];
        $est_parrains = [];

        $relations = $personne->get_relations();

        foreach($relations as $relation){
            $is_source = $personne->id == $relation->personne_source->id;
            switch($relation->statut_id){
                case STATUT_PERE:
                case STATUT_MERE:
                    if($is_source)
                        $enfants[] = $relation;
                    else
                        $parents[] = $relation;
                    break;
                case STATUT_EPOUX:
                case STATUT_EPOUSE:
                    $mariage[] = $relation;
                    break;
                case STATUT_TEMOIN:
                    if($is_source)
                        $est_temoins[] = $relation;
                    else
                        $a_temoins[] = $relation;
                    break;
                case STATUT_PARRAIN:
                    if($is_source)
                        $est_parrains[] = $relation;
                    else
                        $a_parrains[] = $relation;
                    break;
            }
        }

        $str = "";
        foreach($mariage as $relation){
            if($personne->id == $relation->personne_destination->id)
                continue;
            $statut_name = ($relation->statut_id == STATUT_EPOUX)? "est marrié à" : "est marriée à";
            $str .= html_personne_relation($relation->personne_destination, $statut_name, $relation->get_actes());
        }

        foreach($parents as $relation){
            $statut_name = ($relation->statut_id == STATUT_PERE)? "a pour père" : "a pour mère";
            $str .= html_personne_relation($relation->personne_source, $statut_name, $relation->get_actes());
        }

        foreach($enfants as $relation){
            $statut_name = ($relation->statut_id == STATUT_PERE)? "est père de" : "est mère de";
            $str .= html_personne_relation($relation->personne_destination, $statut_name, $relation->get_actes());
        }

        foreach($est_temoins as $relation){
            $str .= html_personne_relation($relation->personne_destination, "est témoin de", $relation->get_actes());
        }

        foreach($a_temoins as $relation){
            $str .= html_personne_relation($relation->personne_source, "a pour témoin", $relation->get_actes());
        }

        foreach($est_parrains as $relation){
            $str .= html_personne_relation($relation->personne_destination, "est parrain de", $relation->get_actes());
        }

        foreach($a_parrains as $relation){
            $str .= html_personne_relation($relation->personne_source, "a pour parrain", $relation->get_actes());
        }

        return "
        <table class='table table-hover table-condensed'>
            <thead>
                <tr>
                    <th>Statut</th>
                    <th>Personne</th>
                    <th>Actes</th>
                </tr>
            </thead>
            <tbody>
                $str
            </tbody>
        </table>";
    }

    function html_condition($condition, $show_personne = FALSE){
        $personne = "";
        $source_name = $condition->get_source_name();
        $actes_html = "";
        foreach($condition->get_actes() as $acte)
            $actes_html .= " <a href='acte/$acte->id'>[$acte->id]</a>";

        if($show_personne){
            $personne = html_personne_link(personne_memory($condition->personne->id));
            $personne = "<td>$personne</td>";
        }

        return "
        <tr>
            <td>$condition->text</td>
            $personne
            <td>$source_name</td>
            <td>$actes_html</td>
        </tr>";
    }

    function html_conditions($conditions, $show_personne = FALSE){
        $personne_column = "";
        $rows = "";
        foreach($conditions as $condition)
            $rows .= html_condition($condition, $show_personne);

        if(strlen($rows) == 0)
            return "";

        if($show_personne)
            $personne_column = "<th>Personne</th>";

        return  "
        <table class='table table-striped table-hover table-condensed'>
            <thead>
                <tr>
                    <th>Condition</th>
                    $personne_column
                    <th>Source</th>
                    <th>Actes</th>
                </tr>
            </thead>
            <tbody>
                $rows
            </tbody>
        </table>";
    }

    function html_personne_link($personne){
        $full_name = html_personne_full_name($personne, TRUE);
        return "
        <a class='personne_link' href='./personne/$personne->id'>
            $full_name
        </a>";
    }

    function html_personne_full_name($personne, $with_id = FALSE){
        $str = "";
        foreach($personne->prenoms as $prenom)
            $str .= "<div class='personne_prenom'>$prenom->prenom</div>";

        foreach($personne->noms as $nom){
            $attr = "";
            if(isset($nom->attribut))
                $attr = $nom->attribut->value . " ";
            $str .="<div class='personne_nom'>$attr$nom->nom</div>";
        }

        if($with_id)
            $str .= "($personne->id)";

        return $str;
    }

    function html_date($date_start, $date_end){
        $str = "";
        if($date_start == $date_end)
            $str = "$date_start";
        else
            $str = "$date_start / $date_end";
        return "<div class='date'>$str</div>";
    }

?>
