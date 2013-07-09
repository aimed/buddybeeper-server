<?php

class Model extends Injector {


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
	 * Primary Key
	 */
	protected $__primaryKey;


	/**
	 * List of keys
	 */
	protected $__keys;


	/**
	 * List of models belonging to this model
	 */
	protected $__childModels;


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
    
	    // handle model definition
	    if (!empty($this->define))
		{
			foreach ($this->define as $key => $val) {
				$this->__handleDefinition($key, $val);
				$this->attributes[] = is_numeric($key) ? $val : $key;
			}
		}
	
		// set default table name
		if (empty($this->table)) $this->table = $this->camelcaseToUnderscore(get_class($this)) . "s";
	
	    parent::__construct($initial);
	}


	/**
	 * Handels a model definition
	 *
	 * @param String $definitionKey
	 * @param Mixed $definitionValue
	 */
	protected function __handleDefinition ($definitionKey, $definitionValue) {

		if (is_numeric($definitionKey)) return; // is key is numeric, we want to do nothing		
		if (is_string($definitionValue)) $definitionValue = array($definitionValue);
		if (!is_array($definitionValue)) return;
	
		foreach ($definitionValue as $key => $value) {
			switch ($definitionValue) {
				case "key": $this->__setKey($definitionKey); break;
				case "model": $this->__setChildModel($definitionKey, is_numeric($key) ? null : $value); break;
				case "primaryKey": $this->__setPrimaryKey($definitionKey); break;
			}
		}
	}


	/**
	 * Primary key setter
	 *
	 * @param String $name
	 */
	protected function __setPrimaryKey ($name) {
		$this->__primaryKey = $name;
	}


	/**
	 * Key setter
	 *
	 * @param String $name
	 */
	protected function __setKey ($name) {
		$this->__keys[] = $name;
	}


	/**
	 * Model setter
	 *
	 * @param String $name
	 */
	protected function __setChildModel ($column, $model = null) {
		if (!$model) $model = $this->underscoreToCamelcase($column);
		$this->__childModels[$column] = $model;
	}


	/**
	 * Checks if attribute is a child model
	 *
	 * @param String $name
	 * @return Bool
	 */
	protected function __isChildModel ($name) {
		return array_key_exists($this->__childModels, $name);
	}


	/**
	 * 
	 */


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
	protected function camelcaseToUnderscore ($s) {
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
	protected function underscoreToCamelcase ($s) {
		return ucfirst(preg_replace_callback("/_[a-z]/", function ($k) {
			return strtoupper(substr($k[0],1));
		}, $s));
	}


	/**
	 * Finds a model by given arguments
	 *
	 * @param String $ops
	 * @param Array $args
	 */    
	public function findBy ($ops, $args) {
	    $this->beforeFind();
    
	    $ops = $this->camelcaseToUnderscore($ops);
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
	    $ops = $this->camelcaseToUnderscore($ops);
	    $query = QB::select()->from($this->table)->where($ops,"=",$args[0]);
    
	    return DB::fetch($query,$query->data);
	}
}