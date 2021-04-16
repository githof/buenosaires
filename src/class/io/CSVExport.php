<?php

include_once(ROOT."src/class/io/ExportInterface.php");
include_once(ROOT."src/class/model/Acte.php");

//  *** Déplacer ça dans utils.php ? cf comm plus bas. Ou le remplacer par implode() ? Tester avec l'export de personnes // 
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

class CSVExport implements ExportInterface {

    public static $CSV_SEPARATOR = ";";
    public static $personnes;

    //  *** Pour stocker le chemin vers le fichier à créer sur le disque 
    public static $fichier;
    public static $out; 

    public function __construct(){ }

    //  INTERFACE   // 

    public static function attr_nom_fichier() {
        //  *** fichier à enregistrer sur le disque 
        self::$fichier = ROOT.'exports/export_'.date('Y-m-d_H-i-s').'.csv';
        // self::$out = fopen(ROOT.'exports/export_'.date('Y-m-d_H-i-s').'.csv', 'a');
        self::$out = fopen(self::$fichier, 'a');
    }

    //  PRIVATE METHODS //

    private static function export_line($line) {

        // //  *** Pour fractionnement des fichiers à chaque ms :
        //  crée un nouveau fichier avec le nom de la ms courante 
        // self::$out = fopen(ROOT.'exports/export_'.time().'.csv', 'a');

        // $first = TRUE;

        // /*  *** On pourrait pas utiliser fputcsv() au lieu de ce trafic avec $CSV_SEPARATOR ? 
        //     A la fin il suffirait d'exporter le fichier créé, au lieu d'en créer 2 différents
        // */  
        // foreach($line as $field) {
        //     if($first)
        //         $first = FALSE;
        //     else
        //         echo self::$CSV_SEPARATOR;

        //     echo $field;
        // }
        // echo PHP_EOL;

        fputcsv(self::$out, $line);
    }


    //  PUBLIC //

    public static function export_personnes(){
        global $mysqli;

        // self::entete();
        self::attr_nom_fichier();

        // self::export_line(array("id","noms","prenoms"));
        fputcsv(self::$out, array("id","noms","prenoms"));

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
            // $prenoms = implode(" ", $prenoms);
            // $noms = implode(" ", $noms);

            // self::export_line(array($id, $noms, $prenoms));
            fputcsv(self::$out, array($id, $noms, $prenoms));
        }

        //  timeline 
        fputcsv(self::$out, array(date('Y-m-d_H-i-s')));
        // echo '<br>$fichier : ';
        // var_dump(self::$fichier);
        // fputcsv(self::$out, array(self::$fichier));
        
        fclose(self::$out);
        
        self::entete();
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

        //  *** pour pouvoir exporter une fraction des relations par id : 
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

            // self::export_line($line);
            fputcsv(self::$out, $line);
        // }
    }


    //  PUBLIC  //

    public static function export_relations($start, $end, $names = FALSE, $dates = FALSE, $deux_sens = FALSE) { 
        global $mysqli, $line;  //   retirer $line ? 

        
        self::attr_nom_fichier();

        //  ***  entete() déplacée après fclose() pour pouvoir avoir accès au fichier (à voir ?) *** // 
        // self::entete();
        //  *** TODO : Commencer par test entete()   *** // 

        $line = [];
        $line[] = "id";

        self::add_personne_to_line($line, "src", $names);
        self::add_personne_to_line($line, "dest", $names);
        $line[] = "statut";
        if($dates)
            $line[] = "date";

        // self::export_line($line);
        fputcsv(self::$out, $line);

        self::$personnes = $mysqli->get_personnes(FALSE);

        // faire un Database->get_relations() comme get_personnes 
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
        fputcsv(self::$out, array(date('Y-m-d_H-i-s')));
        // echo '<br>$fichier : ';
        // var_dump(self::$fichier);
        // fputcsv(self::$out, array(self::$fichier));
        
        fclose(self::$out);
        
        self::entete();
    }

    public static function entete(){
        
        //  *** Récupérer les noms des fichiers du dossier exports/ 
        //  *** Ca ne sert pas pour l'instant, on le virera si ça n'a pas d'utilité 
        // $dir = ROOT."exports";
        // $fichiers = scandir($dir,1);
        
        header('Content-type: text/csv');
        // header('Content-Disposition: attachment; filename="export.csv"');
        header('Content-Disposition: attachment; filename="' . 'export_'.date('Y-m-d_H-i-s').'.csv' . '"');
        
        //  *** exporter le fichier enregistré : 
        readfile(self::$fichier);
    }
}


?>
