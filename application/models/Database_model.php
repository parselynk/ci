<?php


class database_model extends CI_Model {

    private $_response = '';
    private $_last_query = '';
    private $_db_prefix = 'wx';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * __set 
     * 
     * sets fetched data into attributes of called class (public, private, protected)
     *
     * helps to set object attributes even the private ones.
     * this method will be called after fetching data into particular class' non-public attribute
     *   
     * @param	string  $name	Class attribute
     * @param	any	$value	value of attribute
     * @return	object
     */
    public function __set($name, $value) {
        /**
          $db::fields are most of the time gets skipped
          but property_exists() for sure checks if set attributes exist in called class
         */
        //to-do: use validate fopr checking db fields
        if (in_array($name, static::$db_fields) && property_exists(get_called_class(), $name)) {
            $this->$name = $value;
        } else {
            throw new Exception("Property($name) is not allowed");
        }
        return $this;
    }
    /**
     * set_data 
     * 
     * sets objects properties based on input data
     *
     * helps to set object attributes for save method.
     * this method will be called within save method.
     *   
     * @parameters	array of properties and values
     * @return	$this
     */
          public function set_data($parameters=[]) {
              //var_dump($this);die;
//          $properties = [
//              "title"=>"test set_properties",
//              "slug"=>"test set_properties test slug",
//              "text"=>"set_properties set_properties set_properties text",
//          ];
          $properties = $parameters;
          validate($properties);
          foreach ($properties as $property => $value) {
              if(property_exists($this, $property)){
                  $this->$property = $value;
                  unset($properties[$property]);
              }else{
                  $message = "'$property' is not a valid property for ".get_called_class();
                  throw new Exception($message);
              }
          }
         $this->_response = true;
         return $this; 
          
    }
    /**
     * data 
     * 
     * returns _response to the controller
     *   
     * @return	query result object
     */
    public function get_response($array_shift = false, $json_style_response = false) {
        $response = $this->_response;
        $response = ($json_style_response)? $response['data']: $response;
        if($array_shift){
            return array_shift($response);
        }
        return $response;
    }
    
    /**
     * data 
     * 
     * ser _response property from a sub-classes model
     *   
     * @return	this
     */
    public function set_response($parameters) {
        if(!empty($parameters)){
            $this->_last_query = $this->db->last_query();
            $this->_response = $parameters;
        } else {
            throw new Exception("No parameter is set");
        }
        return $this;
    }
    

    /**
     * last_query 
     * 
     * returns last query SQL for var_dump purpose oor debbuging 
     *   
     * @return	String
     */
    public function get_last_query() {
        return $this->_last_query;
    }

    /**
     * select_all
     * 
     * Fetch all rows from static::$table_name and insert into objects of get_called_class() 
     * @set       
     * @return	objects of class
     */
    public function select_all() {
        
//        $sql = 'select * from ';
//        $sql .= '' . static::$table_name . '';
        $sql = $this->db->get_compiled_select(static::$table_name);
        $this->_response = $this->select_query(($sql), get_called_class());
        return $this;
    }
    
    /**
     * affected_rows
     * 
     * Shows affected rows after running Query
     * @return	Integer
     */
    public function affected_rows() {

        return $this->db->affected_rows();
    }

    /**
     * get_by_id
     *
     * fethces one row (only) based on its $id
     *   
     * @param	integer   $id	where clause parameters
     * @return	object of custome class
     */
    public function select_by_id($id = null) {
        if ($id) {
//            $sql = 'select * from ';
//            $sql .= static::$table_name . ' ';
//            $sql .= 'WHERE 1 ';
//            $sql .= 'AND id = ? ';
            
            $sql = $this->db->where(array('id' => $id))->get_compiled_select(static::$table_name, false);
            
            //return self::result($this->db->get_where(static::$table_name, ['id' => $id], 1), get_called_class(),'row');
            $this->_response = $this->select_query($sql, get_called_class(), 'row', [$id]);
            return $this;
        } else {
            throw new Exception("No id is passed as parameter");
        }
    }

