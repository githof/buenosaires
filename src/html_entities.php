<?php


include_once(ROOT."src/class/model/Personne.php");
include_once(ROOT."src/class/model/Condition.php");
include_once(ROOT."src/class/model/Acte.php");
include_once(ROOT."src/class/model/Relation.php");

$memory = [
    "personne" => [],
    "acte" => [],
    "relation" => [],
    "condition" => []
];

function has_memory($class, $id){
    global $memory, $mysqli;

    if(!isset($class))
        return;

    if(isset($memory[$class][$id]))
        return $memory[$class][$id];
    else{
        switch($class){
            //  *** acte_memory() appelée nulle part 
            // case "acte":
            //     $obj = new Acte($id);
            //     break;
            /*  *** Pour l'instant new Personne n'est pas une $personne déjà créée 
            */
            case "personne": 
                $obj = new Personne($id);
                break;
            //  *** n'est pas utilisée 
            // case "relation":
            //     $obj = new Relation($id);
            //     break;
            //  *** n'est pas utilisée 
            // case "condition":
            //     $obj = new Condition($id);
            //     break;
        }
        // $mysqli->from_db($obj, TRUE);   
        $obj->from_db($obj, TRUE);   
        $memory[$class][$id] = $obj;
        return $obj;
    }
}

//  *** [tests-has-memory] décommenter si ça manque qqpart 
//  acte_memory() appelée nulle part 
// function acte_memory($id){
//     return has_memory("acte", $id);
// }

function personne_memory($id){
    return has_memory("personne", $id);
}

//  *** [tests-has-memory] décommenter si ça manque qqpart 
//  relation_memory() appelée nulle part 
// function relation_memory($id){
//     return has_memory("Relation", $id);
// }

//  *** [tests-has-memory] décommenter si ça manque qqpart 
//  condition_memory() appelée nulle part 
// function condition_memory($id){
//     return has_memory("condition", $id);
// }


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
    //  *** tests-has-memory
    /*  ici $actes est un array de strings, pas un objet : 
        J'ai modifié Database::from_db_condition|relation_list_acte()
        pour ne pas re-créer d'objet, comme c'était le cas avant.
    */
    // foreach($actes as $acte) {
    foreach($actes as $acte_id) {
        // $html .= "<a href='acte/$acte->id'>[<span class='acte-ref'>$acte->id</span>]</a>";
        $html .= "<a href='acte/$acte_id'>[<span class='acte-ref'>$acte_id</span>]</a>";
    }
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

