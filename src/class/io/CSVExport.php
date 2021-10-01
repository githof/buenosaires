<?php

//  *** ExportInterface sert à attribuer une entête d'export et un nom au fichier exporté 
include_once(ROOT."src/class/io/ExportInterface.php");
include_once(ROOT."src/class/model/Acte.php");

//  *** implode() remplace la fct array_to_string()  // 
/*  function array_to_string($array, $separator){
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
*/

class CSVExport implements ExportInterface {

    public static $CSV_SEPARATOR = ";";
    public static $personnes;
    
    //  *** Pour stocker le chemin vers le fichier à créer sur le disque 
    public static $fichier;
    public static $out; 

    public function __construct(){ }


    //  EXPORT INTERFACE   // 

    public static function attr_nom_fichier($object) {
        //  *** fichier à enregistrer sur le disque 
        self::$fichier = ROOT.'exports/export_'.$object.'_'.date('Y-m-d_H-i-s').'.csv';
        
        self::$out = fopen(self::$fichier, 'a');
    }

    public static function entete($object){
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="' . 'export_'.$object.'_'.date('Y-m-d_H-i-s').'.csv' . '"');
        
        //  *** exporter le fichier sous le même nom : 
        readfile(self::$fichier);
    }


    //  PRIVATE METHODS //

    // *** fputcsv remplace la fct export_line()  // 
    

    //  PUBLIC //

    //  *** rewrite-noms-export
    public static function export_personnes($attr, $no_accent){ 
        global $mysqli;

        //  *** rewrite-noms-export : adapter nom fichier
        $object = ($attr == true) ? 'personne-avec-de' : 'personne-sans-de';
        $object .= ($no_accent == false) ? '-avec-accents' : '-sans-accent';
        self::attr_nom_fichier($object);

        fputcsv(self::$out, array("id","noms","prenoms"));

        $personnes = $mysqli->get_personnes(FALSE, $attr, $no_accent);

        foreach($personnes as $id => $personne) {
            /*
            bricolage sur les tableaux de noms et prénoms
            ça pourrait être un utilitaire des classes Nom et
            Prenom,
            qui d'ailleurs pourraient hériter d'une même classe
            */
            $prenoms = [];
            foreach($personne->prenoms as $prenom)
                $prenoms[] = $prenom->to_string($no_accent); 

            $noms = [];
            foreach($personne->noms as $nom)
                $noms[] = $nom->to_string($attr, $no_accent);

            $prenoms = implode(' ', $prenoms);
            $noms = implode(' ', $noms);

            fputcsv(self::$out, array($id, $noms, $prenoms));
        }

        //  timeline, si besoin 
        // fputcsv(self::$out, array(date('Y-m-d_H-i-s')));
        
        fclose(self::$out);

        //  *** rewrite-noms-export : adapter nom fichier
        self::entete($object);
    }

    //  PRIVATE METHODS //

    private static function add_personne_to_line(&$line, $p, $names) {

        //  *** utiliser autre chose que gettype() ou instanceof() 
        // if($p instanceof Personne) {
        if(gettype($p) != 'string') {
            $line[] = $p->id;

            if($names == "1") {
                $personne = self::$personnes[$p->id];   

                $line[] = $personne->prenoms_str;
                $line[] = $personne->noms_str;
            } 
        // } elseif(is_string($p)) {
        } else {
            $line[] = $p."_id";

            if($names) {
                $line[] = $p."_prenoms";
                $line[] = $p."_noms";
            }
        }
    }

    //  *** méthode add_date() à revoir : elle prend trop de temps 
    private static function add_date(&$line, $relation) {
        
        // $date = $relation->get_date();
        /* *** fix-add-date 
            Récupérer la date de chaque relation dans l'objet $relation
        */
        $date = $relation->date;
        $line[] = "$date";

    }


    private static function export_relation($relation, 
                                            $names, 
                                            $dates, 
                                            $reverse, 
                                            $attr, 
                                            $no_accent) { 
        $line = array();
        $line[] = $reverse ? -$relation->id : $relation->id;  

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

            fputcsv(self::$out, $line);
    }
    

    //  PUBLIC  //

    public static function export_relations($names, 
                                            $dates, 
                                            $deux_sens, 
                                            $attr, 
                                            $no_accent) { 
        global $mysqli, $line; 

        //  *** rewrite-noms-export : adapter nom fichier
        /*  *** Attribuer noms de fichiers en fonction des options choisies */ 
        $date = ($dates == true) ? 'relations-avec-dates' : 'relations-sans-dates';
        $sens .= ($deux_sens == true) ? '-2-sens' : '-1-sens';
        $de .= ($attr == true) ? '-avec-de' : '-sans-de';
        $accents .= ($no_accent == false) ? '-avec-accents' : '-sans-accent';

        $object = $date . $sens . $de . $accents;
        self::attr_nom_fichier($object);

        $line = array();
        $line[] = "id";

        self::add_personne_to_line($line, "src", $names);
        self::add_personne_to_line($line, "dest", $names);
        $line[] = "statut";
        if($dates)
            $line[] = "date";

        fputcsv(self::$out, $line);

        // self::$personnes = $mysqli->get_personnes(FALSE, $attr, $no_accent);
        self::$personnes = $mysqli->get_personnes(TRUE, $attr, $no_accent); 

        /*  *** fix-add-date
            Test avec propriété $date ajoutée à Relation 
            Récupérer les objets Relation dans un array et les traiter un par un
        */
        $relations = $mysqli->get_relations(TRUE);
            foreach($relations as $relation) {
                //  *** par défaut relations dans les 2 sens 
                if(!$deux_sens) {
                    self::export_relation(
                        $relation, 
                        $names, 
                        $dates, 
                        FALSE,      //   !reverse 
                        $attr, 
                        $no_accent);     
                } else {
                    self::export_relation(
                        $relation, 
                        $names, 
                        $dates, 
                        FALSE,     //   !reverse 
                        $attr, 
                        $no_accent);
                    self::export_relation(
                        $relation, 
                        $names, 
                        $dates, 
                        TRUE,       //  reverse 
                        $attr, 
                        $no_accent);      
                }
            }
        // }
        
        fclose(self::$out);
        
        self::entete($object);
    }   
}


?>
