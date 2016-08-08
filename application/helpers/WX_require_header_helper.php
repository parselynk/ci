<?php

defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('insert_header')){
    /**
	 * insert_header
	 *
	 * Adds JavaScript and CSS files to CI config. 
	 *
	 * @param	string | array	$file
	 * @param	string $type
	 * @return	void
	 */
    function insert_header($file= null , $type = null)
    {
        $valid_types = ['css','js'];
        $str = '';
        $ci = &get_instance();
        if(isset($type) && in_array($type, $valid_types))
        $header  = $ci->config->item($type."_headers");
        $header_name = "header_".$type;
        if(empty($file)){
            return;
        }
 
        if(is_array($file)){
            if(!is_array($file) && count($file) <= 0){
                return;
            }
            foreach($file as $item){
                $header[] = $item;
            }
            $ci->config->set_item($header_name,$header);
        }else{
            $header[] = $file;
            $ci->config->set_item($header_name,$header);
        }
    }
}
 
 
if(!function_exists('require_headers')){
    /**
	 * require_headers
	 *
	 * Loads JavaScript and CSS files in header. 
	 *
	 * @return	string
	 */
    function require_headers()
    {
        $headers = "\n";
        $ci = &get_instance();
        $css_headers = $ci->config->item('css_headers');
        $js_headers = $ci->config->item('js_headers');
        if (!empty($css_headers)){

            foreach($css_headers as $item){
                echo 
                $headers .= link_tag('css/'.$item)."\n";
                
            }
        }
        if (!empty($js_headers)){
            foreach($js_headers as $item){
                $headers .= '<script type="text/javascript" src="'.base_url().'js/'.$item.'"></script>'."\n";
            }
         } 

        return $headers;
    }
}

