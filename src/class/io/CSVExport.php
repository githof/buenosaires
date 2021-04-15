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

    //  *** Pour stocker le chemin vers le fichier à créer sur le disque 
    public static $out; 

    public function __construct(){

    }

    //  PRIVATE METHODS //

    private static function export_line($line) {

        //  *** fichier à enregistrer sur le disque 
        self::$out = fopen(ROOT.'exports/export_'.time().'.csv', 'a');

        //  *** timeline
        fputcsv(self::$out, array(time()));

        echo '<br>'.__METHOD__;
        echo '<br>time() : ';
        var_dump(time());
        //  fin timeline 

        $first = TRUE;

        foreach($line as $field) {
            if($first)
                $first = FALSE;
            else
                echo self::$CSV_SEPARATOR;

            echo $field;
        }
        echo PHP_EOL;

        fputcsv(self::$out, $line);

        // echo '<br>'.__METHOD__;
        // echo '<br>$line : ';
        // var_dump($line);
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
        //  *** test no_accent /
        // $array_names = array($id, $noms, $prenoms);
        // return $array_names;
    }

    //  PRIVATE METHODS //

    private static function add_personne_to_line(&$line, $p, $names = FALSE) {

        if($p instanceof Personne) {
            $line[] = $p->id;
            if($names) {
                $personne = self::$personnes[$p->id];   

                $line[] = $personne->prenoms_str;
                $line[] = $personne->noms_str;
                // echo '<br>'.__METHOD__;
                // echo '<br>$personne->noms_str : ';
                // var_dump($personne->noms_str);
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

    private static function export_relation($relation, $start, $end, $names, $dates, $reverse) { 
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

    public static function export_relations($start, $end, $names = FALSE, $dates = FALSE, $deux_sens = FALSE) { 
        global $mysqli, $line;  //   retirer $line ? 

        self::entete();
        // echo '<br>'.__METHOD__;
        // echo '<br>$deux_sens : ';
        // var_dump($deux_sens);

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

                //  *** par défaut relations dans 1 seul sens 
                if(!$deux_sens) {
                    self::export_relation(
                        $relation, 
                        $start, 
                        $end,
                        $names, 
                        $dates, 
                        FALSE);     //   !reverse 
                } else {
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

        //  timeline 
        fputcsv(self::$out, array(time()));

        echo '<br>'.__METHOD__;
        echo '<br>time() : ';
        var_dump(time());
        //  fin timeline 

        fclose(self::$out);
    }

    //  *** test enregistrer fichier csv sur le serveur 
    // public static function enregistrer_csv($array) {

    //     // $out = fopen('php://export.csv', 'a');
    //     $out = fopen(ROOT."export.csv", 'w');

    //     if ($out) 
    //         // fputcsv($out, array('this','is some', 'csv "stuff", you know.'));
    //         fputcsv($out, $array);
    //     else 
    //         var_dump($out);

    //     // $out = fopen('php://output', 'w');
    //     // fputcsv($out, array('this','is some', 'csv "stuff", you know.'));
    //     fclose($out);

    // }

    public static function entete(){
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="export.csv"');
    }
}


?>
