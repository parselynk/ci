<?php

    if( !function_exists('longUniqueString')) {
            /**
          * Provides an extra long random unique String
          * 
          * @param $iterations (default 3)
          * @return String
          *          Random String
          */
         function longUniqueString($iterations = 3) {
             get_instance() -> load -> helper('string');
             $s = '';
             for($i = 0; $i < $iterations; $i++) {
                 $s .= random_string('unique');
             }

             return $s;
         }
    }