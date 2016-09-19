<?php

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
        $str = "";
        $i = 0;
        $length = count($tab);
        foreach($tab as $entry){
            $str .= "'$entry'";
            if($i < $length -1)
                $str .= "$separator";
            $i++;
        }
        return $str;
    }

    function parse_prenoms($prenoms_str){
        $prenoms_array = explode(",", $prenoms_str);
        $prenoms = [];

        foreach($prenoms_array as $prenom){
            $prenoms[] = new Prenom(NULL, $prenom);
        }
        return $prenoms;
    }

    function parse_noms($noms_str){
        $noms_array = explode(",", $noms_str);
        $noms = [];

        foreach($noms_array as $nom){
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
        
        return $level <= $account->get_rang();
    }

?>
