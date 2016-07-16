<?php

    class Alert {

        var $error_list;
        var $info_list;
        var $warning_list;
        var $success_list;

        function Alert(){
            $this->error_list = [];
            $this->info_list = [];
            $this->warning_list = [];
            $this->success_list = [];
        }

        public function add_success($message){
            $this->success_list[] = $message;
        }

        public function add_error($message){
            $this->error_list[] = $message;
        }

        public function add_info($message){
            $this->info_list[] = $message;
        }

        public function add_warning($message){
            $this->warning_list[] = $message;
        }

        private function html($type, $message){
            return
                "<div class='$type fade in'>
                    <a hred='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    $message
                </div>";
        }

        public function html_error($message){
            return $this->html("alert alert-danger", $message);
        }

        public function html_success($message){
            return $this->html("alert alert-success", $message);
        }

        public function html_warning($message){
            return $this->html("alert alert-warning", $message);
        }

        public function html_info($message){
            return $this->html("alert alert-info", $message);
        }

        public function html_info_all(){
            $s = "";
            foreach($this->info_list as $i)
                $s .= $this->html_info($i);
            return $s;
        }

        public function html_success_all(){
            $s = "";
            foreach($this->success_list as $i)
                $s .= $this->html_success($i);
            return $s;
        }

        public function html_warning_all(){
            $s = "";
            foreach($this->warning_list as $i)
                $s .= $this->html_warning($i);
            return $s;
        }

        public function html_error_all(){
            $s = "";
            foreach($this->error_list as $i)
                $s .= $this->html_error($i);
            return $s;
        }

        public function html_all(){
            return
                $this->html_error_all() .
                $this->html_warning_all() .
                $this->html_info_all() .
                $this->html_success_all();
        }
    }

?>
