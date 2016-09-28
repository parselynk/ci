<?php

class Authorize extends CI_Model {

    private static $session;
    private static $logged_in = false;

    function __construct() {
        parent::__construct();
        self::$session = get_instance()->session;

    }
    
    static public function get_session_item($item='') {
        return self::$session->$item;
    }
    
    static public function is_logged_in() {
        if (!self::$logged_in){
            $logged_in = self::$session->has_userdata('logged_in');
            $email = self::$session->has_userdata('email');
            if($logged_in && $email ){
                self::$logged_in = true;
            }
        }
        return self::$logged_in;
    }
    
    static public function can() {
        //return self::$session->$item;
    }
    
    static public function has_access() {
        //return self::$session->$item;
    }
    
    static public function access_level() {
        //return self::$session->$item;
    }
    
    static public function expose_session() {
        return self::$session->userdata();
    }
    
    static public function set_userdata() {
    $number_of_arguments = func_num_args();
        if ($number_of_arguments < 1 ){
            throw new Exception("No parameter is set for userdata");
        }
    $paramter_list = func_get_args();
        if($number_of_arguments == 1){
            if(!is_array($paramter_list) ){
                throw new Exception(" Parameters for setting SESSIONS must be array");
            } 
            self::$session->set_userdata($paramter_list[0]);
        } else{
            self::$session->set_userdata($paramter_list[0], $paramter_list[1]);
        }
        return true ;
    }
}
