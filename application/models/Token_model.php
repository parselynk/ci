<?php

class Token_model extends database_model{
    
    public $id;
    public $token;
    public $user_id;
    public $created;
    public static $table_name = "tokens";
    public static $db_fields = array(
                                        'id',
                                     'token', 
                                   'user_id', 
                                 'created'
                                );


    function __construct() {
        // Call the Model constructor
        parent::__construct();

    }
    
    public function isTokenValid($token) {
        //$q = $this->db->get_where('tokens', array('token' => $token), 1);
        $token_response = $this->select_where("token", "=", $token)->get_response(true);
        if ($this->db->affected_rows() > 0) {
            $token_issue_date = strtotime($token_response->created);
            $today = strtotime(date('Y-m-d'));
            if ($token_issue_date != $today) {

                return false;
            }
            return $token_response->user_id;
        } else {
            return false;
        }
    }
    
    public function insert_token($user_id) {
        $token = substr(sha1(rand()), 0, 30);
        $date = date('Y-m-d');

        $string = array(
            'token' => $token,
            'user_id' => $user_id,
            'created' => $date
        );
        
        $token_result = $this->set_data($string)->save()->get_response();
        if($token_result['success']){
            return $token;
        }
    }
    
}