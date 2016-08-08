<?php

    $urls = [
        "/(?'page'table{1})/(?'table'[^/]+)"    =>  ["table", "table"],
        "/(?'page'table{1})"                    =>  ["table", "table"],
        "/(?'page'personne)/(?'id'\d+)"         =>  ["detail_personne", "personne"],
        "/(?'page'acte)/(?'id'\d+)"             =>  ["detail_acte", "acte"],
        "/(?'page'recherche)"                   =>  ["search", "recherche"],
        "/(?'page'resultat)"                    =>  ["result", "résultats"],
        "/(?'page'import{1})"                   =>  ["import", "import"],
        "/(?'page'logs{1})"                     =>  ["logs", "logs"],
        "/(?'page'new-account{1})"              =>  ["new_account", "création d'un compte"],
        "/(?'page'disconnect{1})"               =>  ["disconnect", "déconnexion"],
        "(?'page'/)"                        =>  ["accueil", "bienvenue"]
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

?>
