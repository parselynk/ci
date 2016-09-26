<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class WX_template {

    var $ci;

    function __construct() {
        $this->ci = & get_instance();
    }

    /**
     * 
     * load
     * 
     * finds template & content in all paths and load them
     * 
     * @param String $tpl_view [template view]
     * @param string $module [module]
     * @param String $body_view [view content]
     * @param Array $data [content data]
     */
    
    function load($tpl_view, $module = null, $body_view = null, $data = null) {
        //echo "view: {$tpl_view} - module: {$module} - content: {$body_view} ";
        // looks for content file in different directories within modules or in root directory.
        if (!is_null($body_view)) {
            if (file_exists(APPPATH . $module . '/views/' . $tpl_view . '/' . $body_view)) {
                $body_view_path = $tpl_view . '/' . $body_view;
            } else if (file_exists(APPPATH . $module . '/views/' . $tpl_view . '/' . $body_view . '.php')) {
                $body_view_path = $tpl_view . '/' . $body_view . '.php';
            } else if (file_exists(APPPATH . $module . '/views/' . $body_view)) {
                $body_view_path = $body_view;
            } else if (file_exists(APPPATH . $module . '/views/' . $body_view . '.php')) {
                $body_view_path = $body_view . '.php';
            } else {
                show_error('Unable to load the requested file: ' . APPPATH .'views/' . $body_view . '.php');
            }
            
            //sets data for body and assign it to $body
            $body = $this->ci->load->view($body_view_path, $data, TRUE);
            
            //assigns $body to $data whether its null, array or object
            
            if (is_null($data)) {
                $data = array('body' => $body);
            } else if (is_array($data)) {
                $data['body'] = $body;
            } else if (is_object($data)) {
                $data->body = $body;
            }
            
                            //print($data['body']);die("");

        }
        
        // loads template with its content which is assigned to it as $data
        $this->ci->load->view('templates/' . $tpl_view, $data);
    }

}
