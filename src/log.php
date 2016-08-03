<?php

    define("NONE", 0);
    define("ERROR", 1);
    define("WARNING", 2);
    define("INFO", 3);
    define("DEBUG", 4);

    class Log {

        var $output;
        var $level;
        var $filename;

        function Log($filename = LOG_DEFAULT_OUTPUT, $lvl = LOG_DEFAULT_LEVEL){
            $this->set_file($filename);
            $this->level = $lvl;
        }

        private function set_file($filename){
            if(!$this->output = fopen($filename, 'a')){
                return false;
            }
            chmod($filename, 0776);
            $this->filename = $filename;
            return true;
        }

        function close(){
            fclose($this->output);
        }

        private function write($lvl, $lvl_message, $message){
            global $account;

            if($this->level >= $lvl){
                $email = "";
                if(isset($account, $account->is_connected) && $account->is_connected){
                    $email = " " . $account->infos["email"] . " >";
                }
                $m = date("Y m d  H:i:s") . " [" . $lvl_message . "]" . $email ." ". $message . "\n";
                fwrite($this->output, $m);
            }
        }

        public function i($message){
            $this->write(INFO, "INFO", $message);
        }

        public function d($message){
            $this->write(DEBUG, "DEBUG", $message);
        }

        public function e($message){
            $this->write(ERROR, "ERROR", $message);
        }

        public function w($message){
            $this->write(WARNING, "WARNING", $message);
        }

    }
?>
