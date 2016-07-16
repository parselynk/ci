<?php

//function exception_error_handler($severity, $message, $file, $line) {
//    if (!(error_reporting() & $severity)) {
//        // This error code is not included in error_reporting
//        return;
//    }
//    throw new ErrorException($message, 0, $severity, $file, $line);
//}


function error_message($e, $last_query="") {
    if ($e) {
        $output = array();
        $trace = $e->getTraceAsString();
        $separator = '<br>#';
        $trace = str_replace("#", $separator, $e->getTraceAsString());
        $error_message = $trace;
        $error_message_body = "<div style='border:1px solid #990000;padding:5px 20px;margin:0 0 10px 0;'>";
        //print_r(array_reverse($trace));
        $error_message_body .= "<h3>CUSTOME ERROR MESSAGE</h3><hr>";
        $error_message_body .=  "<p><br> <strong>Error Message:</strong> " . $e->getMessage() . '<br/> <strong>In File:</strong> ' . $e->getFile() . '<br/> <strong>In Line:</strong> ' . $e->getLine() . '<br/> <strong>Error Code:</strong> ' . $e->getCode() . '</p>';
        $error_message = "Error Message:" . $e->getMessage() . ' <strong>In File:  ' . $e->getFile() . ' In Line:' . $e->getLine() . ' <strong>Error Code: ' . $e->getCode();
        $error_message = $e->getMessage();
        //die($error_message);
        if($last_query !=''){
            $error_message_body .=  " Last query : " . $last_query . "<br /><br />";
        }
        $error_message_body .=  "<strong>[Trace]</strong> <br>".$trace;
        $error_message_body .=  "</div>";
        if (ENVIRONMENT != 'development'){
            log_message('Error', 'Webox Generated Error==>'.$error_message);
        }else{
            return $error_message_body;
        }
        //exit;
    }
}

if ( ! function_exists('show_webox_error'))
{
	/**
	 * Error Handler
	 *
	 * This function lets us invoke the exception class and
	 * display errors using the standard error template located
	 * in application/views/errors/error_general.php
	 * This function will send the error page directly to the
	 * browser and exit.
	 *
	 * @param	string
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	function show_webox_error($error_object, $status_code = 500, $heading = 'A Webox Error Was Encountered',$last_query='')
	{
		$status_code = abs($status_code);
		if ($status_code < 100)
		{
			$exit_status = $status_code + 9; // 9 is EXIT__AUTO_MIN
			if ($exit_status > 125) // 125 is EXIT__AUTO_MAX
			{
				$exit_status = 1; // EXIT_ERROR
			}

			$status_code = 500;
		}
		else
		{
			$exit_status = 1; // EXIT_ERROR
		}


                $_error =& load_class('Exceptions', 'core');
		echo $_error->show_webox_error($heading, $error_object, 'error_general', $status_code);
		exit($exit_status);
	}
}

