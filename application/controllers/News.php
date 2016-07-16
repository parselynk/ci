<?php

class News extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('database_model');
        $this->load->model('news_model');
        $this->load->helper('url_helper');
        $this->load->helper('WX_validate_helper');
    }

    public function index() {
        $data['news'] = $this->news_model->select_all();


        //var_dump($data['news']);
        $data['title'] = 'News archive';

        $this->load->view('templates/header', $data);
        $this->load->view('news/index', $data);
        $this->load->view('templates/footer');
    }

    public function save() {
        try {
            $this->news_model->id = 105;
            $this->news_model->title = 'Title DB update 2 check';
            $this->news_model->text = 'We are checking update database';
            $this->news_model->slug = 'test';
            //$this->news_model->slugcv = 'test';
            $this->benchmark->mark('code_start');
            $data['news'] = $this->news_model->save();
            $this->benchmark->mark('code_end');
            echo $this->benchmark->elapsed_time('code_start', 'code_end');

            var_dump($data['news']->data());
            die;
        } catch (Exception $ex) {
            //echo error_message($ex,$this->news_model->last_query());
            //show_error( $ex->getTraceAsString(),100,'Custome Error Message');
            //set_status_header();
            show_webox_error($ex, '', 'Webox Error Message', $this->news_model->last_query());
        }



        //var_dump($data['news']);
        //$data['title'] = 'News archive';
        //$this->load->view('templates/header', $data);
        //$this->load->view('news/index', $data);
        //$this->load->view('templates/footer');
    }

    public function list_where($parameter, $field = "id", $operator = ">") {
        /* Like Exampll */
        //$data['news'] = $this->news_model->select_where($field, $operator, $parameter );
        /* Comparison Example */
        $parameter = '';
        try {
            $data['news'] = $this->news_model->select_where($field, $operator, $parameter);
        } catch (Exception $ex) {
            error_message($ex, $this->news_model->last_query());
            //show_error( $ex->getMessage(),100,'Webox Error Message');
        }
        echo $this->news_model->last_query();

        //var_dump($data['news']);
        //echo $this->db->get_compiled_select();
        $data['title'] = 'News archive';

        $this->load->view('templates/header', $data);
        $this->load->view('news/index', $data);
        $this->load->view('templates/footer');
    }

    public function list_in($parameter, $field = "id") {
        //creates an array from url's passed parameter using delimiters:[',' '_' '-']
        $parameter = preg_split("/(,|_|-)/", $parameter);
        try {
            $this->news_model->select_in($field, $parameter);
            $data['news'] = $this->news_model->data();
        } catch (Exception $ex) {
            //echo error_message($ex,$this->news_model->last_query());
            //show_error( $ex->getTraceAsString(),100,'Custome Error Message');
            //set_status_header();
            show_webox_error($ex, '', 'Webox Error Message', $this->news_model->last_query());
        }
        echo $this->news_model->last_query();

        $data['title'] = 'News archive';

        $this->load->view('templates/header', $data);
        $this->load->view('news/index', $data);
        $this->load->view('templates/footer');
    }

    public function list_between($min, $max, $field = 'id') {

        //generates array from $parameter
        //$parameter = preg_split( "/(,|_)/", $parameter );
        //var_dump($parameter);
        $parameter = [$min, $max];
        try {
            $data['news'] = $this->news_model->select_between($field, $parameter);
        } catch (Exception $ex) {
            error_message($ex, $this->news_model->last_query());
            //show_error( $ex->getTraceAsString(),100,'Custome Error Message');
        }
        echo $this->news_model->last_query();
        $data['title'] = 'News archive';

        $this->load->view('templates/header', $data);
        $this->load->view('news/index', $data);
        $this->load->view('templates/footer');
    }

    public function view_by_id($id = NULL) {
        // echo 'viw';die;
        $data['news_item'] = $this->news_model->select_by_id($id);
        $data['news_item']->date = strtotime('tomorrow');
        $segs = $this->uri->segment_array();
//var_dump($segs);
        $data['title'] = $data['news_item']->title;
        $this->load->view('templates/header', $data);
        $this->load->view('news/view', $data);
        $this->load->view('templates/footer');
    }

}
