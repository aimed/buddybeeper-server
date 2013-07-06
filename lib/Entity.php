<?php

class Entity extends Model {
    
    
    /**
     * Primary Key Name
     */
    public $key = "id";
    
    
    /**
     * Model loaded
     */
    private $_loaded = false;
    
    
    /**
     * Constructor
     */
    public function __construct ($initial = null) {
    
        $this->attributes[] = $this->key;
        
        if ($initial !== null && !is_array($initial))
        {
            $this->key($initial);
            $initial = null;
        }
        
        parent::__construct($initial);
    }
    
    
    
    
    /**
     * Default hooks
     */
    public function beforeSave () {}
    
    
    
    /**
     * Gets the key value
     *
     * @return Mixed key value
     */
    public function key () {
        if (func_num_args() === 1) $this->{$this->key} = func_get_arg(0);
        return $this->__isset($this->key) ? $this->{$this->key} : null;
    }
    
    
    
    
    /**
     * Loads a model
     */
    public function load () {
        $results = $this->find($this->get($this->key));
        if (!$results) $this->key(null);
        return $results;
    }
    
    
    
    
    /**
     * Override getter
     */
    public function __get ($name) {
        if (($val = parent::__get($name)) !== null) return $val;
        if ($this->hasAttribute($name) && $this->_loaded === false && $this->key())
        {
            $this->load();
            $this->_loaded = true;
            return $this->__get($name);
        }
        
        return null;
    }
    
    
    
    /**
     * Saves a model
     * 
     * @return Bool Success
     */
    public function save () {
        $this->beforeSave();
        if ($this->key()) return $this->update($this->get($this->key));
        else return $this->key($this->insert());
    }




    /**
     * Deletes model
     * 
     * @return Bool Success
     */
    public function deleteThis () {
        return parent::delete($this->get($this->key));
    }
    
    
    
    
    /** 
     * Entitys will return their id as a string
     */
    public function __toString () {
        return ($key = $this->key()) ? $key : "";
    }
}