    public function select_where($field, $comparison_operator, $parameter,$select_fields='*') {
        $valid_operator = ['LIKE', '=', '>=', '<=', '>', '<', '!='];
        $comparison_operator = strtoupper($comparison_operator);
        if (!isset($comparison_operator) || !in_array($comparison_operator, $valid_operator)) {
            throw new Exception("Operator is missed or not allowed Operator:<strong> $comparison_operator</strong>");
        }
        validate($field);
               $this->db->select($select_fields);
            if($comparison_operator === 'LIKE'){
                $this->db->like($field,$parameter);
                $sql = $this->db->get_compiled_select(static::$table_name);
                $this->_response = $this->select_query($sql, get_called_class(), 'all');
            } else {
                $this->db->where($field,$parameter);
                $sql = $this->db->get_compiled_select(static::$table_name);
                $this->_response = $this->select_query($sql, get_called_class(), 'all', [$parameter]);
                
            }
            $this->_last_query = $this->db->last_query();
            return $this;

    }


    /**
     * select_in 
     * 
     * fetches query using IN clause 
     *
     * this method fethces all rows on default
     * $params passed to this method must be array 
     *   
     * @param	string    $field   database table to search
     * @param   any       $parameter  value to be find
     * @return	object(s) of called class
     */
    public function select_in($field, $parameter) {
        validate($field, $parameter);
        if (isset($field) && isset($parameter)) {
            if (is_array($parameter)) {
                 $this->db->where_in($field, $parameter);
                 $sql = $this->db->get_compiled_select(static::$table_name);
                 echo $sql;
                $this->_response = $this->select_query($sql, get_called_class(), 'all', [$parameter]);
            } else {
                throw new Exception("parameters in Query must be passed as an array<br>Current Parameter: $parameter", 101);
            }
            $this->_last_query = $this->db->last_query();
            return $this;
        } else {
            throw new Exception("Column or Parameter(s) missed field = $field  | parameter = $parameter Undefined ");
        }
    }

    /**
     * select_between
     * 
     * fetches query using BETWEEN clause
     * 
     *   
     * @param	string    $field   database table to search
     * @param	Number    $min     minimum value in range
     * @param   Number    $max     maximum value in range
     * @return	object(s) of called class
     */
    public function select_between($field, $parameter = []) {
        if (isset($field) && isset($parameter)) {

            validate($field, $parameter);
            $min = $parameter[0];
            $max = $parameter[1];
            if ($min > $max) {
                $temp_min = $min;
                $temp_max = $max;
                $min = $temp_max;
                $max = $temp_min;
            }
            $sql = 'select * from ';
            $sql .= static::$table_name . ' ';
            $sql .= 'WHERE 1 ';
            $sql .= 'AND ' . $field . ' ';
            $sql .= 'BETWEEN ? AND ? ';
            $this->_response = $this->select_query($sql, get_called_class(), 'all', [$min, $max]);

            echo $this->_last_query = $this->db->last_query();
            return $this;
        } else {
            throw new Exception("Column or Parameter(s) missed. field = $field | min = $min | max = $max", 100);
        }
    }
    
    /**
     * _get_calling_function
     * 
     * returns the method called the running method
     * 
     * @return	array
     */

    private function _get_calling_function() {

        $caller = debug_backtrace();
        $called = $caller[1];
        $caller = $caller[2];

        $response ['function'] = $caller['function'];
        $response['message'] = $caller['function'] . '()';
        if (isset($caller['class'])) {
            $response['message'] .= ' from ' . $caller['class'];
            $response['class'] = $caller['class'];
        }
        if (isset($caller['object'])) {
            $response['message'] .= '(' . get_class($caller['object']) . ') Class Called ' . $called['function'] . '()';
            $response['object'] = $caller['object'];
        }
        return $response;
    }

