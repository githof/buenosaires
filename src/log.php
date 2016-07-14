<?php

    define("NONE", 0);
    define("ERROR", 1);
    define("WARNING", 2);
    define("INFO", 3);
    define("DEBUG", 4);

    class Log {

        var $output;
        var $level;

        function Log($filename = LOG_DEFAULT_OUTPUT, $lvl = LOG_DEFAULT_LEVEL){
            $this->set_file($filename);
            $this->level = $lvl;
        }

        private function set_file($filename){
            if(!$this->output = fopen($filename, 'a')){
                return false;
            }

            return true;
        }

        function close(){
            fclose($this->output);
        }

        private function write($lvl, $lvl_message, $message){
            if($this->level >= $lvl){
                $m = date("Y m d  H:i:s") . " [" . $lvl_message . "] " . $message . "\n";
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
