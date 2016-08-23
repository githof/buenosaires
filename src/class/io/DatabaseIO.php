<?php

    interface DatabaseIO {

        public function get_table_name();
        public function get_same_values();
        public function result_from_db($row);
        public function values_into_db();
        public function pre_into_db();
        public function post_into_db();
    }

?>
