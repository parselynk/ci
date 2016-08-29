<?php

class WX_user_model extends database_model {

   // public $status;
    //public $roles;
    
    protected static $table_name = "users";
    /**
     *
     * database fields
     * 
     * database reference to match database and class.
     * 
     * @property array  
     */
    
    public static $db_fields = array(
                                     'email', 
                                'first_name', 
                                 'last_name', 
                                      'role',  
                                  'password',
                                'last_login',
                                    'status'
                                );
    
    public $email = "check";
    public $first_name;
    public $last_name;
    public $role;
    public $password;
    public $last_login;
    public $status;
    public static $table_name = "users";

    public function __construct() {
        // Call the Model constructor
        parent::__construct();        
        
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');
    }
    
    public function is_authorized(){
        if( !empty($this->session->userdata('email'))){
            echo $this->session->userdata('email');
        }else{
            echo "Your are not authorized please Log in first";die;
        }
    }
    
    public function insertUser($post) {
        $user_data = $this->input->post(array('firstname', 'lastname','email'), TRUE);
        $user_data['role'] = $this->roles[0];
        $user_data['status'] = $this->status[0];
        
//        $string = array(
//            'first_name' => $d['firstname'],
//            'last_name' => $d['lastname'],
//            'email' => $d['email'],
//            'role' => $this->roles[0],
//            'status' => $this->status[0]
//        );
        
        
        $q = $this->db->insert_string('users', $string);
        $this->db->query($q);
        return $this->db->insert_id();
    }
}

