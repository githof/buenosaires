<?php

    session_start();

    $url_parsed = [];
    $page_title;
    $RACINE = $_SERVER['DOCUMENT_ROOT'];

    include_once("src/URLRewritter.php");
    include_once("config.php");
    include_once("src/log.php");
    include_once("src/utils.php");
    include_once("src/Alert.php");
    include_once("src/account/Account.php");
    include_once("src/database/Database.php");

    $log = new Log();
    $alert = new Alert();
    $mysqli = new Database();
    $account = new Account();

    if(!is_dir(TMP_DIRECTORY))
        mkdir(TMP_DIRECTORY, 0777);

    function get_title($value){
        switch($value){
            case "new_account":
                return "Création d'un compte";
            case "disconnect":
                return "Déconnexion";
            case "import":
                return "Import";
            case "export":
                return "Export";
            case "logs":
                return "Logs";
            case "tables":
                return "Tables";
        }

        return "Acceuil";
    }

    if($account->is_connected && isset($url_parsed["page"]) && $url_parsed["page"] == "disconnect"){
        $account->disconnect();
        $alert->add_success("Déconnexion réussie");
    }


    // HEADER
    include_once("views/header.php");
    $header_output = ob_get_clean();
    ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);

    // CURRENT PAGE
    if(isset($url_parsed["page"])){
        include_once("views/pages/" . $url_parsed["page"] . ".php");
        $page_title = get_title($url_parsed["page"]);
    }else{
        include_once("views/pages/acceuil.php");
        $page_title = "Acceuil";
    }

    $page_output = ob_get_clean();

    // ALERTS
    $alerts_output = $alert->html_all();

?>

<!DOCTYPE HTML>
<html>
    <head>
        <base href="<?php echo BASE_URL; ?>">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="res/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="res/style.css" rel="stylesheet" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
        <title>Buenos Aires</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="sidebar">
                <?php echo $header_output; ?>
            </div>
            <div class="main">
                <?php echo $alerts_output; ?>
                <h1><?php echo $page_title ?></h1>
                <div class="page">
                    <?php echo $page_output; ?>
                </div>
            </div>
        </div>
        <script src="res/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>

<?php
    $mysqli->close();
    $log->close();
?>
