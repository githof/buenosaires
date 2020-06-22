<?php

    include_once(ROOT."src/class/model/Acte.php");

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
	var $personnes;

        function __construct(){

        }

	private function export_line($line)
	{
	  $first = TRUE;
	  foreach($line as $field)
	  {
	    if($first)
	      $first = FALSE;
	    else
	      echo $this->CSV_SEPARATOR;

	    echo $field;
	  }
	  echo PHP_EOL;
	}

        function export_personnes(){
            global $mysqli;

            $this->entete();

	    $this->export_line(array("id","noms","prenoms"));

	    $personnes = $mysqli->get_personnes(FALSE);

	    foreach($personnes as $id => $personne)
	    {
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

	      $prenoms = array_to_string(
					 $prenoms,
					 " ");
	      $noms = array_to_string(
				      $noms,
				      " ");

	      $this->export_line(array($id, $noms, $prenoms));
	    }
	}

	private function add_personne_to_line(&$line, $p,
					      $names = FALSE)
	{
	  if($p instanceof Personne)
	  {
	    $line[] = $p->id;
	    if($names)
	    {
	      $personne = $this->personnes[$p->id];
	      $line[] = $personne->prenoms_str;
	      $line[] = $personne->noms_str;
	    }
	  }
	  elseif(is_string($p))
	  {
	    $line[] = $p."_id";
	    if($names)
	    {
	      $line[] = $p."_prenoms";
	      $line[] = $p."_noms";
	    }
	  }
	}

	private function add_date(&$line, $relation)
	{
	  $date = $relation->get_date();
	  $line[] = "$date";
	}

  private function export_relation($relation,
                      $names, $dates, $reverse)
  {
    $line = [];
    $line[] = $reverse ? -$relation->id : $relation->id;
    if($reverse){
      $this->add_personne_to_line($line,
          $relation->personne_destination,
          $names);
      $this->add_personne_to_line($line,
          $relation->personne_source,
          $names);
    }
    else {
      $this->add_personne_to_line($line,
          $relation->personne_source,
          $names);
      $this->add_personne_to_line($line,
          $relation->personne_destination,
          $names);
    }
    $line[] = $relation->get_statut_name();
    if($dates)
      $this->add_date($line, $relation);

    $this->export_line($line);
  }

        function export_relations($names = FALSE, $dates = FALSE){
            global $mysqli;

            $this->entete();

	    $line = [];
	    $line[] = "id";
	    $this->add_personne_to_line($line, "src", $names);
	    $this->add_personne_to_line($line, "dest", $names);
	    $line[] = "statut";
	    if($dates)
	      $line[] = "date";
	    $this->export_line($line);

	    $this->personnes = $mysqli->get_personnes(FALSE);

	    // faire un Database->get_relations() comme get_personnes
            $results = $mysqli->select("relation", ["*"]);
            if($results != FALSE && $results->num_rows){
                while($row = $results->fetch_assoc()){
                    $relation = new Relation();
                    $relation->result_from_db($row);

                    $this->export_relation(
                      $relation, $names, $dates, FALSE);
                    $this->export_relation(
                      $relation, $names, $dates, TRUE);
                }
            }
        }

        function entete(){
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="export.csv"');
        }
    }


?>
