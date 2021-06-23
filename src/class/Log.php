<?php

    define("NONE", 0);
    define("ERROR", 1);
    define("WARNING", 2);
    define("INFO", 3);
    define("DEBUG", 4);

    class Log {

        var $level;
        var $logs;
        var $filename;

        function Log($filename = LOG_DEFAULT_OUTPUT, $lvl = LOG_DEFAULT_LEVEL){
            $this->filename = $filename;
            $this->level = $lvl;
            $this->logs = [];
        }

        public function write(){
            $lines = [];
            if($handle = fopen($this->filename, 'c+')){
                chmod($this->filename, 0776);
                if(!flock($handle,LOCK_EX))
                    fclose($handle);

                while(($line = fgets($handle, 4096)) !== FALSE){
                    $lines[] = $line;
                }
                ftruncate($handle, 0);
                rewind($handle);

                $this->logs = array_merge($lines, $this->logs);
                $this->logs = array_slice($this->logs, -LOG_LINES_MAX, LOG_LINES_MAX);

                foreach($this->logs as $line){
                    fwrite($handle, $line);
                }
                flock($handle,LOCK_UN);
                fclose($handle);
            }
        }

        private function add($lvl, $lvl_message, $message){
            global $account;

            if($this->level < $lvl)
                return;

            $email = "";
            if(isset($account, $account->is_connected) && $account->is_connected)
                $email = " ".$account->infos["email"]." >";

            $m = date("Y-m-d  H:i:s") . " [" . $lvl_message . "]" . $email ." ". $message . "\n";
            $this->logs[] = $m;
        }

        public function i($message){
            $this->add(INFO, "INFO", $message);
        }

        public function d($message){
            $this->add(DEBUG, "DEBUG", $message);
        }

        public function e($message){
            $this->add(ERROR, "ERROR", $message);
        }

        public function w($message){
            $this->add(WARNING, "WARNING", $message);
        }

    }
?>
