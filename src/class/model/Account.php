<?php

    define("SESSION_ID", "id");

    class Account{

        var $is_connected;
        var $infos;

        public function Account(){
            $this->check_connected();
            $this->infos = [];

            if($this->is_connected)
                $this->get_infos();
        }

        public function check_connected(){
            $this->is_connected = isset($_SESSION[SESSION_ID]);
            return $this->is_connected;
        }

        public function connect(){
            global $mysqli, $log;

            if(isset($this->infos["email"]) && isset($this->infos["pwd"])){
                $email = $this->get_email();
                $pass = $this->get_password();
                $res = $mysqli->select("utilisateurs", "*", "email='$email' && pwd='$pass'");

                if($res->num_rows == 1){
                    $this->infos = $res->fetch_assoc();
                    $_SESSION[SESSION_ID] = $this->infos["id"];
                    $this->check_connected();

                    $log->i("user " . $this->get_full_name() . " connected");
                    return true;
                }
            }
            return false;
        }

        public function disconnect(){
            global $log;

            session_destroy();

            $this->info = [];
            $this->is_connected = false;

            $log->i("user " . $this->get_full_name() . " disconnected");
        }

        private function get_infos(){
            global $mysqli;
            $id = $_SESSION[SESSION_ID];

            $res = $mysqli->select("utilisateurs", "*", "id='$id'");

            if($res->num_rows != 1)
                return false;

            $this->infos = $res->fetch_assoc();
            return true;
        }

        public function exist_in_db(){
            global $mysqli;

            $email = $this->get_email();
            if($email == null)
                return false;

            $res = $mysqli->select("utilisateurs", ["id"], "email='$email'");

            return $res->num_rows != 0;
        }

        public function check_valid_infos(){
            return
                $this->get_email() != null &&
                $this->get_password() != null &&
                $this->get_prenom() != null &&
                $this->get_nom() != null;
        }

        public function add_into_db(){
            global $mysqli;

            if($this->is_connected)
                return false;

            if($this->exist_in_db())
                return false;

            if(!$this->check_valid_infos())
                return false;

            $this->set_date_inscription("now()");
            $this->set_rang(1);
            $res = $mysqli->insert("utilisateurs", $this->infos);

            return $res === true;
        }



        private function get($key){
            if(isset($this->infos[$key]))
                return $this->infos[$key];
            return null;
        }

        public function get_id(){
            return $this->get("id");
        }

        public function get_prenom(){
            return $this->get("prenom");
        }

        public function get_nom(){
            return $this->get("nom");
        }

        public function get_full_name(){
            return $this->get_prenom() . " " . $this->get_nom();
        }

        public function get_rang(){
            return $this->get("rang");
        }

        public function get_email(){
            return $this->get("email");
        }

        public function get_password(){
            return $this->get("pwd");
        }

        public function get_date_inscription(){
            return $this->get("date_inscr");
        }


        private function set($key, $value, $update_db = false){
            global $mysqli;

            if($this->is_connected && $update_db){
                if($mysqli->update("utilisateurs", [$key => $value]) === false)
                    return false;
            }

            $this->infos[$key] = $value;

            return true;
        }

        public function set_prenom($value, $update_db = false){
            return $this->set("prenom", $value, $update_db);
        }

        public function set_nom($value, $update_db = false){
            return $this->set("nom", $value, $update_db);
        }

        public function set_rang($value, $update_db = false){
            return $this->set("rang", $value, $update_db);
        }

        public function set_email($value, $update_db = false){
            return $this->set("email", $value, $update_db);
        }

        public function set_password($value, $update_db = false){
            return $this->set("pwd", $value, $update_db);
        }

        public function set_date_inscription($value, $update_db = false){
            return $this->set("date_inscr", $value, $update_db);
        }
    }

?>
