<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// Throws Exception if any invalid parameter (E.g. $parameter, $field, $comparison_operator)
// todo : check for empty parameters add one more element to array
// add $comparison operator stuff 
function validate() {
    $number_of_arguments = func_num_args();
    //echo $numargs;
    $paramter_list = func_get_args();
    $trace_message = get_calling_function();
    $db_fields = isset($trace_message['object']) ? $trace_message['object']::$db_fields : array();
    if ($number_of_arguments > 0) { // check if parameter is passed
        switch ($number_of_arguments) {
            case 1:
                $field = $paramter_list[$number_of_arguments - 1];
                $is_valid = validate_field($field, $db_fields, $trace_message);
                break;
            case 2:
                $field = $paramter_list[$number_of_arguments - 2]; // in this case field is only one 
                $parameters = $paramter_list[$number_of_arguments - 1];
                $is_valid = validate_field($field, $db_fields, $trace_message);
                $is_valid = validate_parameter($parameters, $trace_message, $field, $db_fields);
                break;
            case 3:
                break;
            default:
                // if $number_of_arguments = 0 means no field is assigned to validate
                throw new Exception("Model Validation:No Field is Assigned to query " . $trace_message['message']);
        }
        return $is_valid;
    }
}

/**
 * 
 * validates field whether it is valid as array value or key
 * 
 * @param array || string $field
 * @param array || string $db_fields
 * @param array $trace_message
 * @return boolean
 */

function validate_field($field, $db_fields, $trace_message) {
    if (is_array($field)) {
        $is_valid = validate_field_parameter_array($field, $db_fields, $trace_message);
    } else {
        $is_valid = is_valid($field, $db_fields, $trace_message);
    }

    return $is_valid;
}
/**
 * checks if field is valid
 * 
 * @param array || string $field
 * @param array $db_fields
 * @param array $trace_message
 * @return boolean
 * @throws Exception
 */
function is_valid($field, $db_fields, $trace_message) {
    if (in_array($field, $db_fields)) { // field is an array value
        return true;
    }
    if (array_key_exists($field, $db_fields)) { // field is array key
        return true;
    }

    $object = get_class($trace_message['object']); // calling class
    $message = ($field != '') ? "<strong>\"$field\"</strong> is not found in <strong>{$object}</strong> \$db_fields" : "EMPTY Field is passed to query";
    throw new Exception("Model Validation: $message " . $trace_message['message'], 406);
}

/**
 * //checks if parameter_array includes correct(not empty) $values
 * 
 * @param type $param_array
 * @param type $trace_message
 * @return boolean
 * @throws Exception
 */
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

/**
 * checks validity of $field => $parameter in passed array 
 * 
 * @param array $field_parameter_array
 * @param array $allowed_db_fields
 * @param array $trace_message
 * @return boolean
 */
function validate_field_parameter_array($field_parameter_array, $allowed_db_fields, $trace_message) {
    foreach ($field_parameter_array as $field => $parameter) {
        if (is_valid($field, $allowed_db_fields, $trace_message)) {    // $values cannot be empty for parameters
            validate_parameter($parameter, $trace_message, $field, $allowed_db_fields);
        }
    }
    return true;
}

// for validate_field_parameter_array function use
function validate_parameter($parameter, $trace_message, $field, $allowed_db_fields = "") {
    //var_dump($field);
    if (isset($parameter)) {
        
        if (key_exists($field, $allowed_db_fields) && !is_array($parameter)) { // $filed is array key

            return true; // always return true in case field is array key
        }

        // only validate_field_parameter_array funtion passes $parameter as array
        if (is_array($parameter)) {
            //checks if array of parameters are 
            //not including empty $value for each $key=>$value (Not Null)
            $is_valid = validate_parameter_array($parameter, $trace_message);
        }
        if ($parameter != '') {
            $is_valid = true;
        } else {
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
