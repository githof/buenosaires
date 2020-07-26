<?php

function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}

    function safe($string){
        global $mysqli;
        return htmlspecialchars($mysqli->real_escape_string(trim($string)));
    }

    function no_accent($string){
        return str_replace(
            [
                'à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò',
                'ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','À','Á','Â','Ã','Ä','Ç',
                'È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú',
                'Û','Ü','Ý'
            ],
            [
                'a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o',
                'o','o','o','o','u','u','u','u','y','y','A','A','A','A','A','C',
                'E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U',
                'U','U','Y'
            ],
            $string
        );
    }

    function accent_uppercase($string){
        return str_replace(
            [
                'à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò',
                'ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ'
            ],
            [
                'À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò',
                'Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý', 'Ÿ'
            ],
            $string
        );
    }

    function pre_process_acte_xml($acte_xml){
        return preg_replace('!\s+!', ' ', $acte_xml);
    }

    function read_date($date){
        $split = explode('-', trim($date));
        if(count($split) == 3){
            $d = format_date($split[2], $split[1], $split[0]);
            return [$d, $d];
        }else if(count($split) == 1){
            return [
                format_date($split[0], "01", "01"),
                format_date($split[0], "12", "31")
            ];
        }
        return NULL;
    }

    function format_date($year, $month, $day){
        return fill_number($year, 4)
            . "-" . fill_number($month, 2)
            . "-" . fill_number($day, 2);
    }

    function fill_number($str, $length){
        while(strlen($str) < $length)
            $str = "0" . $str;
        return $str;
    }

    function startsWith($str, $start) {
        return $start === "" || strrpos($str, $start, -strlen($str)) !== false;
    }

    function endsWith($str, $end){
        $length = strlen($end);
        if ($length == 0) {
            return true;
        }

        return (substr($str, -$length) === $end);
    }

    function array_to_string_with_separator($tab, $separator){
        return implode($separator, $tab);
    }

    function renommer_personne($personne, $noms, $prenoms)
    {
      global $mysqli;

      $mysqli->delete("prenom_personne", "personne_id='$personne->id'");
      $i = 1;
      foreach($prenoms as $prenom){
          $mysqli->into_db($prenom);
          $mysqli->into_db_prenom_personne($personne, $prenom, $i);
          $i++;
      }

      $mysqli->delete("nom_personne", "personne_id='$personne->id'");
      $i = 1;
      foreach($noms as $nom){
          $mysqli->into_db($nom);
          $mysqli->into_db_nom_personne($personne, $nom, $i);
          $i++;
      }
    }

    function parse_prenoms($prenoms_str){
        $prenoms_array = explode(",", $prenoms_str);
        $prenoms = [];

        foreach($prenoms_array as $prenom){
            if($prenom == '')
              continue;
            $prenoms[] = new Prenom(NULL, $prenom);
        }
        return $prenoms;
    }

    function parse_noms($noms_str){
        $noms_array = explode(",", $noms_str);
        $noms = [];

        foreach($noms_array as $nom){
            if($nom == '')
              continue;
            $split = explode(")", $nom);
            if(count($split) == 2){
                $split0 = explode("(", $split[0]);
                $attribut = $split0[1];
                $noms[] = new Nom(NULL, trim($split[1]), NULL, trim($attribut));
            }else{
                $noms[] = new Nom(NULL, trim($nom));
            }
        }
        return $noms;
    }

    function has_prenom($prenoms, $prenom){
        foreach($prenoms as $p){
            if($p->id == $prenom->id)
                return TRUE;
        }
        return FALSE;
    }

    function has_nom($noms, $nom){
        foreach($noms as $n){
            if($n->id == $nom->id)
                return TRUE;
        }
        return FALSE;
    }

    function default_input_prenoms($prenoms_A, $prenoms_B = []){
        $str = "";
        $start = TRUE;
        foreach($prenoms_A as $prenom){
            if($start)
                $start = FALSE;
            else
                $str .= ", ";
            $str .= $prenom->to_string();
        }
        foreach($prenoms_B as $prenom){
            if(has_prenom($prenoms_A, $prenom))
                continue;
            if($start)
                $start = FALSE;
            else
                $str .= ", ";
            $str .= $prenom->to_string();
        }
        return $str;
    }

    function default_input_noms($noms_A, $noms_B = []){
        $str = "";
        $start = TRUE;
        foreach($noms_A as $nom){
            if($start)
                $start = FALSE;
            else
                $str .= ", ";
            if(isset($nom->attribut))
                $str .= "($nom->attribut) ";
            $str .= $nom->nom;
        }
        foreach($noms_B as $nom){
            if(has_nom($noms_A, $nom))
                continue;
            if($start)
                $start = FALSE;
            else
                $str .= ", ";
            if(isset($nom->attribut))
                $str .= "($nom->attribut) ";
            $str .= $nom->nom;
        }
        return $str;
    }

    function can_access($level){
        global $account;

        return $level <= $account->get_rang() && $account->is_connected || $level == 0;
    }

    function error_message_receive_file($error_code){
        $log = "Erreur ($error_code) inconnue lors du téléchargement du fichier";
        $alert = "Erreur lors du téléchargement du fichier vers le serveur";

        switch($error_code){
            case 1:
                $message = "La taille du fichier téléchargé excède la valeur maximale supportée par le serveur";
                $log = $message;
                $alert = $message;
                break;
            case 2:
                $message = "La taille du dichier téléchargé excède la valeur MAX_FILE_SIZE, qui a été spécifiée dans le formulaire HTML";
                $log = $message;
                $alert = $message;
                break;
            case 3:
                $message = "Le fichier n'a été que partiellement téléchargé";
                $log = $message;
                $alert = $message;
                break;
            case 4:
                $message = "Aucun fichier n'a été téléchargé";
                $log = $message;
                $alert = $message;
                break;
            case 6:
                $log = "Un fichier temporaire est manquant";
                break;
            case 7:
                $log = "Echec de l'écriture du fichier sur le disque";
                break;
            case 8:
                $log = "Une extension PHP a arrêté l'envoi de fichier";
                break;
        }
        return [
            "log" => $log,
            "alert" => $alert
        ];
    }

    function receive_file($key){
        global $alert, $log;

        if($_FILES[$key]["error"] > 0){
            $tabs = error_message_receive_file($_FILES[$key]["error"]);
            $log->e($tabs["log"]);
            $alert->error($tabs["alert"]);
            return NULL;
        }

        $infos = pathinfo($_FILES[$key]["name"]);
        if($infos["extension"] === "xml"){
            $uploadfile = TMP_DIRECTORY . "/" . basename($_FILES[$key]["name"]);
            $uploadfile = append_unique_identifier($uploadfile);

            $source = fopen($_FILES[$key]["tmp_name"], "r");
            $destination = fopen($uploadfile, "w");

            while($line = fgets($source)){
                fputs($destination, pre_process_acte_xml($line)."\n");
            }

            fclose($source);
            fclose($destination);

            return $uploadfile;
        }else{
            $alert->error("Le fichier doit être au format XML");
            return NULL;
        }
    }

    function receive_text($text){
        global $alert, $log;

        $sources =
            "<document><ACTES>".PHP_EOL.
            stripslashes($text).PHP_EOL.
            "</ACTES></document>";
        $sources = '<?xml version="1.0" encoding="UTF-8"?>' . $sources;

        $filename = TMP_DIRECTORY . "/new_actes";
        $filename = append_unique_identifier($filename);
        $filename = $filename . ".xml";
        $tmp_file = @fopen($filename, "w");
        if($tmp_file === FALSE){
            $log->e("Erreur de la fct fopen($filename, 'w')");
            $alert->error("Erreur interne du serveur lors de l'import");
            return NULL;
        }
        if(fwrite($tmp_file, $sources) === FALSE){
            $log->e("Erreur de la fct fwrite($tmp_file, $sources)");
            $alert->error("Erreur interne du serveur lors de l'import");
            return NULL;
        }
        fclose($tmp_file);
        return $filename;
    }

    function append_unique_identifier($filename){
        srand(intval(date("YmdHis")));
        return $filename."_".rand(1, 9999999);
    }

/*
  Comme array_unique mais en testant seulement l'attribut id des objets contenus dans le tableau
*/
function array_unique_by_id($a)
{
  $ids = array();
  $res = array();
  foreach($a as $x)
  {
    if(! isset($x->id)) continue;
    if(in_array($x->id, $ids)) continue;
    $ids[] = $x->id;
    $res[] = $x;
  }
  return $res;
}

function string_list_of_ids($liste)
{
  $ids = [];

  foreach($liste as $x)
    $ids[] = $x->id;

  return implode(',', $ids);
}

?>