function html_relation($relation, $show_actes = TRUE){
    $html_statut = html_relation_statut($relation->get_statut_name());
    $html_source = html_personne(personne_memory($relation->personne_source->id));
    $html_destination = html_personne(personne_memory($relation->personne_destination->id));
    $html_actes = ($show_actes)?
        html_list_actes($relation->actes) :
        "";
    return "
        <div class='relation' id='relation-$relation->id'>
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

// Là on va réordonner la liste de relations pour l'affichage
// (enfin je crois que c'est ça...)
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

//  *** tests-has-memory
/*  *** personne_memory() : pas de re-création d'une personne déjà existante :
        ==> Ici on a (par ex.) la personne source avec ses infos, 
        on crée une new Personne($id) (has_memory()) pour récupérer les infos de la pers_destination, 
        from_db_personne_relations() crée une nouvelle personne pour la pers_destination.
*/
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

// Y'a du boulot de factorisation à faire ici :)
//  *** test-has-memory 
//  *** ici $personne est la personne appelée dans l'url 
function html_personne_relations($personne){
    $rel_btype = $personne->get_relations_by_type();
    //  *** test rewrite-requete
    // echo '<br>'.__METHOD__.' $rel_btype["mariage"] : ';
    // var_dump($rel_btype["mariage"]);
    //  fin test
    $str = "";
    foreach($rel_btype['mariage'] as $relation){
        $statut_name = "est mariée à";
        $pers = $relation->personne_destination;
        if($relation->personne_destination->id == $personne->id){
            $statut_name = "est marié à";
            $pers = $relation->personne_source;
        }
        $str .= html_personne_relation($pers,
                    $statut_name,
                    $relation->actes);
    }

    foreach($rel_btype['parents'] as $relation){
        $statut_name = ($relation->statut_id == STATUT_PERE)?
        "a pour père" :
        "a pour mère";
        $str .= html_personne_relation($relation->personne_source,
                    $statut_name,
                    $relation->actes);
    }

    foreach($rel_btype['enfants'] as $relation){
        $statut_name = ($relation->statut_id == STATUT_PERE)?
        "est père de" :
        "est mère de";
        $str .= html_personne_relation($relation->personne_destination,
                    $statut_name,
                    $relation->actes);
    }

    foreach($rel_btype['est_temoin'] as $relation){
        $str .= html_personne_relation($relation->personne_destination,
                    "est témoin de",
                    $relation->actes);
    }

    foreach($rel_btype['a_temoins'] as $relation){
        $str .= html_personne_relation($relation->personne_source,
                    "a pour témoin",
                    $relation->actes);
    }

    foreach($rel_btype['est_parrain'] as $relation){
        $str .= html_personne_relation($relation->personne_destination,
                    "est parrain de",
                    $relation->actes);
    }

    foreach($rel_btype['a_parrains'] as $relation){
        $str .= html_personne_relation($relation->personne_source,
                    "a pour parrain",
                    $relation->actes);
    }

    return "<div class='relations'>$str</div>";
}


//  *** tests-has-memory
/*  Pour l'instant on ne peut pas contourner personne_memory, 
    on en a besoin pour afficher ses infos, 
    et le code ne les récupèrera que pour la section Relations.
    Il faudrait récupérer les infos de la personne en cours avant de les afficher 
    (utiliser l'objet comme il est prévu pour fonctionner, en fait)
*/
function html_condition($condition, $show_personne = TRUE, $show_actes = TRUE){
    $html_text = html_condition_text($condition->text);
    $html_personne = ($show_personne)?
        //  *** tests-has-memory 210531 
        html_personne(personne_memory($condition->personne->id)) :
        // html_personne($condition->personne->id) :
        "";
    $html_source = html_condition_source($condition->get_source_name());
    $html_actes = ($show_actes)?
        html_list_actes($condition->actes) :
        "";

    return "
        <div class='condition' id='condition-$condition->id'>
            $html_text
            $html_personne
            <div class='more'>
                $html_source
                $html_actes
            </div>
        </div>";
}

function html_conditions($condition, $show_personne = TRUE){ 
    $rows = "";
    foreach($condition as $condition)
        $rows .= html_condition($condition, $show_personne);

    return "
        <div class='conditions'>$rows</div>";
}

function html_personne($personne,
                        $with_url = TRUE, 
                        $with_id = TRUE, 
                        $id_first = FALSE){

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

function html_personne_periode($personne_id){
    global $mysqli;
    $date_max = null;
    $date_min = null;

    $result = $mysqli->query("
        SELECT date_start, date_end
        FROM acte
        WHERE acte.id IN (
            SELECT acte_id
            FROM relation INNER JOIN acte_has_relation
            ON relation.id = acte_has_relation.relation_id
            WHERE pers_source_id = $personne_id
            OR pers_destination_id = $personne_id
        ) OR acte.id IN (
            SELECT acte_id
            FROM `condition` INNER JOIN acte_has_condition
            ON `condition`.id = acte_has_condition.condition_id
            WHERE personne_id = $personne_id
        ) OR epoux = $personne_id
        OR epouse = $personne_id");

    if($result != FALSE && $result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            $date_start = new DateTime($row["date_start"]);
            $date_end = new DateTime($row["date_end"]);

            if($date_max == null || $date_end > $date_max)
                $date_max = $date_end;

            if($date_min == null || $date_start < $date_min)
                $date_min = $date_start;
        }
    }

    if($date_min == null || $date_max == null)
        return "Aucune période trouvée";

    $date_max_s = $date_max->format("Y-m-d");
    $date_min_s = $date_min->format("Y-m-d");

    $interval = $date_max->diff($date_min);
    $interval_s = $interval->format("%d jours");
    if($interval->m > 0)
        $interval_s = $interval->format("%m mois, ") . $interval_s;
    if($interval->y > 0)
        $interval_s = $interval->format("%Y ans, ") . $interval_s;

    return "<div class='personne-periode'>
                $interval_s (de $date_min_s à $date_max_s)
            </div>";
}

//  sert dans import.php (+ header.php (new_account.php) search.php)
/*  Sans Bootstrap si y a des problèmes :
  <div class="form-group ">
        ' . "$contents" . '
        </div>
*/
function html_form_group($contents) {
  return '
        <div class="row">
            <div class="form-group col-md-12">
                ' . "$contents" . '
            </div>
        </div>
  ';
}

//  sert dans import.php (+ header.php, detail_acte.php, search.php)
function html_hidden_type($name, $value) {
    return '
        <input type="hidden"'
        . ' name="' . $name . '" value="' . $value . '" />';
}

/*
    sert dans import.php, header.php, dissocier.php, new_account.php,
    search.php
*/
function html_submit($class, $message) {
    $button = '<button type="submit"'
      . ' class="' . $class 
      . ' btn btn-primary">' . $message . '</button>';
    return html_form_group($button);
}

//  *** Tabs pour search.php et export.php // 
//  onglets "Actes" et "Personnes"
function html_tab_title($href, $class, $label) {

    return '<li role="presentation" class="' . $class . '">
                <a href="' . $href . '" aria-controls="' . $href . '" role="tab" data-toggle="tab">' . $label . '</a>
            </li>';
                
}


?>
