<?php

// afficher les erreurs /!\ #devOnly /!\  ***
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// end #devOnly

$exec_time_script = microtime(TRUE);

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
        if(can_access($access_pages[$url_parsed["page"]])){ 
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
        echo $page_output;
    }
    else {
?>

<!DOCTYPE HTML>
<html>
    <?php 
        include(ROOT."src/views/head.php"); 
    ?> 

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
