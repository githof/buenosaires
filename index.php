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
        }

        return "acceuil.php";
    }
?>

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="stylesheet" href="includes/style.css" />
        <script type="text/javascript" src="includes/fonctions_js.js"></script>
        <title>Buenos Aires</title>
    </head>
    <body>
        <?php include("views/header.php"); ?>
        <div class="page">
            <?php include("views/pages/" . get_page()); ?>
        </div>
    </body>
</html>

<?php
    $mysqli->close();
?>
