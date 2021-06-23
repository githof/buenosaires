<?php

    $urls = [
        "/(?'page'table)/(?'table'[^/]+)"
            =>  ["table", "Table"],
        "/(?'page'table)"
            =>  ["table", "Tables"],
        "/(?'page'personne)/(?'id'\d+)"
            =>  ["detail_personne", "Personne"],
        "/(?'page'acte)/(?'id'\d+)"
            =>  ["detail_acte", "Acte"],
        "/(?'page'recherche)"
            =>  ["search", "Recherche"],
        "/(?'page'resultat)"
            =>  ["results", "Résultats de la recherche"],
        "/(?'page'supprimer)/(?'type'acte)/(?'id'\d+)"
            =>  ["delete", "Supprimer acte"],
        "/(?'page'get)"
            =>  ["", ""],
        "/(?'page'import)"
            =>  ["import", "Import"],
        "/(?'page'export)"
            =>  ["export", "Export"],
        "/(?'page'fusion)"
            =>  ["fusion", "Fusion"],
        "/(?'page'dissocier)"
            => ["dissocier", "Dissocier"],
        "/(?'page'logs)"
            =>  ["logs", "Logs"],
        "/(?'page'new-account)"
            =>  ["new_account", "Nouveau compte"],
        "/(?'page'administration)"
            => ["administration", "Administration"],
        "/(?'page'test)"
            =>  ["test", "Test"],
        "(?'page'/)"
            =>  ["accueil", "Buenos Aires"]
    ];


    $uri = rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/');
    $uri = '/' . trim(str_replace($uri, '', $_SERVER['REQUEST_URI']), '/');
    $uri = urldecode($uri);

    $args = NULL;
    if(strpos($uri, "?") !== FALSE){
        $split = explode("?", $uri);
        $uri = $split[0];
        $args = $split[1];
    }

    foreach($urls as $url => $infos){
        if(preg_match('~^'.$url.'$~i', $uri, $params)){
            $url_parsed = $params;
            $url_parsed["include"] = $infos[0];
            $url_parsed["title"] = $infos[1];
            break;
        }
    }

    if(isset($args)){
        $args_split = explode("&", $args);
        foreach($args_split as $arg){
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
