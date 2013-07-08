<?php

class Model extends Injector {


    /**
     * Object table
     */
    public $table;


    /**
     * Object attributes that are accessible via get
     */
    public $attributes;
    
    
    /**
     * Stores modified attributes
     */
    private $_modified = array();
    
    
    /**
     * Constructor
     */
    public function __construct ($initial = null) {
    
        $this->registerWildcardFunction("findBy");
        $this->registerWildcardFunction("findAllBy");
        
        parent::__construct($initial);
    }
    
    
    /**
     * Sets attributes
     * if the key is an attribute, the result gets passed
     * to the modified array and will be saved to the db 
     *
     * @param String $key
     * @param Mixed $value
     * @return Mixed Value
     */
    public function __set ($key, $value) {
        $value = parent::__set($key, $value);
        if ($this->hasAttribute($key)) $this->_modified[$key] = $value;
        return $value;
    }
    
    
    /**
     * Defaults hooks
     */
    public function beforeFind () {}
    public function afterFind () {}
    public function beforeInsert () {}
    public function afterInsert () {}
    public function beforeUpdate () {}
    public function afterUpdate () {}
    public function beforeDelete () {}
    public function afterDelete () {}
    public function onError () {}


    /**
     * Checks entity has attribute
     *
     * @param String $name
     * @return Bool Is attribute
     */
    public function hasAttribute ($name) {
        return in_array($name, $this->attributes);
    }
    
    
    /**
     * Default attribute getter
     *
     * @return Array Attributes
     */
    public function find ($where) {
        $this->beforeFind();
        
        $fetched = DB::grabOne($this->table, $this->attributes, $where);
        $this->store($fetched);
        
        $this->afterFind();
        
        return $fetched;
    }
    
    
    /**
     * Updates the model
     *
     * @param Array $where
     * @return Integer affected rows
     */
    public function update ($where) {
        if (empty($this->_modified)) return 0;
                
        DB::update($this->table, $this->_modified, $where);
        $this->_modified = array();
        
        $this->afterUpdate();
        return DB::affectedRows();
    }
    
    
    /**
     * Inserts the model
     *
     * @param Array $where
     * @return Mixed insert id
     */
    public function insert () {
        $this->beforeInsert();
        
        $id = DB::insert($this->table, $this->_modified);
        $this->_modified = array();

        if (DB::affectedRows() === 0) $this->onError();         

        $this->afterInsert();
        
        return $id;
    }
    
    
    /**
     * Deletes the object
     *
     * @return Bool Success
     */
    public function delete ($where) {
        $this->beforeDelete();
        
        DB::delete($this->table, $where);
        
        $this->afterDelete();
        
        return DB::affectedRows() > 0;
    }
    
    
    /**
     * Converts camel case to underscore name convention
     * 
     * @param String $s
     * @return String Converted string
     */
    private function camelcaseToUnderscore ($s) {
        return preg_replace_callback(
            "/[A-Z]+/", 
            function ($s) { return "_" . strtolower($s[0]); }, 
            $s
        );
    }
    
    
    /**
     * Finds a model by given arguments
     *
     * @param String $ops
     * @param Array $args
     */    
    public function findBy ($ops, $args) {
        $this->beforeFind();
        
        $ops = $this->camelcaseToUnderscore(lcfirst($ops));
        $query = QB::select()->from($this->table)->where($ops,"=",$args[0]);

        $result = DB::row($query,$query->data);
        $this->store($result);
        
        $this->afterFind();
        
        return $result;
    }
    
    
    /**
     * Finds a model by given arguments
     *
     * @param String $ops
     * @param Array $args
     */    
    public function findAllBy ($ops, $args) {
        $ops = $this->camelcaseToUnderscore(lcfirst($ops));
        $query = QB::select()->from($this->table)->where($ops,"=",$args[0]);
        
        return DB::fetch($query,$query->data);
    }
}