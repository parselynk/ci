<?php

class Main extends CI_Controller {

    public $status;
    public $roles;

    function __construct() {
        parent::__construct();
        $this->load->model('Database_model');
        $this->load->library('session', 'WX_password');
        $this->load->model('Authorize');
        $this->load->model('User_model', 'user_model', TRUE);
        $this->load->helper('WX_validate_helper');
        $this->load->helper('url_helper');
        $this->load->helper('html');
        $this->load->helper('WX_require_header_helper');
        $this->load->library('form_validation');
        $this->load->library('WX_password');
        $this->load->model('Token_model', 'token');

        
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        $this->status = $this->config->item('status');
        $this->roles = $this->config->item('roles');
        // var_dump($this->session);die;
    }

    public function index() {
        /**
         *  checks if user is logged in and user_email is set
         * 
         */
        
        if (!Authorize::is_logged_in()) {
            redirect(site_url() . '/main/login/');
        }
        /* front page */
                         var_dump(Authorize::expose_session());
                         Authorize::is_logged_in();

        $data = $this->session->userdata;
        $this->load->view('templates/user/header');
        $this->load->view('user/index', $data);
        $this->load->view('templates/user/footer');
    }

    public function register() {

        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/user/header');
            $this->load->view('user/register');
            $this->load->view('templates/user/footer');
        } else {
            try{
                if ($this->user_model->is_duplicate($this->input->post('email'))) {
                    $this->session->set_flashdata('flash_message', 'User email already exists');
                    redirect(site_url() . 'main/register');
                } else {
                    $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));
                    $clean['status'] = $this->user_model->status[1];
                    $clean['role']= $this->user_model->roles[0];
                    $result = $this->user_model->set_data($clean)->save()->get_response();

                    $user_id = $result['success'] == true ? $result['inserted_id']:null;
                    $token = $this->token->insert_token($user_id);

                    $qstring = base64_encode($token);
                    $url = site_url() . 'main/complete/token/' . $qstring;
                    $link = '<a href="' . $url . '">' . $url . '</a>';

                    $message = '';
                    $message .= '<strong>You have signed up with our website</strong><br>';
                    $message .= '<strong>Please click:</strong> ' . $link;

                    echo $message; //send this in email
                    exit;
                }
            }catch(Exception $ex){
                show_webox_error($ex, '', 'Webox Error Message', $this->user_model->get_last_query());
            }
        }
    }

    public function login() {

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/user/header');
            $this->load->view('user/login');
            $this->load->view('templates/user/footer');
        } else {

            $post = $this->input->post();
            $clean_post = $this->security->xss_clean($post);
            //$this->user_model->set_data($clean_post);
            $userInfo = $this->user_model->check_login($clean_post)->get_response(false, false);
            if ($userInfo['success'] !== true) {
                $this->session->set_flashdata('flash_message', 'The login was unsucessful');
                redirect(site_url() . 'main/login');
            }
            foreach ($userInfo['data'] as $key => $val) {
                
                // CI session set_userdata
                //$this->session->set_userdata($key, $val);
                
                // Webox session set_userdata
                Authorize::set_userdata($key, $val);
            }
            Authorize::set_userdata('logged_in', true);

            redirect(site_url() . 'main/');
        }
    }

    public function complete() {
        $token = base64_decode($this->uri->segment(4));
        $cleanToken = $this->security->xss_clean($token);
        $user_info = $this->user_model->verify_user($cleanToken); //either false or array();           
        //var_dump($user_info);die;
        if (!$user_info) {
            $this->session->set_flashdata('flash_message', 'Token is invalid or expired');
            redirect(site_url() . 'main/login');
        }
        $data = array(
            'firstName' => $user_info->first_name,
            'email' => $user_info->email,
            'user_id' => $user_info->id,
            'token' => base64_encode($token)
        );

        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/user/header');
            $this->load->view('user/complete', $data);
            $this->load->view('templates/user/footer');
        } else {

            $post = $this->input->post(NULL, TRUE);
            $cleanPost = $this->security->xss_clean($post);

            $hashed = $this->wx_password->create_hash($cleanPost['password']);

            $cleanPost['password'] = $hashed;
            unset($cleanPost['passconf']);
            $data = get_object_vars($user_info);
            $data['password'] = $hashed;
            $data['last_login'] = date('Y-m-d h:i:s A');
            $data['status'] = $data['status'][1];
            $data['role'] = $data['role'][0];
                  $data['status'] = $this->user_model->status[0];
                $data['role']= $this->user_model->roles[0];
            
            $userInfo = $this->user_model->set_data($data)->update_user_info();
            
            if (!$userInfo) {
                $this->session->set_flashdata('flash_message', 'There was a problem updating your record');
                redirect(site_url() . 'main/login');
            }

            unset($userInfo->password);

            foreach ($userInfo as $key => $val) {
                
                // CI session set_userdata
                //$this->session->set_userdata($key, $val);
                
                // Webox session set_userdata
                Authorize::set_userdata($key, $val);

            }

            redirect(site_url() . 'main/');
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect(site_url() . 'main/login/');
    }

    public function forgot() {

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/user/header');
            $this->load->view('user/forgot');
            $this->load->view('templates/user/footer');
        } else {
            $email = $this->input->post('email');
            $clean = $this->security->xss_clean($email);
            $userInfo = $this->user_model->select_where('email', "=" , $clean)->get_response(true);
            if (!$userInfo) {
                $this->session->set_flashdata('flash_message', 'We cant find your email address');
                redirect(site_url() . 'main/login');
            }

            if ($userInfo->status != $this->user_model->status[1]) { //if status is not approved
                $this->session->set_flashdata('flash_message', 'Your account is not in approved status');
                redirect(site_url() . 'main/login');
            }

            //build token 

            $token = $this->token->insert_token($userInfo->id);
            $qstring = base64_encode($token);
            $url = site_url() . 'main/reset_password/token/' . $qstring;
            $link = '<a href="' . $url . '">' . $url . '</a>';

            $message = '';
            $message .= '<strong>A password reset has been requested for this email account</strong><br>';
            $message .= '<strong>Please click:</strong> ' . $link;

            echo $message; //send this through mail
            exit;
        }
    }

    public function reset_password() {
        $token = base64_decode($this->uri->segment(4));
        $cleanToken = $this->security->xss_clean($token);

        $user_info = $this->user_model->verify_user($cleanToken); //either false or array();               

        if (!$user_info) {
            $this->session->set_flashdata('flash_message', 'Token is invalid or expired');
            redirect(site_url() . 'main/login');
        }
        $data = array(
            'firstName' => $user_info->first_name,
            'email' => $user_info->email,
            'user_id' => $user_info->id,
            'token' => base64_encode($token)
        );

        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/user/header');
            $this->load->view('user/reset_password', $data);
            $this->load->view('templates/user/footer');
        } else {

           // $this->load->library('password');
            $post = $this->input->post(NULL, TRUE);
            $cleanPost = $this->security->xss_clean($post);
            $hashed = $this->wx_password->create_hash($cleanPost['password']);
            $cleanPost['password'] = $hashed;
            unset($cleanPost['passconf']);
            $data = get_object_vars($user_info);
            $data['password'] = $hashed;
            $data['last_login'] = date('Y-m-d h:i:s A');
            
                                                              
            
            $update_response = $this->user_model->set_data($data)->update_user_info();
            if (! $update_response) {
                $this->session->set_flashdata('flash_message', 'There was a problem updating your password');
            } else {
                $this->session->set_flashdata('flash_message', 'Your password has been updated. You may now login');
            }
            redirect(site_url() . 'main/login');
        }
    }

}
