<?php

class News_model extends database_model {

    protected static $table_name = "news";
    public static $db_fields = array('id', 'title', 'slug'=>array('null'), 'text','date');
    public $id;
    public $title;
    public $slug;
    public $date;
    public $text;

    //public $show_them;
//      public function __construct() {
//        parent::__construct();
//                $this->id = "check";
//    }

 
        
      


    public function show_them() {
        if (isset($this->title) && isset($this->text)) {
            return 'News Title: ' . $this->title . '  ' . '<br>News Text: ' . $this->text;
        }
    }
    
    public function show_date() {
        if (isset($this->date) && isset($this->date)) {
            return 'News date: ' . $this->date;
        }
    }
    
     public function text($value) {
        $this->text = $value;
    }
    
     public function show_text() {
       return $this->text;
    }

    //public static $database_instance;
    //public function __construct() {
    //self::$database_instance = $this->load->database();
    // parent::__construct(self::$table_name);
    //}
//    public function get_news($slug = FALSE) {
//        if ($slug === FALSE) {
//            $query = $this->db->get('news');
//            return $query->result_array();
//        }
//
//        $query = $this->db->get_where('news', array('slug' => $slug));
//        return $query->row_array();
//    }
}
