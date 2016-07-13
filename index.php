<?php

    session_start();

    include("src/utils.php");
    include("src/account/account.php");
    include("src/database/database.php");

    $mysqli = new Database();
    $account = new Account();
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
        <?php
            include("views/header.php");
            include("views/acceuil.php");
        ?>
    </body>
</html>

<?php
    $mysqli->close();
?>
