<?php

class Utils {


    /**
     * Defines a constant
     *
     * @param String $name
     * @param String $value
     */
    public static function define ($name, $value) {
        if (!defined($name)) define($name, $value); 
    }
    
    
    
    /**
     * HTML safe string
     *
     * @param String $string
     * @return String safe string
     */
    public static function htmlsafe ($string) {
        return htmlspecialchars($string, ENT_QUOTES, "UTF-8");
    }
}