<?php

class Hmvc extends CI_Controller {
       public function __construct() {
        parent::__construct();
        $this->load->model('database_model');
        $this->load->model('news_model');
        $this->load->helper('url_helper');
        $this->load->helper('WX_validate_helper');
    }
    public function index() {
        $this->news_model->select_all();
        $data['news'] = $this->news_model->data(); 
        
       $data['message'] = "check this out";
       $this->load->view('view', $data);
    }
}
