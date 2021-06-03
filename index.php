<?php

// afficher les erreurs /!\ #devOnly /!\  ***
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// include("/home/morgan/internet/file_with_errors.php");
// echo "<br>";
// end #devOnly

$exec_time_script = microtime(TRUE);

//  *** à mettre dans html_entities ou URLRewriter ou config 
define("ROOT", __DIR__ . "/");

session_start();

$url_parsed = [];
$ARGS = [];
$page_title = "Buenos Aires";

include_once(ROOT."config.php");
include_once(ROOT."src/utils.php");
include_once(ROOT."src/class/Log.php");
include_once(ROOT."src/class/Alert.php");
include_once(ROOT."src/class/model/Account.php");
include_once(ROOT."src/class/io/Database.php");

$log = new Log();
$alert = new Alert();
$mysqli = new Database();
$account = new Account();

include_once(ROOT."src/URLRewritter.php");

if(!is_dir(TMP_DIRECTORY))
    mkdir(TMP_DIRECTORY, 0777);

if(isset($_POST["action"])){
    if($_POST["action"] == "deconnexion" && $account->is_connected){
        $account->disconnect();
        $alert->success("Déconnexion réussie");
    }else if($_POST["action"] == "connexion" && isset($_POST['connect_email'], $_POST['connect_pass']) && !$account->is_connected){
        $account->set_email(safe($_POST['connect_email']));
        $account->set_password(safe(md5($_POST['connect_pass'])));
        if($account->connect())
            $alert->success("Connexion réussie !");
        else
            $alert->warning("Echec de la connexion");
    }
}


// VIEW SCRIPT
//  *** à factoriser 
$view = "";
$is_get = FALSE;
if(isset($url_parsed["page"])){
    if($url_parsed["page"] == "get"){
        if(can_access($access_pages[$ARGS["s"]])){
            $view = ROOT."src/views/get/" . $ARGS["s"] . ".php";
            $is_get = TRUE;
        }else{
            $alert->warning("Accès a un contenu restreint");
        }
    } else {
        if(can_access($access_pages[$url_parsed["page"]])){     //  *** undefined index resultat line 66 // 
            $view = ROOT."src/views/pages/" . $url_parsed["include"] . ".php";
            $page_title = $url_parsed["title"];
        } else {
            $view = ROOT."src/views/pages/interdit.php";
            $page_title = "Accès restreint";
        }
    }
} else {
    $view = ROOT."src/views/pages/404.php";
    $page_title = "Page introuvable";
}

if($is_get){
    include_once($view);
    echo $alert->html_all();
} else {
    // HEADER
    include_once(ROOT."src/views/header.php");
    $header_output = ob_get_clean();
    ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);

    // VIEW
    include_once($view);
    $page_output = ob_get_clean();

    // ALERTS
    $alerts_output = $alert->html_all();

    if(isset($ARGS["export"])){
        //  test form radio html 
        // echo '<br>$ARGS : ';
        // var_dump($ARGS);
        // echo '<br>$_REQUEST : ';
        // var_dump($_REQUEST);
        //  fin test 
        echo $page_output;
    }
    else {
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <base href="<?php echo BASE_URL; ?>">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="res/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="res/multi-select/css/multi-select.css" rel="stylesheet" />
        <link href="res/xmlselect/includes/html5.css" rel="stylesheet" />
        <link href="res/style.css" rel="stylesheet" />
        <script
            src="https://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
            crossorigin="anonymous"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/lodash/4.6.1/lodash.min.js"></script>
        <script type="text/javascript" src="res/clipboard.min.js"></script>
        <script type="text/javascript" src="res/xmlselect/tag_set.js"></script>
        <script type="text/javascript" src="res/xmlselect/xml_node.js"></script>
        <script type="text/javascript" src="res/xmlselect/taggable_xml.js"></script>
        <script type="text/javascript" src="res/xmlselect/select_and_show.js"></script>
        <script type="text/javascript" src="res/xmlselect/xml_parser.js"></script>
        <script type="text/javascript" src="res/xmlselect/init.js"></script>
        <title><?php echo $page_title; ?></title>
    </head>
    <body>
        <div class="nav-bar">
            <div class="nav-bar-in">
            <?php echo $header_output; ?>
            </div>
        </div>
        <div class="main">
            <h1><?php echo $page_title ?></h1>
            <div class="page">
                <?php 
                    echo $page_output; 
                    //  test form radio html 
                    // echo '<br>$ARGS : ';
                    // var_dump($ARGS);
                    // echo '<br>$_REQUEST : ';
                    // var_dump($_REQUEST);
                    //  fin test 
                ?>
            </div>
        </div>
        <div id="alert-container">
            <?php echo $alerts_output; ?>
        </div>
        <script src="res/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="res/quicksearch/jquery.quicksearch.js" type="text/javascript"></script>
        <script src="res/multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
        <script src="res/init.js" type="text/javascript"></script>
    </body>
</html>

<?php
    }
}

$mysqli->close();
$exec_time_script = microtime(TRUE) - $exec_time_script;
$log->i("EXEC TIME SCRIPT PAGE ".($exec_time_script *1000)." ms");
$log->write();


?>