    protected function attributes() {
        $attributes = array();
        foreach (static::$db_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }

    protected function assigned_attributes() {
        global $database;
        $clean_attributes = array();
        // sanitize the values before submitting
        // Note: does not alter the actual value of each attribute
        foreach ($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $value;
        }
        return $clean_attributes;
    }

    /**
     * save 
     * 
     * inserts/updates data into database  
     *
     * [NOTE]
     * if the calling class has assigned id means its and update operation
     * and modify method will be called otherwise its insert operation and 
     * creat method will be called
     *   
     * @return	$this object
     */
    public function save() {
        //var_export($this);die;
        
        return isset($this->id) ? $this->modify() : $this->create();
    }

    /**
     * create 
     * 
     * inserts data into database  
     *
     *   
     * @return	$this object
     */
    public function create($generated_id = true) {
        
        if ($generated_id === true) {
            $this->id = $this->_generate_unique_id(static::$table_name);
        }
        if ($this->db->insert(static::$table_name, $this)) {

            $insert_id = isset($this->id) ? $this->id : $this->db->insert_id();
       
            $affected_rows = $this->db->affected_rows();
            $this->_response = ['success' => 'true',  'affected_rows' => $affected_rows,'inserted_id' => $insert_id];
        } else {
            $this->_response = ['success' => 'false'];
        }
        $this->_last_query = $this->db->last_query();
        return $this;
    }

    /**
     * modify 
     * 
     * updates existing data in database  
     *
     *   
     * @return	$this object
     */
    public function modify() {

        $this->db->where('id', $this->id);
        //[NOTE]
        //row gets modified only if "New" data is updated in columns 
        
        if ($this->db->update(static::$table_name, $this)) {
            $affected_rows = $this->db->affected_rows();
            $this->_response = ['success' => 'true', 'affected_rows' => $affected_rows];
        } else {
            $affected_rows = $this->db->affected_rows();
            $this->_response = ['success' => 'false', 'affected_rows' => $affected_rows];
        }
        
        echo $affected_rows;
        echo $this->_last_query = $this->db->last_query();
        return $this;
    }

    /**
     * remove 
     * 
     * remove rows from database based on column name and array of parameters  
     *
     *   
     * @return	$this object
     */
    public function remove($field, $parameter = []) {
        validate($field, $parameter);
        $this->db->where_in($field, $parameter);

        //[NOTE]
        //row gets removed only if "New" row is removed i 
        if ($success = $this->db->delete(static::$table_name)) {
            $affected_rows = $this->db->affected_rows();
            $this->_response = ['success' => 'true', 'affected_rows' => $affected_rows];
        } else {
            $affected_rows = $this->db->affected_rows();
            $this->_response = ['success' => 'false', 'affected_rows' => $affected_rows];
        }
        $this->_last_query = $this->db->last_query();
        return $this;
    }

    /**
     * select_query
     *
     * custome query fetch 
     * all other methods passed their prepared queries to this method to fetch results 
     * 
     * $params passed to this method must be array 
     * $class  accepts "array", "object" or "custome class name" as parameters. for more info 
     * refer to result() method
     *   
     * @param	sql     $query	
     * @param	string	$class prepare data for class 
     * @param   string  $_response_set set of result(s) (all, row)
     * @return	object(s) of custom class, std object or array
     */
    public function select_query($query, $class, $_response_set = 'all', $params = NULL) {
        $caller_fucntion = $this->_get_calling_function();
        if ($query) {
            //echo $query;die;
            $this->_last_query = "Not Compiled yet:: ". $query;
            if ($params) {
                if (is_array($params)) {
                    return self::result($this->db->query($query, $params), $class, $_response_set);
                } else {
                    throw new Exception("parameters in Query must be passed as an array<br> sql: " . $this->db->get_compiled_select());
                }//var_export( $query);die;
            } else if (!$params && $_response_set != "all") {
                throw new Exception("Where got parameters ???<br> sql: " . $this->db->get_compiled_select());
            } else {
                try {
                    
                    return self::result($this->db->query($query), $class, $_response_set);
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                }
            }
        } else {
            throw new Exception("Empty query <br> sql: " . $this->db->get_compiled_select());
        }
    }
    

    /**
     * result
     * 
     * Generate result_set for queries
     *
     * by default generates result set as object
     * by default provides all rows result_set
     *   
     * @param	object   $query  query object from DB class
     * @param	string	 $type   type of result_set: "object","array" or name of custome class
     * @return	string   $_response_set can be "all" results or a "row" of result 
     */
    public static function result($query, $type = 'object', $_response_set = 'all') {
        if ($_response_set === 'all') {
            if ($type === 'array') {
                return $query->result_array();
            } elseif ($type === 'object') {
                return $query->result_object();
            } else {
                return $query->custom_result_object($type);
            }
        } else {
            if ($type === 'array') {
                return $query->row_array();
            } elseif ($type === 'object') {
                return $query->row();
            } else {
                return $query->custom_row_object(0, $type);
            }
        }
    }
    
    /**
     * _generate_unique_id
     * 
     * Generates unique_id for each record in database
     *
     *   
     * @param	string   $prefix  table name
     * @param	boolean	 $more_entropy  TRUE, returns string with 23 characters. Default(FALSE),returns string with 13 characters
     * @return	string   
     */

    private function _generate_unique_id($prefix = "", $more_entropy = FALSE) {
        $prefix = strtoupper($prefix) . strtolower($prefix);
        $length = strlen($prefix) / 2;
        $key = '';
        for ($i = 0; $i < 4; $i++) {
            $key .= $prefix[(mt_rand(0, (strlen($prefix) - 1)))];
        }
        return substr(uniqid($key, $more_entropy), 0, 13);
    }

}
