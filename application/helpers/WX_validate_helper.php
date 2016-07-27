<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// Throws Exception if any invalid parameter (E.g. $parameter, $field, $comparison_operator)
// todo : check for empty parameters add one more element to array
// add $comparison operator stuff 
function validate() {
    $numargs = func_num_args();
    //echo $numargs;
    $paramter_list = func_get_args();
    $trace_message = get_calling_function();
    $db_fields = isset($trace_message['object']) ? $trace_message['object']::$db_fields : array();
    if ($numargs > 0) { // check if parameter is passed
        switch ($numargs) {
            case 1:
                $field = $paramter_list[$numargs - 1];
                $is_valid = validate_field($field, $db_fields, $trace_message);
                break;
            case 2:
                $field = $paramter_list[$numargs - 2]; // in this case field is only one 
                $parameters = $paramter_list[$numargs - 1];
                $is_valid = validate_field($field, $db_fields, $trace_message);
                $is_valid = validate_parameter($parameters, $trace_message,$field,$db_fields);
                break;
            case 3:
                break;
            default:
            // if $numargs = 0 means no field is assigned to validate
                throw new Exception("Model Validation:No Field is Assigned to query ". $trace_message['message']);
        }
        return $is_valid;
    }
}

    function validate_field($field, $db_fields, $trace_message) {
        if (is_array($field)) {
            $is_valid = validate_field_parameter_array($field, $db_fields, $trace_message);
        } else {
            $is_valid = is_valid($field, $db_fields, $trace_message);
        }

        return $is_valid;
    }

//foreach($parameterz as $field=>$parameter){
//	if (array_key_exists($field, $array2)) {
//    	echo "The '$field' element is in the array";
//		if (is_array([$array[$field]]) && in_array("null",$array[$field])){
//			echo "no need to check";
//		}else{
//			echo (empty($parameter))?"{$field} is empty": "goot to go";
//		}
//	}
//}
    function is_valid($field, $db_fields, $trace_message) {
        if (in_array($field, $db_fields)) {
            return true;
        }
        if (array_key_exists($field, $db_fields)) {
            return true;
        }

        $object = get_class($trace_message['object']);
        $message = ($field != '') ? "<strong>\"$field\"</strong> is not found in <strong>{$object}</strong> \$db_fields" : "EMPTY Filed is passed to query";
        throw new Exception("Model Validation: $message " . $trace_message['message'], 406);
    }

//checks if parameter_array includes correct(not empty) $values 
    function validate_parameter_array($param_array, $trace_message) {
        //var_dump($param_array);
        foreach ($param_array as $key => $value) {
            // $values cannot be empty for parameters
            if (!isset($value) || $value == '') {
                throw new Exception("Model Validation: Empty Parameter(s) found in <strong>parameter array</strong> " . $trace_message['message']);
            }
        }
        return true;
    }

    function validate_field_parameter_array($field_parameter_array, $allowed_db_fields, $trace_message) {
        foreach ($field_parameter_array as $field => $parameter) {
            if (is_valid($field, $allowed_db_fields, $trace_message)) {    // $values cannot be empty for parameters
                    validate_parameter($parameter,$trace_message,$field,$allowed_db_fields);
            }
        }
        return true;
    }

// for validate_field_parameter_array function use
    function validate_parameter($parameter, $trace_message, $field, $allowed_db_fields="") {
         //var_dump($field);
                    if(isset($parameter)){

            if(key_exists($field, $allowed_db_fields) && (is_array($allowed_db_fields[$field]) && in_array("null",$allowed_db_fields[$field])) &&  !is_array($parameter)){
                //echo is_array($allowed_db_fields[$field]);
                               // var_dump($allowed_db_fields[$field]);die;
                return true;
            }
     
            // only from validate_field_parameter_array
            if (is_array($parameter)) {
                //checks if array of parameters are 
                //not including empty $value for each $key=>$value
                $is_valid = validate_parameter_array($parameter, $trace_message);
            }
            if(isset($parameter) && $parameter !=''){
                $is_valid = true;
            } else{
                throw new Exception("Model Validation: no parameter is passed to <strong>\"$field\"</strong> field" . $trace_message['message']); 
            }
       }
        return $is_valid;
    
    }

function get_calling_function() {

    $backtrace = debug_backtrace();
    $called = $backtrace[1];
    $caller = $backtrace[2];
    $response['message'] = '';
    //var_dump($called);
    if (isset($caller['class'])) {
        $response['message'] .= '<br> <strong>Error Internal Trace: </strong>  <strong>' . $caller['class'];
        $response['class'] = $caller['class'];
    }
    if (isset($caller['object'])) {
        $response['message'] .= '((object)' . get_class($caller['object']) . ')->';
        $response['object'] = $caller['object'];
    }
    $response['message'] .= $caller['function'] . '()&nbsp{' . $called['function'] . '}</strong> in file: ' . $called['file'] . ' [line: ' . $called['line'] . ']';
    return $response;
}
