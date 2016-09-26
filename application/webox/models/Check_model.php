<?php

class check_model extends database_model {
     public function __construct() {
        parent::__construct();
        $this->load->database();
    }
}

