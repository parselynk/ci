<?php

class database_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {

            $query = $this->db->get(static::$table_name);
            return $query->result();
        
    }
    public function get_by_id($id = null) {
        if($id){
            //var_export(static::$db_fields);
            $query = $this->db->get_where(static::$table_name, ['id' => $id], 1);
            if ($query->num_rows() > 0)
            {
                //return $query->row();
                //$row = $query->custom_row_object(0,get_called_class());
                //$row = $query->row_array();
                //die($row->show_them());
                //return $row;
                $object_array = array();
                $row = $query->result_array();
                //var_dump($row);die;
//                foreach($row as $key){
//                    var_export(self::instantiate($key));
//                    
//                }die;
//                while ($row = $query->row()) {
//                    $object_array[] = self::instantiate($row);
//                }
                foreach($row as $key){
                    $object_array[] = self::instantiate($key);
                }
                return array_shift($object_array);
            }
        }
    }
    
    private static function instantiate($record) {
		// Could check that $record exists and is an array
        //echo "initiate " . $id++  ;
            $class_name = get_called_class();
            $object = new $class_name;
    
            foreach($record as $attribute=>$value){
                if($object->has_attribute($attribute)) {
                    $object->$attribute = $value;
                }
            }
            return $object;
	}
	
	
	private function has_attribute($attribute) {
	  // get_object_vars returns an associative array with all attributes 
	  // (incl. private ones!) as the keys and their current values as the value
	  $object_vars = $this->attributes();
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $object_vars);
	}

	protected function attributes(){
            $attributes = array();
            foreach(static::$db_fields as $field) {
                if(property_exists($this, $field)) {
                    $attributes[$field] = $this->$field;
                }
            }
            return $attributes;
        }
    

}
