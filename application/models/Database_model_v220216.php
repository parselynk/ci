<?php

class database_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function test($name, $value) {
        if (in_array($name, static::$db_fields)) {
            return get_class_methods($this);
        } else {
            throw new Exception('Property Not Found');
        }
    }

    
    /**
	 * Set attributes of called class (public, private, protected)
	 *
         * this method helps to set object attributes even the private ones.
         * this method will be called after fetching data into particular class' object
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
        if (in_array($name, static::$db_fields) && property_exists(get_called_class(),$name)) {
                $this->$name = $value;
                
        }else{
            throw new Exception("Property($name) is not allowed");
        }
        return $this;
    }
    /**
	 * Fetch all rows from static::$table_name and insert into objects of get_called_class() 
         *   
	 * @return	objects of called class
	 */
    public function get_all() {
//        $query = $this->db->get(static::$table_name);
//        return $query->custom_result_object(get_called_class());
        return self::result($this->db->get(static::$table_name), get_called_class());
    }
    /**
	 * Fetch one row from static::$table_name where id=$id and inserts into object of get_called_class()
	 *
         * this method fethces one row only
         *   
	 * @param	array   $params	where clause parameters
	 * @param	string	$result_set   result_set amount (all, row)
	 * @return	object(s) of called class
	 */
    public function get_by_id($id = null) {
        if ($id) {
            return self::result($this->db->get_where(static::$table_name, ['id' => $id], 1), get_called_class(),'row');
        }else{
            throw new Exception("No id is passed as parameter");
        }
    }
    /**
	 * Fetch all/one row(s) from static::$table_name and insert into object of get_called_class()
	 *
         * this method fethces one row on default
         * $params passed to this method must be array 
         *   
	 * @param	array   $params	where clause parameters
	 * @param	string	$result_set   result_set amount (all, row)
	 * @return	object(s) of called class
	 */
    public function get_by($params, $result_set='row') {
        if ($params) {
            if(is_array($params)){
                return self::result($this->db->get_where(static::$table_name, $params), get_called_class(),$result_set);
                //var_export( $query);die;
            }else{
                throw new Exception("parameters in get_by() must be passed as an array<br> sql: " .$this->db->get_compiled_select());
            }
        }else{
                throw new Exception("No parameter is set for get_by() <br> sql: " .$this->db->get_compiled_select());
        }
    }
    /**
	 * Generate query result
	 *
         * this method on default generates result set as object
         * this method by default provides all rows
         *   
	 * @param	array   $params	where clause parameters
	 * @param	string	$result_set   result_set amount (all, row)
	 * @return	Genrated result set (std_object, custom object or array)
	 */
    public static function result($query, $type = 'object', $result_set='all') {
        if($result_set === 'all'){
            if ($type === 'array') {
                return $query->result_array();
            } elseif ($type === 'object') {
                return $query->result_object();
            } else {
                return $query->custom_result_object($type);
            }
        }else{
            if ($type === 'array') {
                return $query->row_array();
            } elseif ($type === 'object') {
                return $query->row();
            } else {
                return $query->custom_row_object(0, $type);
            }
        }
    }

}
