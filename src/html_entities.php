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
            $obj->result_from_db($mysqli->from_db($obj));
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

        return "
        <tr>
            <td>$source</td>
            <td class='relation_statut'>$statut_name</td>
            <td>$destination</td>
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
                </tr>
            </thead>
            <tbody>
                $rows
            </tbody>
        </table>";
    }

    function html_personne_relation($personne, $statut_name){
        $personne = html_personne_link(personne_memory($personne->id));
        return "
        <div class='personne_relation'>
            <span class='personne_relation_statut'>$statut_name</span>
            <span class='personne_relation_personne'>$personne</span>
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
        $rows = "";
        foreach($mariage as $relation){
            if($personne->id == $relation->personne_destination->id)
                continue;
            $statut_name = ($relation->statut_id == STATUT_EPOUX)? "est marrié à" : "est marriée à";
            $rows .= html_personne_relation($relation->personne_destination, $statut_name);
        }
        if(strlen($rows) > 0)
            $str .= "<div class='personne_relations'>$rows</div>";

        $rows = "";
        foreach($parents as $relation){
            $statut_name = ($relation->statut_id == STATUT_PERE)? "a pour père" : "a pour mère";
            $rows .= html_personne_relation($relation->personne_source, $statut_name);
        }
        if(strlen($rows) > 0)
            $str .= "<div class='personne_relations'>$rows</div>";

        $rows = "";
        foreach($enfants as $relation){
            $statut_name = ($relation->statut_id == STATUT_PERE)? "est père de" : "est mère de";
            $rows .= html_personne_relation($relation->personne_destination, $statut_name);
        }
        if(strlen($rows) > 0)
            $str .= "<div class='personne_relations'>$rows</div>";

        $rows = "";
        foreach($est_temoins as $relation){
            $rows .= html_personne_relation($relation->personne_destination, "est témoin de");
        }
        if(strlen($rows) > 0)
            $str .= "<div class='personne_relations'>$rows</div>";

        $rows = "";
        foreach($a_temoins as $relation){
            $rows .= html_personne_relation($relation->personne_source, "a pour témoin");
        }
        if(strlen($rows) > 0)
            $str .= "<div class='personne_relations'>$rows</div>";

        $rows = "";
        foreach($est_parrains as $relation){
            $rows .= html_personne_relation($relation->personne_destination, "est parrain de");
        }
        if(strlen($rows) > 0)
            $str .= "<div class='personne_relations'>$rows</div>";

        $rows = "";
        foreach($a_parrains as $relation){
            $rows .= html_personne_relation($relation->personne_source, "a pour parrain");
        }
        if(strlen($rows) > 0)
            $str .= "<div class='personne_relations'>$rows</div>";

        return $str;
    }

    function html_condition($condition){
        $personne = html_personne_link(personne_memory($condition->personne->id));
        $source_name = $condition->get_source_name();
        return "
        <tr>
            <td>$condition->text</td>
            <td>$source_name</td>
        </tr>";
    }

    function html_conditions($conditions){
        $rows = "";
        foreach($conditions as $condition)
            $rows .= html_condition($condition);

        if(strlen($rows) == 0)
            return "";

        return  "
        <table class='table table-striped table-hover table-condensed'>
            <thead>
                <tr>
                    <th>Condition</th>
                    <th>Source</th>
                </tr>
            </thead>
            <tbody>
                $rows
            </tbody>
        </table>";
    }

    // function html_periode($periode){
    //     $d_m = $periode->values["debut_min"];
    //     $d_M = $periode->values["debut_max"];
    //     $f_m = $periode->values["fin_min"];
    //     $f_M = $periode->values["fin_max"];
    //
    //     return "
    //     <div class='periode'>
    //         <div class='periode_part'>
    //             <div class='periode_debut'>début</div>
    //             <div class='periode_min_max'>
    //                 <div class='periode_max'>
    //                     <div class='periode_max_title'>
    //                         max
    //                     </div>
    //                     <div class='periode_value'>
    //                         $d_M
    //                     </div>
    //                 </div>
    //                 <div class='periode_min'>
    //                     <div class='periode_max_title'>
    //                         min
    //                     </div>
    //                     <div class='periode_value'>
    //                         $d_m
    //                     </div>
    //                 </div>
    //             </div>
    //         </div>
    //         <div class='periode_part'>
    //             <div class='periode_min_max'>
    //                 <div class='periode_max'>
    //                     <div class='periode_value'>
    //                         $f_M
    //                     </div>
    //                     <div class='periode_max_title'>
    //                         max
    //                     </div>
    //                 </div>
    //                 <div class='periode_min'>
    //                     <div class='periode_value'>
    //                         $f_m
    //                     </div>
    //                     <div class='periode_max_title'>
    //                         min
    //                     </div>
    //                 </div>
    //             </div>
    //             <div class='periode_fin'>fin</div>
    //         </div>
    //     </div>
    //     ";
    // }

    function html_personne_link($personne){
        $full_name = html_personne_full_name($personne);
        return "
        <a class='personne_link' href='./personne/$personne->id'>
            $full_name
            ($personne->id)
        </a>";
    }

    function html_personne_full_name($personne){
        $str = "";
        foreach($personne->prenoms as $prenom)
            $str .= "<div class='personne_prenom'>$prenom->prenom</div>";

        foreach($personne->noms as $nom){
            $attr = "";
            if(isset($nom->attribut))
                $attr = $nom->attribut->value . " ";
            $str .="<div class='personne_nom'>$attr$nom->nom</div>";
        }
        return $str;
    }

?>
