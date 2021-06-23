<?php

    $log_type_css = [
        "[ERROR]" => "log-error",
        "[WARNING]" => "log-warning",
        "[DEBUG]" => "log-debug"
    ];

    function log_html($line){
        global $log_type_css;

        $class = "log-line";
        $regex = "~^(?'date'[\d]{4}-[\d]{2}-[\d]{2}  [\d]{2}:[\d]{2}:[\d]{2}) (?'type'\[.*\]) (?'text'.*)$~i";
        if(preg_match($regex, $line, $matches)){
            if(isset($log_type_css[$matches["type"]]))
                $class .= " " . $log_type_css[$matches["type"]];
            return "
                <div class='$class'>
                    <span class='log-date'>{$matches["date"]}</span>
                    <span class='log-type'>{$matches["type"]}</span>
                    <span class='log-text'>{$matches["text"]}</span>
                </div>";
        }
        return "";
    }

    function read_logs(){
        global $log, $alert;

        $handle = @fopen($log->filename, 'r');
        if($handle === FALSE){
            $alert->e("Impossible de lire le fichier log : $log->filename");
            return;
        }

        $html = "";
        while(($line = fgets($handle)) !== FALSE){
            $html = log_html($line) . $html;
        }

        if(!feof($handle)){
            $alert->e("Erreur lors de la lecture du fichier log : $log->filename");
            return;
        }

        fclose($handle);

        return $html;
    }

?>

<div class="log-container">
    <?php echo read_logs(); ?>
</div>
