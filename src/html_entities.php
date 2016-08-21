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
        return "<div class='acte-contenu'>$acte_contenu</div>";
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

    function html_list_actes($actes){
        $html = "";
        foreach($actes as $acte)
            $html .= "<a href='acte/$acte->id'>[$acte->id]</a>";
        if(strlen($html) == 0)
            return "";
        return "<div class='list-acte'>actes: $html</div>";
    }

    function html_relation_statut($statut){
        return "<div class='relation-statut'>$statut</div>";
    }

    function html_condition_source($source){
        return "<div class='condition-source'>source: $source</div>";
    }

    function html_condition_text($text){
        return "<div class='condition-text'>$text</div>";
    }

    function html_relation($relation){
        $html_statut = html_relation_statut($relation->get_statut_name());
        $html_source = html_personne(personne_memory($relation->personne_source->id));
        $html_destination = html_personne(personne_memory($relation->personne_destination->id));
        $html_actes = html_list_actes($relation->get_actes());
        return "
            <div class='relation'>
                $html_source
                $html_statut
                $html_destination
                <div class='more'>$html_actes</div>
            </div>";
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
        $html_relations = "";
        foreach($all as $relation)
            $html_relations .= html_relation($relation);

        return "
            <div class='relations'>
                $html_relations
            </div>";
    }

    function html_personne_relation($personne, $statut_name, $actes){
        $html_personne = html_personne(personne_memory($personne->id));
        $html_statut = html_relation_statut($statut_name);
        $html_actes = html_list_actes($actes);

        return "
            <div class='relation'>
                $html_statut
                $html_personne
                <div class='more'>$html_actes</div>
            </div>";
    }

    function html_personne_relations($personne){
        $parents = [];
        $enfants = [];
        $mariage = [];
        $a_temoins = [];
        $est_temoins = [];
        $a_parrains = [];
        $est_parrains = [];

        foreach($personne->relations as $relation){
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

        return "<div class='relations'>$str</div>";
    }

    function html_condition($condition, $show_personne = TRUE){
        $html_text = html_condition_text($condition->text);
        $html_personne = ($show_personne)? html_personne(personne_memory($condition->personne->id)) : "";
        $html_source = html_condition_source($condition->get_source_name());
        $html_actes = html_list_actes($condition->get_actes());

        return "
            <div class='condition'>
                $html_text
                $html_personne
                <div class='more'>
                    $html_source
                    $html_actes
                </div>
            </div>";
    }

    function html_conditions($conditions, $show_personne = TRUE){
        $rows = "";
        foreach($conditions as $condition)
            $rows .= html_condition($condition, $show_personne);

        return "
            <div class='conditions'>$rows</div>";
    }

    function html_personne($personne, $with_url = TRUE, $with_id = TRUE, $id_first = FALSE){
        $html = "";
        foreach($personne->prenoms as $prenom)
            $html .= "<div class='prenom'>".$prenom->to_string()."</div>";

        $html_noms = "";
        foreach($personne->noms as $nom)
            $html .= "<div class='nom'>".$nom->to_string()."</div>";

        if($with_url)
            $html = "<a href='personne/$personne->id'>$html</a>";

        if($with_id){
            if($id_first)
                $html = "($personne->id) $html";
            else
                $html .= "($personne->id)";
        }

        return "<div class='personne'>$html</div>";
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
