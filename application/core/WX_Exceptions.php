<?php


class WX_Exceptions extends CI_Exceptions {

    public function show_webox_error($heading, $error_object, $template = 'error_general', $status_code = 500, $last_query = '') {
        if ($error_object) {
            $error_output = array();
            
            //Trace prep for $message_output
            $separator = '<br>#';
            $trace = str_replace("#", $separator, $error_object->getTraceAsString());
            
            $error_output['message'] = "Error Message:" . $error_object->getMessage(); 
            $error_output['file']= '<strong>In File: </strong>  ' . $error_object->getFile();
            $error_output['line']='<strong>In Line: </strong> ' . $error_object->getLine();
            if ($error_object->getCode()){
                set_status_header($error_object->getCode());
                $error_output['error_code']='<strong>Error Code: <strong>' . $error_object->getCode();
            } 
            // if $last_query is set
            if ($last_query != '') {
                $error_output['last_query'] .= " Last query : " . $last_query . "<br /><br />";
            }

            $error_output['trace'] = "<strong>[Trace]</strong> <br>" . $trace;
            

            if (ENVIRONMENT != 'development') {
                //array to string to to assign log_message()
                $message = '<p>'.implode('</p><p>', $error_output).'</p>';
                log_message('Error', 'Webox Generated Error ==> ' . $message);
            } else {
                //assign $error_input to message
                $message = $error_output;
                return $this->show_error($heading, $message, $template, $status_code);
            }
        }
    } 
}
