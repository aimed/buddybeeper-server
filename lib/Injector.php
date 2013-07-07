<?php

class Injector {


    /**
     * Stores attributes
     */
    public $_storage = array();
    
    
    
    /**
     * Stores wildcards
     */
    protected $_wildcards = array();
    
    
    
    
    /**
     * Constructor
     *
     * @param Array $initial Optional Data the object will be populated with
     */
    public function __construct ($initial = null) {
        $this->store($initial);
    }
    
    
    
    
    /**
     * Stores data
     *
     * @param Mixed $data Data to store.
     * @param String $key Optional If no key is passed, data will be merged into storage
     */
    public function store ($data, $key = null) {
        if ($key) 
        {
            $this->_storage[$key] = $data;
        } 
        elseif (!empty($data) && is_array($data)) 
        {
            $this->_storage = array_merge($this->_storage, $data);    
        }
    }

    
    
    
    /**
     * Getter Name
     *
     * @param String $attr
     * @return String Getter
     */
    protected function _getter ($attr) {
        return "get" . ucfirst($attr);
    }
    
    
    
    
    /**
     * Setter Name
     *
     * @param String $attr
     * @return String Setter
     */
    protected function _setter ($attr) {
        return "set" . ucfirst($attr);
    }
    
    
    
    
    /**
     * Attribute getter
     *
     * @param String $attr
     * @return Mixed Value 
     */
    public function __get ($attr) {
        if ($this->__isset($attr)) return $this->_storage[$attr];
     
        // has getter
        $getter = $this->_getter($attr);
        if (method_exists($this, $getter)) 
        {
            $this->_storage[$attr] = $this->{$getter}();
            return $this->__get($attr);
        }
                
        return null;
    }
    
    
    
    
    /**
     * Isset
     *
     * @param String $name
     */
    public function __isset ($name) {
        return isset($this->_storage[$name]) || array_key_exists($name, $this->_storage);
    }
    
    
    
    /**
     * Sets attributes
     *
     * @param String $key
     * @param Mixed $value
     * @return Mixed Value
     */
    public function __set ($key, $value) {
        $setter = $this->_setter($key);
        if (method_exists($this, $setter)) $value = $this->{$setter}($value);
        $this->_storage[$key] = $value;    
            
        return $value;
    }
    
    
    
    
    /**
     * Gets a list of attributes
     *
     * @return Array Attributes
     */
    public function get () {
        $args = func_get_args();
        if(is_array($args[0])) $args = $args[0];
        foreach ($args as $attr) {
            $data[$attr] = $this->{$attr};
        }
        
        return $data;
    }
    
    
    
    
    /**
     * Registers a wildcard function
     *
     * @param String $name
     */
    public function registerWildcardFunction ($name) {
        $this->_wildcards[] = $name;
    }
    
    
    
    
    /**
     * Calls wildcard functions
     *
     * @param String $name
     * @param Array $args
     */
    public function __call ($name, $args) {
        foreach ($this->_wildcards as $wildcard) {
            if (strpos($name, $wildcard) === 0) 
                return $this->{$wildcard}(substr($name, strlen($wildcard)), $args);
        }
    }
}