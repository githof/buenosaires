<?php

    $urls = [
        "/(?'page'table)/(?'table'[^/]+)"
            =>  ["table", "table"],
        "/(?'page'table)"
            =>  ["table", "table"],
        "/(?'page'personne)/(?'id'\d+)"
            =>  ["detail_personne", "personne"],
        "/(?'page'acte)/(?'id'\d+)"
            =>  ["detail_acte", "acte"],
        "/(?'page'recherche)"
            =>  ["search", "recherche"],
        "/(?'page'resultat)(\?(?'args'[^/]+)){0,1}"
            =>  ["results", "résultats"],
        "/(?'page'supprimer)/(?'type'acte)/(?'id'\d+)"
            =>  ["delete", "supprimer acte"],
        "/(?'page'get)\?(?'args'[^/]+)"
            =>  ["", ""],
        "/(?'page'import)"
            =>  ["import", "import d'acte(s)"],
        "/(?'page'fusion)"
            =>  ["fusion", "fusion"],
        "/(?'page'logs)"
            =>  ["logs", "logs"],
        "/(?'page'new-account)"
            =>  ["new_account", "création d'un compte"],
        "(?'page'/)"
            =>  ["accueil", "bienvenue"]
    ];


    $uri = rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/');
    $uri = '/' . trim(str_replace($uri, '', $_SERVER['REQUEST_URI']), '/');
    $uri = urldecode($uri);

    foreach($urls as $url => $infos){
        if(preg_match('~^'.$url.'$~i', $uri, $params)){
            $url_parsed = $params;
            $url_parsed["include"] = $infos[0];
            $url_parsed["title"] = $infos[1];
            break;
        }
    }

    if(isset($url_parsed["args"])){
        $args = explode("&", $url_parsed["args"]);
        foreach($args as $arg){
            $split = explode("=", $arg);
            if(endsWith($split[0], "[]")){
                $key = substr($split[0],0, strlen($split[0]) -2);
                if(!isset($ARGS[$key]))
                    $ARGS[$key] = [];
                if(strlen($split[1]) > 0)
                    $ARGS[$key][] = safe($split[1]);
            }else if(strlen($split[1]) > 0)
                $ARGS[$split[0]] = safe($split[1]);
        }
    }
?>
