<?php

class ModelHelper {
    
    
    /**
     * stores instances of models
     */
    protected static $__instances;
    
    
    /**
     * adds an instance
     */
    protected static function __addInstance ($instance) {
        self::$__instances[get_class_name($instance)] = $instance;
        return $instance;
    }
    
    
    /**
     * gets an instance
     */
    protected static function __getInstance ($name) {
        return (isset(self::$__instances[$name])) ? self::$__instances[$name] : self::__addInstance($name);
    }
    
    
    /**
     * gets the table name
     */
    public static function getTableName ($name) {
        $model = is_string($name) ? self::__getInstance($name) : $name;
        return $model->table;
    }
    
    
    /**
     * gets the attribute list
     */
    public static function getAttributeList ($name) {
        $model = is_string($name) ? self::__getInstance($name) : $name;  
        return $model->attributes; 
    }
    
    
}