<?php

if ( !defined('BASEPATH')) exit('No direct script access allowed');

class Hmvc extends CI_Controller {
       public function __construct() {
        parent::__construct();

        $this->load->model('webox_core/Database_model');
        $this->load->model('webox_core/news_model');

        $this->load->library('session');
        $this->session->userdata('item');
        $newdata = array(
        'email'  => 'johndoe'
        );

       // $this->session->set_userdata($newdata);
        //$this->load->model('users/WX_user_model', 'user_model');

        $this->load->helper('url_helper');
        $this->load->helper('WX_validate_helper');
        $this->load->library('WX_template');
    }
    public function index() {
       $this->news_model->select_all();
       $data['news'] = $this->news_model->get_response(); 
        
       $data['title'] = "user Login UI";
        $this->load->view('index', $data);
               $this->load->view('/templates/header');

        $this->wx_template->load('default','webox/hmvc', 'content', $data);
                $this->load->view('/templates/footer');

    }
}
 

