<?php

    function log_html($line){
        $class = "log-line";

        if(strstr($line, "[ERROR]") !== FALSE)
            $class .= " log-error";
        else if(strstr($line, "[WARNING]") !== FALSE)
            $class .= " log-warning";
        else if(strstr($line, "[DEBUG]") !== FALSE)
            $class .= " log-debug";

        return "<div class='$class'>$line</div>";
    }

    function read_logs(){
        global $log, $alert;

        $handle = @fopen($log->filename, 'r');
        if($handle === FALSE){
            $alert->e("Impossible de lire le fichier log : $log->filename");
            return;
        }

        while(($line = fgets($handle)) !== FALSE){
            echo log_html($line);
        }

        if(!feof($handle)){
            $alert->e("Erreur lors de la lecture du fichier log : $log->filename");
            return;
        }

        fclose($handle);
    }

?>

<div class="log-container">
    <?php read_logs(); ?>
</div>
