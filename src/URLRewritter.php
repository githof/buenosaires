<?php

    $urls = [
        'table/t'       =>  "/(?'page'table{1})/(?'table'[^/]+)",   // /table/actes
        'table'         =>  "/(?'page'table{1})",                   // /table
        'import'        =>  "/(?'page'import{1})",                  // /import
        'logs'          =>  "/(?'page'logs{1})",                    // /logs
        'new_account'   =>  "/(?'page'new-account{1})",             // /new-account
        'disconnect'    =>  "/(?'page'disconnect{1})",              // /disconnect
        'acceuil'       =>  "/"
    ];


    $uri = rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/');
    $uri = '/' . trim(str_replace($uri, '', $_SERVER['REQUEST_URI']), '/');
    $uri = urldecode($uri);

    foreach($urls as $action => $url){
        if(preg_match('~^'.$url.'$~i', $uri, $params)){
            $url_parsed = $params;
            break;
        }
    }

?>
