<?php

    session_start();

    include("src/utils.php");
    include("src/account/account.php");
    include("src/database/database.php");

    $mysqli = new Database();
    $account = new Account();

    function get_page(){
        if(!isset($_GET["p"]))
            return "acceuil.php";

        switch($_GET["p"]){
            case "new_account":
                return "new_account.php";
            case "disconnect":
                return "disconnect.php";
        }

        return "acceuil.php";
    }

    if($account->is_connected && isset($_GET["p"]) && $_GET["p"] == "disconnect"){
        $account->disconnect();
    }

?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="res/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
        <link href="res/style.css" rel="stylesheet" />
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script type="text/javascript" src="includes/fonctions_js.js"></script>
        <title>Buenos Aires</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3 col-md-2 sidebar">
                    <?php include("views/header.php"); ?>
                </div>
                <div class="col-sm-9 col-md-10 col-sm-offset-3 col-md-offset-2 main">
                    <?php include("views/pages/" . get_page()); ?>
                </div>
            </div>
        </div>

        <script src="res/bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>

<?php
    $mysqli->close();
?>
