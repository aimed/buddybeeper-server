<?php

class Model extends Injector {

    
    /**
     * Contains the db instance
     */
    protected $db;
    
    
    /**
     * Primary Key
     */
    protected $_primaryKey = "";
    
    
    /**
     * Model loaded
     */
    protected $_modelLoaded = false;
    

    /**
     * List of keys
     */
    protected $_keys;


    /**
     * List of models belonging to this model
     */
    protected $_childModels;


    /**
     * Stores modified attributes
     */
    private $_modified = array();


    /**
     * Model table
     */
    public $table;


    /**
     * Model attributes that are accessible via get
     */
    public $attributes;


    /**
     * Model definition
     */
    public $define;


    /**
     * Constructor
     *
     * @param Array $initial Optional Data the model will be prefilled with
     */
    public function __construct ($initial = null) {
        
    	$this->registerWildcardFunction("findBy");
        $this->registerWildcardFunction("findAllBy");

        // handle model definition
        if (!empty($this->define))
    	{
    		foreach ($this->define as $key => $val) {
    			$this->_handleDefinition($key, $val);
    			$this->attributes[] = is_numeric($key) ? $val : $key;
    		}
    	}
        
    	// set default table name
    	if (empty($this->table)) $this->table = $this->_camelcaseToUnderscore(get_class($this)) . "s";
        
        // set primary
        if (is_string($initial))
        {
            if ($this->_hasPrimaryKey()) $this->primaryKey($initial);
            elseif (count($this->_keys) === 1) $this->{current($this->_keys)} = $initial;
            $initial = null;
        }
    
        parent::__construct($initial);
    }


    /**
     * Handels a model definition
     *
     * @param String $definitionKey
     * @param Mixed $definitionValue
     */
    protected function _handleDefinition ($definitionKey, $definitionValue) {
        
    	if (is_numeric($definitionKey)) return; // is key is numeric, we want to do nothing		
    	if (is_string($definitionValue)) $definitionValue = array($definitionValue);
    	if (!is_array($definitionValue)) return;

    	foreach ($definitionValue as $key => $value) {
    		switch ($value) {
    			case "key": $this->_setKey($definitionKey); break;
    			case "model": $this->_setChildModel($definitionKey, is_numeric($key) ? null : $value); break;
    			case "primaryKey": $this->_setPrimaryKeyName($definitionKey); break;
    		}
    	}
    }


    /**
     * Primary key setter
     *
     * @param String $name
     */
    protected function _setPrimaryKeyName ($name) {
    	$this->_primaryKey = $name;
    	$this->_setKey($this->_primaryKey);
    }
    
    
    /**
     * Gets the primary key and its value
     *
     * @return Array
     */
    protected function _getPrimaryKey () {
        return $this->get($this->_primaryKey);
    }
    
    
    /**
     * Model has a primary key
     *
     * @return Bool
     */
    protected function _hasPrimaryKey () {
        return $this->_primaryKey !== "";
    }
    
    
    /**
     * Gets the key value
     *
     * @param String $key Optional
     * @return Mixed key value
     */
    public function primaryKey () {
        die(func_get_arg(0));
        if (func_num_args() === 1) $this->{$this->_primaryKey} = func_get_arg(0);
        return $this->__isset($this->_primaryKey) ? $this->{$this->_primaryKey} : null;
    }


    /**
     * Key setter
     *
     * @param String $name
     */
    protected function _setKey ($name) {
    	$this->_keys[] = $name;
    }
    
    
    /**
     * Gets all keys and their values
     *
     * @return Array
     */
    protected function _getKeys () {
        return $this->get($this->_keys);
    }
    
    
    /**
     * Check if attribute is a key
     * 
     * @param String $name
     * @return Bool
     */
    protected function _hasKey ($name) {
        return in_array($name, $this);
    }


    /**
     * Model setter
     *
     * @param String $name
     */
    protected function _setChildModel ($column, $model = null) {
    	if (!$model) $model = $this->_underscoreToCamelcase($column);
    	$this->_childModels[$column] = $model;
    }


