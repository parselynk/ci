<?php

defined('BASEPATH') OR exit('No direct script access allowed');
// Throws Exception if any invalid parameter (E.g. $parameter, $field, $comparison_operator)

function validate($field='',$param=''){
  
   $trace_message = get_calling_function();
   //var_dump($trace_message);die;
   $db_fields = isset($trace_message['object'])?$trace_message['object']::$db_fields:array();
   
   if(isset($field) && $field!='') {
       if(is_array($field)){
           validate_field_parameter_array($field,$db_fields,$trace_message);
           return true;
       }
       
       //checkes if field is available(valid) in class;
       is_valid($field, $db_fields,$trace_message);
       
       //note: only if $param is set as argument, parameters will be validated
            if(isset($param)){
       
                if(is_array($param)){
                    //checks if array of parameters are 
                    //not including empty $value for each $key=>$value
                    validate_parameter_array($param,$trace_message );
                }
            
                
            }else{
                throw new Exception("Model Validation: Empty Parameter ". $trace_message['message']);
            }
    }else{
        throw new Exception("Model Validation:No Field is Assigned to query ". $trace_message['message']);
    }
   
   return true;
}

//checks if fileds are available in class $db_fields
function is_valid($field, $db_fields,$trace_message){
    if(in_array($field, $db_fields)){
        return true;
    }else{
        $object = get_class($trace_message['object']);
        $message = ($field!='')? "<strong>\"$field\"</strong> is not found in <strong>{$object}</strong> \$db_fields": "EMPTY Filed is passed to query";
        throw new Exception("Model Validation: $message ". $trace_message['message'],406); 
    }
}

//checks if parameter_array includes correct(not empty) $values 
function validate_parameter_array($param_array,$trace_message ){
           foreach ($param_array as $key=>$value) {
               // $values cannot be empty for parameters
               if(!isset($value) || $value ==''){
                    throw new Exception("Model Validation: Empty Parameter(s) found in <strong>parameter array</strong> ". $trace_message['message']);
               }
           }   
           return true;
}

function validate_field_parameter_array($field_parameter_array,$allowed_db_fields,$trace_message ){
    foreach ($field_parameter_array as $field=>$parameter) {
        if(is_valid($field, $allowed_db_fields,$trace_message))  {    // $values cannot be empty for parameters
            if(!isset($parameter) || $parameter ==''){
                throw new Exception("Model Validation: Empty Parameter(s) found in <strong>parameter array</strong> ". $trace_message['message']);
            }
        }
    }
}

    function get_calling_function() {
   
        $caller = debug_backtrace();
        $called = $caller[1];
        $caller = $caller[2];
        $response['message']='';
        //var_dump($called);
        if (isset($caller['class'])) {
            $response['message'] .= '<br> <strong>Error Internal Trace: </strong>  <strong>' . $caller['class'];
            $response['class'] = $caller['class'];
        }
        if (isset($caller['object'])) {
            $response['message'] .= '((object)' . get_class($caller['object']) . ')->';
            $response['object'] = $caller['object'];
        }
        $response['message'] .= $caller['function'] . '()&nbsp{'.$called['function'].'}</strong> in file: '.$called['file'].' [line: '.$called['line'].']';
        return $response;
    }

