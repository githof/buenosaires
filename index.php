<?php

    session_start();

    include("config.php");
    include("src/log.php");
    include("src/utils.php");
    include("src/Alert.php");
    include("src/account/account.php");
    include("src/database/database.php");

    $log = new Log();
    $alert = new Alert();
    $mysqli = new Database();
    $account = new Account();

    if(!is_dir(TMP_DIRECTORY))
        mkdir(TMP_DIRECTORY, 0777);

    function get_page(){
        if(!isset($_GET["p"]))
            return "acceuil.php";

        switch($_GET["p"]){
            case "new_account":
                return "new_account.php";
            case "disconnect":
                return "disconnect.php";
            case "import":
                return "import.php";
            case "export":
                return "export.php";
        }

        return "acceuil.php";
    }

    if($account->is_connected && isset($_GET["p"]) && $_GET["p"] == "disconnect"){
        $account->disconnect();
        $alert->add_success("Déconnexion réussie");
    }


    // HEADER
    include_once("views/header.php");
    $header_output = ob_get_clean();
    ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);

    // CURRENT PAGE
    include_once("views/pages/" . get_page());
    $page_output = ob_get_clean();

    // ALERTS
    $alerts_output = $alert->html_all();

?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="res/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="res/style.css" rel="stylesheet" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
        <title>Buenos Aires</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-2 col-md-2 sidebar">
                    <?php echo $header_output; ?>
                </div>
                <div class="col-sm-10 col-md-10 col-sm-offset-3 col-md-offset-2 main">
                    <?php echo $alerts_output; ?>
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