    /**
     * Checks if attribute is a child model
     *
     * @param String $name
     * @return Bool
     */
    protected function _isChildModel ($name) {
    	return isset($this->_childModels[$name]);
    }
    
    
    /**
     * Gets an array of model identifiers
     * 
     * @return Array
     */
    public function _getModelIdentifier () {
        return $this->_hasPrimaryKey() ? $this->_getPrimaryKey() : $this->_getKeys();
    }
    
    
    /**
     * Checks if the model is identifiable by its keys
     *
     * @return Bool
     */
    public function _isIdentifiable () {
        $identifier = $this->_getModelIdentifier();
        foreach ($identifier as $val) if(empty($val)) return false;
        return true;
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
     * Override getter
     *
     * @param String $name
     * @return Mixed value
     */
    public function __get ($name) {
        if (($val = parent::__get($name)) !== null) return $val;
        if ($this->hasAttribute($name) && $this->_modelLoaded === false && $this->_isIdentifiable())
        {
            $this->load();
            $this->_modelLoaded = true;
            return $this->__get($name);
        }
        
        return null;
    }
    
    
    // some default hooks
    public function beforeFind () {}


    public function afterFind () {}
        
        
    public function beforeInsert () {}  
        
          
    public function afterInsert () {}
        
        
    public function beforeUpdate () {}
        
        
    public function afterUpdate () {}
        
        
    public function beforeDelete () {}
        
        
    public function afterDelete () {}
        
    
    public function beforeSave () {}
            
            
    public function afterSave () {}


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
     * Loads the model from set keys
     * 
     * @return Array query result
     */
    public function load () {
        $identifier = $this->_getModelIdentifier();
        if (empty($identifier)) return null;
        
        $result = $this->find($identifier);
        if (!$result) $this->primaryKey(null);
        
        return $result;
    }
    

    /**
     * Updates the model
     *
     * @param Array $where
     * @return Integer affected rows
     */
    public function update ($where = null) {
        if (empty($this->_modified)) return 0;
        if (empty($where)) $where = $this->_getModelIdentifier;
        
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

        if (DB::affectedRows() === 0) 
        {
            $this->onError();         
        }
        elseif ($this->_hasPrimaryKey()) {
            $this->primaryKey($id);
        }
        
        $this->afterInsert();

        return $id;
    }
    
    
    /**
     * Saves a model
     *
     * If it's identifiable, so the primary or all keys have been set
     * it will be updated and inserted otherwise.
     */
    public function save () {
        $this->beforeSave();
        if ($this->_isIdentifiable()) $this->update();
        else $this->insert();
    }
    

    /**
     * Deletes the object
     *
     * @return Bool Success
     */
    public function delete ($where = null) {
        $this->beforeDelete();
        
        if (empty($where)) $where = $this->_getModelIdentifier;
        
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
    protected function _camelcaseToUnderscore ($s) {
        return preg_replace_callback("/[A-Z]+/", function ($s) { 
    		return "_" . strtolower($s[0]); 
    	}, lcfirst($s));
    }


    /**
     * Converts underscore to camelcase
     *
     * @param String $s
     * @return String
     */
    protected function _underscoreToCamelcase ($s) {
    	return ucfirst(preg_replace_callback("/_[a-z]/", function ($k) {
    		return strtoupper(substr($k[0],1));
    	}, $s));
    }
    
    
    /**
     * Translates a wildcard to a valid query
     *
     * @param String $wildcard
     * @param Array $args
     * @return QueryBuilder $query
     */
    protected function _translateFindWildcard ($wildcard, $args) {
        
        $validArg = array("where", "and", "or");        
        $query    = new QueryBuilder;
        $query->select()->from($this->table);
        
        $wildcard = "where_" . $this->_camelcaseToUnderscore($wildcard);
        $wildcard = explode("_", $wildcard);
        
        $partNumb = count($wildcard);
        for ($i = 0; $i < $partNumb; $i = $i + 2) {
            if (in_array($wildcard[$i], $validArg)) {
                $query->where($wildcard[$i+1], "=", $args[$i/2], $wildcard[$i]);
            }
        }
        
        return $query;
    }
    

    /**
     * Finds a model by given arguments
     *
     * @param String $ops
     * @param Array $args
     */    
    public function findBy ($ops, $args) {
        $this->beforeFind();

        $query = $this->_translateFindWildcard($ops,$args);
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
        $query = $this->_translateFindWildcard($ops,$args);
        return DB::fetch($query,$query->data);
    }
}