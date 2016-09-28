<?php

class User_model extends database_model {

    public $id;
    public $status;
    public $role;
    public static $db_fields = array(
                                        'id',
                                     'email', 
                                'first_name', 
                                 'last_name', 
                                      'role',  
                                  'password',
                                'last_login',
                                    'status'
                                );
    
    public $email;
    public $first_name;
    public $last_name;
    public $password;
    public $last_login;
    public static $table_name = "users";


    function __construct() {
        // Call the Model constructor
        parent::__construct();
        $this->status = empty($this->status)?$this->config->item('status'):$this->status;
        $this->role =  empty($this->role)? $this->config->item('roles'):$this->role;
        //$this->load->model('Token_model');
    }

    public function insertUser($d) {
        $string = array(
            'first_name' => $d['firstname'],
            'last_name' => $d['lastname'],
            'email' => $d['email'],
            'role' => $this->roles[0],
            'status' => $this->status[0]
        );
        $q = $this->db->insert_string('users', $string);
        $this->db->query($q);
        return $this->db->insert_id();
    }

    public function is_duplicate($email) {
        $duplicate = $this->select_where("email", "=", $email)->affected_rows();
        return $duplicate > 0 ? true : false;
    }

    public function insert_token($user_id) {
        $token = substr(sha1(rand()), 0, 30);
        $date = date('Y-m-d');

        $string = array(
            'token' => $token,
            'user_id' => $user_id,
            'created' => $date
        );
        
        $query = $this->db->insert_string('tokens', $string);
        $this->db->query($query);
        return $token;
    }

    public function verify_user($token) {
        //$q = $this->db->get_where('tokens', array('token' => $token), 1);
        if ($this->token->isTokenValid($token) !== false ){
    
            $user_id = $this->token->isTokenValid($token);
            //select_by_id
            $user_info = $this->get_user_info($user_id)->get_response();
            return $user_info;
        }
    }

    public function get_user_info($id) {
        $user_info = $this->select_by_id($id);
        //var_dump($user_info->get_response());die;
        //$q = $this->db->get_where('users', array('id' => $id), 1);
        if ($this->db->affected_rows() > 0) {
            return $user_info;
        } else {
            error_log('no user found getUserInfo(' . $id . ')');
            return false;
        }
    }

    public function update_user_info() {
   
//        $this->db->where('id', $post['user_id']);
//        $this->db->update('users', $data);
        $response = $this->save()->get_response();
        //$success = $this->db->affected_rows();

        if (!$response['success']) {
            error_log('Unable to updateUserInfo(' . $this->id . ')');
            return false;
        }

         return  $this->get_user_info($this->id);
    }

    public function check_login($post) {

       $userInfo = $this->select_where("email", "=", $post['email'])->get_response(true);
   
           if (!isset($userInfo) || !$this->wx_password->validate_password($post['password'], $userInfo->password)) {
                error_log('Unsuccessful login attempt(' . $post['email'] . ')');
                $response_array = ["success"=> false, "response_code"=>401, "response_message"=>"Un-Authorized User","data"=>""];
                $this->set_response($response_array);
                return $this;
            }

        $this->update_login_time($userInfo);
        unset($this->password);
        $response_array = ["success"=> true, "response_code"=>200, "response_message"=>"User Sign in","data"=> get_object_vars($this)];
        $this->set_response($response_array);
        return $this;
    }

    public function update_login_time($data_to_update) {
        $data_array_to_set = get_object_vars($data_to_update);
        $data_array_to_set['last_login'] = date('Y-m-d h:i:s A');
        $this->set_data($data_array_to_set);
        return $this->save(); // returns this
    }

    public function get_user_info_email($email) {
        $q = $this->db->get_where('users', array('email' => $email), 1);
        if ($this->db->affected_rows() > 0) {
            $row = $q->row();
            return $row;
        } else {
            error_log('no user found getUserInfo(' . $email . ')');
            return false;
        }
    }

//    public function updatePassword($post) {
//        $this->db->where('id', $post['user_id']);
//        $this->db->update('users', array('password' => $post['password']));
//        $success = $this->db->affected_rows();
//
//        if (!$success) {
//            error_log('Unable to updatePassword(' . $post['user_id'] . ')');
//            return false;
//        }
//        return true;
//    }

}
