<?php

include_once(ROOT."src/class/model/Acte.php");

//  *** Déplacer ça dans Prenom et Nom aussi ? cf comm plus bas // 
function array_to_string($array, $separator){
    $str = "";
    $i = 0;
    $length = count($array);
    foreach($array as $item){
        $str .= $item;
        if($i < $length -1)
            $str .= $separator;
        $i++;
    }
    return $str;
}

class CSVExport {

    public static $CSV_SEPARATOR = ";";
    public static $personnes;

    public function __construct(){

    }

    //  PRIVATE METHODS //

    private static function export_line($line) {
        $first = TRUE;

        foreach($line as $field) {
            if($first)
                $first = FALSE;
            else
                echo self::$CSV_SEPARATOR;

            echo $field;
        }
        echo PHP_EOL;
    }

    //  PUBLIC //

    public static function export_personnes(){
        global $mysqli;

        self::entete();

        self::export_line(array("id","noms","prenoms"));

        $personnes = $mysqli->get_personnes(FALSE);

        foreach($personnes as $id => $personne) {
            /*
            bricolage sur les tableaux de noms et prénoms
            ça pourrait être un utilitaire des classes Nom et
            Prenom,
            qui d'ailleurs pourraient hériter d'une même classe
            */
            $prenoms = [];
            foreach($personne->prenoms as $prenom)
                $prenoms[] = $prenom->to_string();

            $noms = [];
            foreach($personne->noms as $nom)
                $noms[] = $nom->to_string();

            $prenoms = array_to_string($prenoms, " ");
            $noms = array_to_string($noms, " ");

            self::export_line(array($id, $noms, $prenoms));
        }
    }

    //  PRIVATE METHODS //

    private static function add_personne_to_line(&$line, $p, $names = FALSE) {
        if($p instanceof Personne) {
            $line[] = $p->id;
            if($names) {
                $personne = self::$personnes[$p->id];
                $line[] = $personne->prenoms_str;
                $line[] = $personne->noms_str;
            }
        } elseif(is_string($p)) {
            $line[] = $p."_id";

            if($names) {
                $line[] = $p."_prenoms";
                $line[] = $p."_noms";
            }
        }
    }

    private static function add_date(&$line, $relation) {
        $date = $relation->get_date();
        $line[] = "$date";
    }

    private static function export_relation($relation, $start, $end, $names, $dates, $reverse) {       // 
        $line = [];
        $line[] = $reverse ? -$relation->id : $relation->id;  

        //  *** pour pouvoir exporter une fraction des relations : 
        // if((isset($relation->id)) && ($relation->id >= $start) && ($relation->id <= $end)) {
            if($reverse){
                self::add_personne_to_line($line,
                    $relation->personne_destination,
                    $names);
                self::add_personne_to_line($line,
                    $relation->personne_source,
                    $names);
            } else {
                self::add_personne_to_line($line,
                    $relation->personne_source,
                    $names);
                self::add_personne_to_line($line,
                    $relation->personne_destination,
                    $names);
            }
            $line[] = $relation->get_statut_name();
            if($dates)
                self::add_date($line, $relation);

            self::export_line($line);
        // }
    }

    //  PUBLIC  //

    public static function export_relations($start, $end, $names = FALSE, $dates = FALSE) {   //  
        global $mysqli;

        self::entete();

        $line = [];
        $line[] = "id";

        self::add_personne_to_line($line, "src", $names);
        self::add_personne_to_line($line, "dest", $names);
        $line[] = "statut";
        if($dates)
            $line[] = "date";

        self::export_line($line);

        self::$personnes = $mysqli->get_personnes(FALSE);

        // faire un Database->get_relations() comme get_personnes ? 
        $results = $mysqli->select("relation", ["*"]);
        if($results != FALSE && $results->num_rows){
            while($row = $results->fetch_assoc()){
                $relation = new Relation();
                $relation->result_from_db($row);

                //  *** par défaut relations dans les 2 sens 
                self::export_relation(
                        $relation, 
                        $start, 
                        $end,
                        $names, 
                        $dates, 
                        FALSE);     //   !reverse 
                self::export_relation(
                        $relation, 
                        $start, 
                        $end, 
                        $names, 
                        $dates, 
                        TRUE);      //  reverse 
            }
        }
    }

    public static function entete(){
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
    }
}


?>
