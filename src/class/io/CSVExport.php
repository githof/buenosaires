<?php

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

        var $CSV_SEPARATOR = ";";

        function __construct(){

        }

        function export_personnes(){
            global $mysqli;

            $this->entete();

            echo "id".$this->CSV_SEPARATOR.
                "noms".$this->CSV_SEPARATOR.
                "prenoms".PHP_EOL;

	    $personnes = $mysqli->get_personnes(FALSE);
	    foreach($personnes as $id => $personne){
	      $prenoms = [];
	      foreach($personne->prenoms as $prenom)
		$prenoms[] = $prenom->to_string();

	      $noms = [];
	      foreach($personne->noms as $nom)
		$noms[] = $nom->to_string();

	      $prenoms = array_to_string(
					 $prenoms,
					 " ");
	      $noms = array_to_string(
				      $noms,
				      " ");

	      echo $id . $this->CSV_SEPARATOR .
		$noms . $this->CSV_SEPARATOR .
		$prenoms . PHP_EOL;
	    }
	}

        function export_relations(){
            global $mysqli;

            $this->entete();

            echo "id".$this->CSV_SEPARATOR.
                "source".$this->CSV_SEPARATOR.
                "destination".$this->CSV_SEPARATOR.
                "statut".PHP_EOL;

            $results = $mysqli->select("relation", ["*"]);
            if($results != FALSE && $results->num_rows){
                while($row = $results->fetch_assoc()){
                    $relation = new Relation();
                    $relation->result_from_db($row);

                    echo $relation->id . $this->CSV_SEPARATOR .
                        $relation->personne_source->id . $this->CSV_SEPARATOR .
                        $relation->personne_destination->id . $this->CSV_SEPARATOR .
                        $relation->get_statut_name() . PHP_EOL;
                }
            }
        }

        function entete(){
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
        }
    }


?>
